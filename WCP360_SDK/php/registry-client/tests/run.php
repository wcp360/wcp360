<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__).'/autoload.php';
use Wcp360\Sdk\Registry\{RegistryClient,Transport,TransportResponse,ClientException,CurlTransport};
final class FakeTransport implements Transport {
    public array $calls=[];
    public function __construct(public array $responses) {}
    public function get(string $url,array $headers=[]): TransportResponse {
        $this->calls[]=[$url,$headers];
        return array_shift($this->responses) ?? throw new RuntimeException('No fixture');
    }
}
$checks=0;
function check(bool $ok,string $why): void { global $checks; $checks++; if (!$ok) throw new RuntimeException($why); }
function fails(callable $f,string $code): void { try {$f();} catch(ClientException $e) {check($e->errorCode===$code,'Unexpected error '.$e->errorCode);return;} throw new RuntimeException('Expected '.$code); }
$provenance=json_decode(file_get_contents(dirname(__DIR__).'/contracts/PROVENANCE.json'),true,32,JSON_THROW_ON_ERROR);
foreach($provenance['sha256'] as $file=>$hash) check(strtoupper(hash_file('sha256',dirname(__DIR__).'/contracts/'.$file))===$hash,'Pinned contract '.$file);
$data=['service'=>'wcp360-registry','features'=>['catalog'=>true,'versions'=>true,'update_checks'=>true,'installation'=>false,'downloads'=>false,'license_validation'=>false,'billing'=>false]];
$envelope=['schema_version'=>'0.1.0','contract_status'=>'candidate','data'=>$data];
$body=json_encode($envelope,JSON_THROW_ON_ERROR);
$etag='"'.hash('sha256',$body).'"';
function response(string $body,array $headers=[]): TransportResponse {return new TransportResponse(200,$headers+['Content-Type'=>'application/json; charset=utf-8','Cache-Control'=>'public, max-age=60'], $body);}
function client(Transport $t): RegistryClient {return new RegistryClient('https://registry.example.invalid/api/v1/wcp360-registry',$t,true);}
$fake=new FakeTransport([response($body,['ETag'=>$etag]),new TransportResponse(304,[],'')]);
$c=client($fake);
check($c->status()===$data,'Valid status');
check($c->status()===$data && $fake->calls[1][1]===['If-None-Match'=>$etag],'ETag revalidation');
$weak=new FakeTransport([response($body,['ETag'=>'W/'.$etag]),new TransportResponse(304,[],'')]);
$wc=client($weak);$wc->status();check($wc->status()===$data && $weak->calls[1][1]===['If-None-Match'=>'W/'.$etag],'Weak ETag revalidation');
fails(fn()=>new RegistryClient('https://example.invalid/',new FakeTransport([])),'candidate_opt_in_required');
foreach (['http://example.invalid/','ftp://example.invalid/'] as $url) fails(fn()=>new RegistryClient($url,new FakeTransport([]),true),'https_required');
foreach (['https://u:p@example.invalid/','https://example.invalid/?secret=1','https://example.invalid/#x',"https://example.invalid/\r\nx",'https://example.invalid/\\x'] as $url) fails(fn()=>new RegistryClient($url,new FakeTransport([]),true),'invalid_endpoint');
check((new RegistryClient('http://localhost/grav/api/v1/wcp360-registry',new FakeTransport([response($body)]),true,true))->status()===$data,'Explicit loopback HTTP');
fails(fn()=>new RegistryClient('http://localhost.evil.invalid/',new FakeTransport([]),true,true),'https_required');
fails(fn()=>client(new FakeTransport([new TransportResponse(304,[],'')]))->status(),'unexpected_not_modified');
fails(fn()=>client(new FakeTransport([new TransportResponse(429,[],'SECRET')]))->status(),'rate_limited');
fails(fn()=>client(new FakeTransport([new TransportResponse(302,['Location'=>'https://evil.invalid/'],'')]))->status(),'http_error');
fails(fn()=>client(new FakeTransport([response('{bad')]))->status(),'invalid_json');
fails(fn()=>client(new FakeTransport([response($body,['Content-Type'=>'text/html'])]))->status(),'invalid_content_type');
fails(fn()=>client(new FakeTransport([response(str_repeat('x',CurlTransport::MAX_BODY+1))]))->status(),'response_too_large');
foreach ([['schema_version'=>'1.0.0'],['contract_status'=>'accepted'],['extra'=>'SECRET'],['data'=>array_replace($data,['features'=>array_replace($data['features'],['installation'=>true])])]] as $change) fails(fn()=>client(new FakeTransport([response(json_encode(array_replace($envelope,$change)))]))->status(),'contract_mismatch');
fails(fn()=>client(new FakeTransport([]))->package('plugin','../../config'),'invalid_identity');
fails(fn()=>client(new FakeTransport([]))->catalog('core'),'invalid_type');
fails(fn()=>client(new FakeTransport([]))->catalog('plugin',['page'=>['1']]),'invalid_query');
fails(fn()=>client(new FakeTransport([]))->catalog('plugin',['token'=>'secret']),'invalid_query');
fails(fn()=>client(new FakeTransport([]))->updates(['type'=>'plugin']),'incomplete_environment');
$noCache=new FakeTransport([response($body,['ETag'=>$etag,'Cache-Control'=>'no-store']),response($body)]);
$nc=client($noCache);$nc->status();$nc->status();check($noCache->calls[1][1]===[],'No private/no-store cache');
$crlf=new FakeTransport([response($body,['ETag'=>$etag."\r\nInjected: yes"]),response($body)]);
$cc=client($crlf);$cc->status();$cc->status();check($crlf->calls[1][1]===[],'No header injection');
try {client(new FakeTransport([new TransportResponse(500,[],'PASSWORD=SECRET')]))->status();} catch(ClientException $e) {check(!str_contains($e->getMessage(),'SECRET'),'Redacted error');}
echo json_encode(['suite'=>'registry-client','checks'=>$checks,'result'=>'passed','php'=>PHP_VERSION],JSON_PRETTY_PRINT)."\n";
