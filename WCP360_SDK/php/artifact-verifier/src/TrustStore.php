<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Artifacts;

/** Supplied from trusted local configuration, never from the envelope or a URL. */
final class TrustStore
{
    private array $keys=[];
    public function __construct(array $document)
    {
        StrictJson::keys($document,['schema_version','keys']);
        if($document['schema_version']!=='0.1.0' || !is_array($document['keys']) || !array_is_list($document['keys']) || count($document['keys'])>32) throw new ArtifactException('invalid_trust_store');
        foreach($document['keys'] as $key) {
            if(!is_array($key)) throw new ArtifactException('invalid_trust_key');
            StrictJson::keys($key,['keyid','public_key','publisher','allowed_types','slug_prefix','not_before','not_after','revoked']);
            $public=EnvelopeVerifier::base64($key['public_key'],32);
            if(strlen($public)!==32 || $key['keyid']!==hash('sha256',$public) || isset($this->keys[$key['keyid']]) || !is_string($key['publisher']) || !preg_match('/^[a-z0-9][a-z0-9.-]{0,79}$/D',$key['publisher']) || !is_bool($key['revoked']) || !is_array($key['allowed_types']) || !array_is_list($key['allowed_types']) || !$key['allowed_types'] || count($key['allowed_types'])>4 || !is_string($key['slug_prefix']) || !preg_match('/^[a-z0-9-]{0,80}$/D',$key['slug_prefix'])) throw new ArtifactException('invalid_trust_key');
            foreach($key['allowed_types'] as $type) if(!in_array($type,Statement::TYPES,true)) throw new ArtifactException('invalid_trust_scope');
            if(Statement::date($key['not_after'])<=Statement::date($key['not_before'])) throw new ArtifactException('invalid_trust_validity');
            $this->keys[$key['keyid']]=$key+['decoded_public_key'=>$public];
        }
    }
    public function key(string $keyid,int $now):array
    {
        $key=$this->keys[$keyid] ?? null;
        if(!$key || $key['revoked'] || $now<Statement::date($key['not_before']) || $now>=Statement::date($key['not_after'])) throw new ArtifactException('untrusted_signing_key');
        return $key;
    }
}
