<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Artifacts;

final class Statement
{
    public const PAYLOAD_TYPE='application/vnd.wcp360.artifact-staging.v1+json';
    public const MAX_ARCHIVE=134217728;
    public const MAX_LIFETIME=604800;
    public const TYPES=['plugin','module','app','core'];
    private const SEMVER='/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-((?:0|[1-9]\d*|\d*[A-Za-z-][0-9A-Za-z-]*)(?:\.(?:0|[1-9]\d*|\d*[A-Za-z-][0-9A-Za-z-]*))*))?(?:\+[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*)?$/D';
    public static function identity(array $value):array
    {
        StrictJson::keys($value,['type','slug','version']);
        if(!in_array($value['type'],self::TYPES,true) || !is_string($value['slug']) || strlen($value['slug'])>80 || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D',$value['slug']) || !is_string($value['version']) || strlen($value['version'])>96 || !preg_match(self::SEMVER,$value['version'])) throw new ArtifactException('invalid_identity');
        return $value;
    }
    public static function validate(array $value):array
    {
        StrictJson::keys($value,['schema_version','purpose','package','artifact','issued_at','expires_at']);
        if($value['schema_version']!=='0.1.0' || $value['purpose']!=='artifact-staging' || !is_array($value['package']) || !is_array($value['artifact'])) throw new ArtifactException('invalid_statement');
        $p=$value['package'];StrictJson::keys($p,['type','slug','version','publisher']);
        self::identity(['type'=>$p['type'],'slug'=>$p['slug'],'version'=>$p['version']]);
        if(!is_string($p['publisher']) || !preg_match('/^[a-z0-9][a-z0-9.-]{0,79}$/D',$p['publisher'])) throw new ArtifactException('invalid_publisher');
        $a=$value['artifact'];StrictJson::keys($a,['media_type','sha256','size']);
        if($a['media_type']!=='application/zip' || !is_string($a['sha256']) || !preg_match('/^[a-f0-9]{64}$/D',$a['sha256']) || !is_int($a['size']) || $a['size']<1 || $a['size']>self::MAX_ARCHIVE) throw new ArtifactException('invalid_artifact_metadata');
        $issued=self::date($value['issued_at']);$expires=self::date($value['expires_at']);
        if($expires<=$issued || $expires-$issued>self::MAX_LIFETIME) throw new ArtifactException('invalid_validity_window');
        return $value;
    }
    public static function date(mixed $value):int
    {
        if(!is_string($value) || strlen($value)!==20) throw new ArtifactException('invalid_utc_date');
        $date=\DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s\Z',$value,new \DateTimeZone('UTC'));
        if(!$date || $date->format('Y-m-d\TH:i:s\Z')!==$value) throw new ArtifactException('invalid_utc_date');
        return $date->getTimestamp();
    }
    public static function identityHash(array $identity):string
    {
        self::identity($identity);
        return hash('sha256',$identity['type'].'/'.$identity['slug'].'/'.$identity['version']);
    }
}
