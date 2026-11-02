<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Pairing;
final class Wire
{
    public const VERSION='wcp360-pairing-0.1.0';
    public static function base(string $base,bool $allowLoopback=false):string
    {
        $u=parse_url($base);if(strlen($base)>2048||!$u||preg_match('/[\x00-\x20\x7f\\\\%]/',$base)||isset($u['user'],$u['pass'])||isset($u['user'])||isset($u['pass'])||isset($u['query'])||isset($u['fragment'])||empty($u['host'])||strtolower($u['host'])!==$u['host']||!preg_match('~^(?:/[a-zA-Z0-9_-]+)*$~D',$u['path']??''))throw new PairingException('invalid_hub_origin');
        if(($u['scheme']??'')!=='https'&&!($allowLoopback&&($u['scheme']??'')==='http'&&in_array($u['host'],['127.0.0.1','localhost'],true)))throw new PairingException('https_required');return $base;
    }
    public static function bytes(string $action,array $r):string
    {
        $values=['WCP360-PAIRING',$action,$r['protocol'],$r['audience'],$r['public_key'],$r['client_nonce']];
        if($action==='claim')array_push($values,hash('sha256',$r['code']),$r['core_version'],$r['os'],$r['architecture']);elseif($action==='confirm')array_push($values,$r['instance_id'],$r['issued_at']);else throw new PairingException('invalid_purpose');return implode("\n",$values)."\n";
    }
    public static function request(string $action,string $base,#[\SensitiveParameter]string $secretKey,array $fields,bool $allowLoopback=false):array
    {
        self::base($base,$allowLoopback);if(strlen($secretKey)!==SODIUM_CRYPTO_SIGN_SECRETKEYBYTES)throw new PairingException('invalid_local_key');
        if($action==='claim'){
            if(array_diff(array_keys($fields),['code','core_version','os','architecture'])||count($fields)!==4||!is_string($fields['code']??null)||!preg_match('/^wcp-pair1\.[a-f0-9]{32}\.[a-f0-9]{64}$/D',$fields['code']))throw new PairingException('invalid_pairing_input');
            if(!is_string($fields['core_version'])||strlen($fields['core_version'])>96||!preg_match('/^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(?:-[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*)?(?:\+[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*)?$/D',$fields['core_version'])||!in_array($fields['os'],['unknown','debian-12','debian-13','ubuntu-22.04','ubuntu-24.04','ubuntu-26.04'],true)||!in_array($fields['architecture'],['unknown','amd64','arm64'],true))throw new PairingException('invalid_pairing_input');
        }elseif($action==='confirm'){
            if(array_keys($fields)!==['instance_id']||!is_string($fields['instance_id'])||!preg_match('/^[a-f0-9]{32}$/D',$fields['instance_id']))throw new PairingException('invalid_pairing_input');$fields['issued_at']=gmdate('Y-m-d\TH:i:s\Z');
        }else throw new PairingException('invalid_purpose');
        $r=['protocol'=>self::VERSION,'audience'=>$base,'public_key'=>bin2hex(sodium_crypto_sign_publickey_from_secretkey($secretKey)),'client_nonce'=>bin2hex(random_bytes(32))]+$fields;$r['signature']=bin2hex(sodium_crypto_sign_detached(self::bytes($action,$r),$secretKey));return $r;
    }
}
