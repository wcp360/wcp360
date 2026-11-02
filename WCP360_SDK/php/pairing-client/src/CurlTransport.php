<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Pairing;
final class CurlTransport implements Transport
{
    public function __construct(private bool $allowLoopback=false){}
    public function post(string $url,#[\SensitiveParameter]array $request):array
    {
        $action=str_ends_with($url,'/claim')?'claim':(str_ends_with($url,'/confirm')?'confirm':null);if(!$action)throw new PairingException('invalid_endpoint');$suffix='/api/v1/verify-license/pairing/'.$action;$base=substr($url,0,-strlen($suffix));if($url!==$base.$suffix)throw new PairingException('invalid_endpoint');Wire::base($base,$this->allowLoopback);
        if(!extension_loaded('curl'))throw new PairingException('curl_required');$raw=json_encode($request,JSON_THROW_ON_ERROR);if(strlen($raw)>8192)throw new PairingException('request_too_large');$body='';$headers=[];$headerBytes=0;$overflow=false;$c=curl_init($url);if(!$c)throw new PairingException('transport_failed');
        try{
            curl_setopt_array($c,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$raw,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Accept: application/json'],CURLOPT_FOLLOWLOCATION=>false,CURLOPT_MAXREDIRS=>0,CURLOPT_CONNECTTIMEOUT=>3,CURLOPT_TIMEOUT=>10,CURLOPT_NOSIGNAL=>true,CURLOPT_SSL_VERIFYPEER=>true,CURLOPT_SSL_VERIFYHOST=>2,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS|($this->allowLoopback?CURLPROTO_HTTP:0),CURLOPT_PROXY=>'',CURLOPT_HTTPAUTH=>0,CURLOPT_USERAGENT=>'WCP360-Pairing-Candidate/0.1.0',
                CURLOPT_WRITEFUNCTION=>static function($curl,string $part)use(&$body,&$overflow):int{if(strlen($body)+strlen($part)>16384){$overflow=true;return 0;}$body.=$part;return strlen($part);},
                CURLOPT_HEADERFUNCTION=>static function($curl,string $line)use(&$headers,&$headerBytes,&$overflow):int{$headerBytes+=strlen($line);if($headerBytes>16384){$overflow=true;return 0;}if(str_starts_with($line,'HTTP/'))$headers=[];elseif(str_contains($line,':')){[$k,$v]=explode(':',$line,2);$headers[strtolower(trim($k))]=trim($v);}return strlen($line);}]);
            if(curl_exec($c)===false){if($overflow)throw new PairingException('response_too_large');throw new PairingException('transport_failed');}$status=(int)curl_getinfo($c,CURLINFO_RESPONSE_CODE);if($status>=300&&$status<400)throw new PairingException('redirect_refused',$status);return ['status'=>$status,'headers'=>$headers,'body'=>$body];
        }finally{curl_close($c);}
    }
}
