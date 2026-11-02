<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Pairing;
final class Client
{
    public function __construct(private Transport $transport){}
    public function send(string $action,#[\SensitiveParameter]array $request):array
    {
        if(!in_array($action,['claim','confirm'],true))throw new PairingException('invalid_purpose');$r=$this->transport->post($request['audience'].'/api/v1/verify-license/pairing/'.$action,$request);$status=$r['status'];if($status!==200)throw new PairingException($status===429?'rate_limited':'hub_refused',$status);
        if(strlen($r['body'])>16384||!str_starts_with(strtolower($r['headers']['content-type']??''),'application/json')||!str_contains(strtolower($r['headers']['cache-control']??''),'no-store'))throw new PairingException('invalid_receipt');
        try{$json=json_decode($r['body'],true,4,JSON_THROW_ON_ERROR);}catch(\JsonException){throw new PairingException('invalid_receipt');}
        preg_match_all('/"((?:[^"\\\\]|\\\\.)*)"\s*:/s',$r['body'],$matches);$seen=[];foreach($matches[1] as $key){$key=json_decode('"'.$key.'"',true,4,JSON_THROW_ON_ERROR);if(isset($seen[$key]))throw new PairingException('invalid_receipt');$seen[$key]=true;}
        if(!is_array($json)||array_keys($json)!==['data']||!is_array($json['data']))throw new PairingException('invalid_receipt');$d=$json['data'];$keys=array_keys($d);sort($keys);if($keys!==['audience','instance_id','paired_at','protocol','public_key_sha256','replayed','request_nonce','status'])throw new PairingException('invalid_receipt');
        if($d['protocol']!==Wire::VERSION||$d['audience']!==$request['audience']||$d['request_nonce']!==$request['client_nonce']||$d['public_key_sha256']!==hash('sha256',hex2bin($request['public_key']))||$d['status']!=='paired'||!is_bool($d['replayed'])||!is_string($d['instance_id'])||!preg_match('/^[a-f0-9]{32}$/D',$d['instance_id'])||!is_string($d['paired_at'])||!preg_match('/^20[0-9]{2}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}Z$/D',$d['paired_at']))throw new PairingException('invalid_receipt');
        $date=\DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s\Z',$d['paired_at'],new \DateTimeZone('UTC'));if(!$date||$date->format('Y-m-d\TH:i:s\Z')!==$d['paired_at'])throw new PairingException('invalid_receipt');if($action==='confirm'&&$d['instance_id']!==$request['instance_id'])throw new PairingException('invalid_receipt');return $d;
    }
}
