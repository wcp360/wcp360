<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Pairing;
/** An operator-owned private directory; hostile shared filesystems are not supported. */
final class Vault
{
    private string $directory;
    private $lock=null;
    public function __construct(string $directory,array $webRoots)
    {
        if(str_contains($directory,'://')||str_contains($directory,"\0")||!preg_match('~^(?:[A-Za-z]:[/\\\\]|/)~',$directory))throw new PairingException('private_directory_required');$directory=rtrim(str_replace('\\','/',$directory),'/');$parent=realpath(dirname($directory));if(!$parent||is_link($directory)||!preg_match('/^[a-zA-Z0-9_-]{1,80}$/D',basename($directory))||self::canon($parent)!==self::canon(dirname($directory)))throw new PairingException('unsafe_private_directory');
        foreach($webRoots as $root){$real=realpath($root);if(!$real)throw new PairingException('invalid_webroot');$a=self::canon($directory);$b=self::canon($real);if($a===$b||str_starts_with($a,$b.'/')||str_starts_with($b,$a.'/'))throw new PairingException('state_overlaps_webroot');}
        $this->checkMode($parent,false);if(!is_dir($directory)&&!mkdir($directory,0700))throw new PairingException('private_directory_unavailable');$this->checkMode($directory,true);$this->directory=$directory;
        $file=$directory.'/state.lock';if(file_exists($file))$this->checkFile($file);$this->lock=fopen($file,'c+b');if(!$this->lock)throw new PairingException('state_lock_failed');@chmod($file,0600);if(!flock($this->lock,LOCK_EX|LOCK_NB))throw new PairingException('state_busy');$this->checkFile($file);
    }
    private static function canon(string $path):string{$p=rtrim(str_replace('\\','/',$path),'/');return PHP_OS_FAMILY==='Windows'?strtolower($p):$p;}
    private function checkMode(string $path,bool $private):void
    {
        if(is_link($path))throw new PairingException('unsafe_private_directory');
        if(PHP_OS_FAMILY!=='Windows'){clearstatcache(true,$path);$s=lstat($path);if(!$s||($s['mode']&($private?0077:0022))!==0||(function_exists('posix_geteuid')&&!in_array($s['uid'],[0,posix_geteuid()],true)))throw new PairingException('unsafe_private_permissions');}
    }
    private function checkFile(string $file):void
    {
        clearstatcache(true,$file);$s=lstat($file);if(!$s||is_link($file)||!is_file($file)||$s['nlink']!==1||$s['size']>16384)throw new PairingException('unsafe_state_file');$this->checkMode($file,true);
    }
    public function read():?array
    {
        $file=$this->directory.'/identity.json';if(!file_exists($file))return null;$this->checkFile($file);try{$r=json_decode(file_get_contents($file),true,8,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new PairingException('invalid_local_state');}
        if(!is_array($r)||!is_string($r['secret_key']??null)||!preg_match('/^[a-f0-9]{128}$/D',$r['secret_key'])||!in_array($r['status']??'', ['pending','paired'],true))throw new PairingException('invalid_local_state');return $r;
    }
    public function save(#[\SensitiveParameter]array $state):void
    {
        $this->checkMode($this->directory,true);$file=$this->directory.'/identity.json';if(file_exists($file))$this->checkFile($file);$raw=json_encode($state,JSON_THROW_ON_ERROR);if(strlen($raw)>8192)throw new PairingException('local_state_too_large');$temp=$this->directory.'/state-'.bin2hex(random_bytes(8)).'.tmp';$h=fopen($temp,'xb');if(!$h)throw new PairingException('state_write_failed');@chmod($temp,0600);
        try{if(fwrite($h,$raw)!==strlen($raw)||!fflush($h))throw new PairingException('state_write_failed');if(function_exists('fsync')&&!fsync($h))throw new PairingException('state_write_failed');}finally{fclose($h);}
        $this->checkFile($temp);if(!rename($temp,$file))throw new PairingException('state_write_failed');@chmod($file,0600);$this->checkFile($file);
    }
    public function __destruct(){if(is_resource($this->lock)){flock($this->lock,LOCK_UN);fclose($this->lock);}}
}
