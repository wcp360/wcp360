<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Artifacts;

/** Bounded JSON decoder which rejects duplicate keys, including escaped aliases. */
final class StrictJson
{
    private int $position=0;
    private function __construct(private readonly string $json) {}
    public static function object(string $json,int $maxBytes=32768):array
    {
        if(strlen($json)>$maxBytes) throw new ArtifactException('metadata_too_large');
        try { $value=json_decode($json,true,24,JSON_THROW_ON_ERROR); }
        catch(\JsonException) { throw new ArtifactException('invalid_json'); }
        if(!is_array($value) || !str_starts_with(ltrim($json),'{')) throw new ArtifactException('invalid_json_object');
        (new self($json))->value();
        return $value;
    }
    public static function encode(array $value):string
    {
        return json_encode($value,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
    }
    public static function keys(array $value,array $expected):void
    {
        $keys=array_keys($value);sort($keys);sort($expected);
        if($keys!==$expected) throw new ArtifactException('unexpected_fields');
    }
    private function whitespace():void
    {
        while(isset($this->json[$this->position]) && str_contains(" \n\r\t",$this->json[$this->position])) $this->position++;
    }
    private function string():string
    {
        $start=$this->position++;
        while($this->json[$this->position]!=='"') {
            $this->position += $this->json[$this->position]==='\\' ? 2 : 1;
        }
        $this->position++;
        return json_decode(substr($this->json,$start,$this->position-$start),true,24,JSON_THROW_ON_ERROR);
    }
    private function value():void
    {
        $this->whitespace();$char=$this->json[$this->position];
        if($char==='{') {
            $seen=[];$this->position++;$this->whitespace();
            if($this->json[$this->position]==='}') {$this->position++;return;}
            while(true) {
                $this->whitespace();$key=$this->string();
                if(array_key_exists($key,$seen)) throw new ArtifactException('duplicate_json_key');
                $seen[$key]=true;$this->whitespace();$this->position++;$this->value();$this->whitespace();
                if($this->json[$this->position++]==='}') return;
            }
        }
        if($char==='[') {
            $this->position++;$this->whitespace();
            if($this->json[$this->position]===']') {$this->position++;return;}
            while(true) {$this->value();$this->whitespace();if($this->json[$this->position++]===']') return;}
        }
        if($char==='"') {$this->string();return;}
        while(isset($this->json[$this->position]) && !str_contains(" \n\r\t,]}",$this->json[$this->position])) $this->position++;
    }
}
