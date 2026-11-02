<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Artifacts;

final class EnvelopeVerifier
{
    public function __construct(private readonly TrustStore $trust,private readonly ?int $clock=null) {}
    public static function pae(string $type,string $payload):string { return 'DSSEv1 '.strlen($type).' '.$type.' '.strlen($payload).' '.$payload; }
    public static function base64(mixed $input,int $maxBytes):string
    {
        if(!is_string($input) || strlen($input)>4*(int)ceil($maxBytes/3)+4 || !(preg_match('~^[A-Za-z0-9+/]*={0,2}$~D',$input) || preg_match('~^[A-Za-z0-9_-]*={0,2}$~D',$input))) throw new ArtifactException('invalid_base64');
        $normalized=rtrim(strtr($input,'-_','+/'),'=');
        if(strlen($normalized)%4===1) throw new ArtifactException('invalid_base64');
        if(str_contains($input,'=') && (strlen($input)%4!==0 || strlen($input)-strlen($normalized)!==(4-strlen($normalized)%4)%4)) throw new ArtifactException('invalid_base64');
        $bytes=base64_decode($normalized.str_repeat('=',(4-strlen($normalized)%4)%4),true);
        if($bytes===false || strlen($bytes)>$maxBytes || rtrim(base64_encode($bytes),'=')!==$normalized) throw new ArtifactException('invalid_base64');
        return $bytes;
    }
    public function verify(string $json,array $expected):array
    {
        if(!extension_loaded('sodium')) throw new ArtifactException('sodium_required');
        Statement::identity($expected);
        $envelope=StrictJson::object($json);
        if(($envelope['payloadType'] ?? null)!==Statement::PAYLOAD_TYPE || !isset($envelope['payload'],$envelope['signatures']) || !is_array($envelope['signatures']) || !array_is_list($envelope['signatures']) || count($envelope['signatures'])!==1) throw new ArtifactException('invalid_envelope');
        $entry=$envelope['signatures'][0];
        if(!is_array($entry) || !isset($entry['keyid'],$entry['sig']) || !is_string($entry['keyid']) || !preg_match('/^[a-f0-9]{64}$/D',$entry['keyid'])) throw new ArtifactException('invalid_signature_entry');
        $payload=self::base64($envelope['payload'],16384);$signature=self::base64($entry['sig'],64);
        if(strlen($signature)!==64) throw new ArtifactException('invalid_signature');
        $now=$this->clock ?? time();$key=$this->trust->key($entry['keyid'],$now);
        if(!sodium_crypto_sign_verify_detached($signature,self::pae(Statement::PAYLOAD_TYPE,$payload),$key['decoded_public_key'])) throw new ArtifactException('invalid_signature');
        // Parse these exact verified bytes once, never re-read payload from the envelope.
        $statement=Statement::validate(StrictJson::object($payload,16384));
        $p=$statement['package'];
        if($p['type']!==$expected['type'] || $p['slug']!==$expected['slug'] || $p['version']!==$expected['version']) throw new ArtifactException('identity_mismatch');
        if($p['publisher']!==$key['publisher'] || !in_array($p['type'],$key['allowed_types'],true) || !str_starts_with($p['slug'],$key['slug_prefix'])) throw new ArtifactException('publisher_scope_mismatch');
        $issued=Statement::date($statement['issued_at']);$expires=Statement::date($statement['expires_at']);
        if($issued>$now || $expires<=$now || $issued<Statement::date($key['not_before']) || $expires>Statement::date($key['not_after'])) throw new ArtifactException('statement_not_current');
        return ['statement'=>$statement,'keyid'=>$entry['keyid'],'statement_id'=>hash('sha256',$payload)];
    }
}
