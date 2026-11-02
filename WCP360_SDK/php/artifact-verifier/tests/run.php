<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/autoload.php';
use Wcp360\Sdk\Artifacts\{ArtifactException,StrictJson,Statement,TrustStore,EnvelopeVerifier,ZipInspector,ArtifactVerifier};
$checks=0;
function check(bool $ok,string $why):void{global $checks;$checks++;if(!$ok)throw new RuntimeException($why);}
function rejected(callable $call,?string $code=null):void{try{$call();}catch(ArtifactException $e){check($code===null||$e->errorCode===$code,'Unexpected code: '.$e->errorCode);return;}throw new RuntimeException('Expected rejection');}
function zipFile(string $path,array $entries):void{$z=new ZipArchive();if($z->open($path,ZipArchive::CREATE|ZipArchive::OVERWRITE)!==true)throw new RuntimeException('Fixture ZIP creation failed');foreach($entries as $name=>$data){if(str_ends_with($name,'/'))$z->addEmptyDir(rtrim($name,'/'));else $z->addFromString($name,$data);}if(!$z->close())throw new RuntimeException('Fixture ZIP write failed');}
$folder=($argv[1] ?? sys_get_temp_dir()).'/wcp360-artifact-tests-'.bin2hex(random_bytes(8));mkdir($folder,0700,true);
$pair=sodium_crypto_sign_keypair();$secret=sodium_crypto_sign_secretkey($pair);$public=sodium_crypto_sign_publickey($pair);$keyid=hash('sha256',$public);$now=1793534400;
$identity=['type'=>'plugin','slug'=>'fixture-package','version'=>'1.0.0'];
$key=['keyid'=>$keyid,'public_key'=>base64_encode($public),'publisher'=>'wcp360','allowed_types'=>['plugin'],'slug_prefix'=>'fixture-','not_before'=>'2026-01-01T00:00:00Z','not_after'=>'2027-01-01T00:00:00Z','revoked'=>false];
function signed(string $payload,string $secret,string $keyid):string{return StrictJson::encode(['payloadType'=>Statement::PAYLOAD_TYPE,'payload'=>base64_encode($payload),'signatures'=>[['keyid'=>$keyid,'sig'=>base64_encode(sodium_crypto_sign_detached(EnvelopeVerifier::pae(Statement::PAYLOAD_TYPE,$payload),$secret))]]]);}
try{
 $archive=$folder.'/valid.zip';zipFile($archive,['src/'=>'','src/main.php'=>'<?php /* fixture, never executed */','README.txt'=>'Test only']);
 $statement=['schema_version'=>'0.1.0','purpose'=>'artifact-staging','package'=>$identity+['publisher'=>'wcp360'],'artifact'=>['media_type'=>'application/zip','sha256'=>hash_file('sha256',$archive),'size'=>filesize($archive)],'issued_at'=>'2026-11-01T08:00:00Z','expires_at'=>'2026-11-02T08:00:00Z'];
 $payload=StrictJson::encode($statement);$envelope=signed($payload,$secret,$keyid);
 $trust=new TrustStore(['schema_version'=>'0.1.0','keys'=>[$key]]);
 $verifier=new EnvelopeVerifier($trust,$now);$fileVerifier=new ArtifactVerifier($verifier);
 check(EnvelopeVerifier::pae('http://example.com/HelloWorld','hello world')==='DSSEv1 29 http://example.com/HelloWorld 11 hello world','Official DSSE PAE vector');
 check(EnvelopeVerifier::base64('YQ==',1)==='a' && EnvelopeVerifier::base64('YQ',1)==='a','Padded and unpadded base64');
 foreach(['YQ=','YQ===','YR==','YWJj=','a+_-'] as $encoded) rejected(fn()=>EnvelopeVerifier::base64($encoded,10),'invalid_base64');
 check($verifier->verify($envelope,$identity)['statement']===$statement,'Trusted signature');
 $verified=$fileVerifier->verify($archive,$envelope,$identity);check($verified['zip']['files']===2,'Signature, digest and ZIP verified');
 check($verified['statement_id']===hash('sha256',$payload),'Verified byte identity');
 $url=json_decode($envelope,true);$url['payload']=rtrim(strtr($url['payload'],'+/','-_'),'=');$url['signatures'][0]['sig']=rtrim(strtr($url['signatures'][0]['sig'],'+/','-_'),'=');
 check($verifier->verify(StrictJson::encode($url),$identity)['statement']===$statement,'DSSE URL-safe base64');
 $extra=json_decode($envelope,true);$extra['transport_hint']='ignored';check($verifier->verify(StrictJson::encode($extra),$identity)['statement']===$statement,'Unsigned extension is not trusted or consumed');
 rejected(fn()=>StrictJson::object('{"a":1,"a":2}'),'duplicate_json_key');
 rejected(fn()=>StrictJson::object('{"a":1,"\\u0061":2}'),'duplicate_json_key');
 rejected(fn()=>StrictJson::object('{"nested":[{"x":1,"x":2}]}'),'duplicate_json_key');
 check(StrictJson::object(StrictJson::encode(['a'=>'escaped " quote','list'=>[1,true,null,['b'=>2]]]))['list'][1]===true,'Nested JSON scan');
 rejected(fn()=>StrictJson::object(str_repeat(' ',32769)),'metadata_too_large');
 rejected(fn()=>StrictJson::object('[]'),'invalid_json_object');
 $changed=json_decode($envelope,true);$changed['payload']=base64_encode(str_replace('fixture-package','fixture-modified',$payload));rejected(fn()=>$verifier->verify(StrictJson::encode($changed),$identity),'invalid_signature');
 $changed=json_decode($envelope,true);$changed['payloadType']='application/json';rejected(fn()=>$verifier->verify(StrictJson::encode($changed),$identity),'invalid_envelope');
 rejected(fn()=>$verifier->verify($envelope,array_replace($identity,['version'=>'2.0.0'])),'identity_mismatch');
 rejected(fn()=>(new EnvelopeVerifier(new TrustStore(['schema_version'=>'0.1.0','keys'=>[]]),$now))->verify($envelope,$identity),'untrusted_signing_key');
 foreach([['revoked'=>true],['not_after'=>'2026-10-01T00:00:00Z'],['not_before'=>'2026-12-01T00:00:00Z']] as $change){$t=new TrustStore(['schema_version'=>'0.1.0','keys'=>[array_replace($key,$change)]]);rejected(fn()=>(new EnvelopeVerifier($t,$now))->verify($envelope,$identity),'untrusted_signing_key');}
 foreach([['publisher'=>'other'],['allowed_types'=>['core']],['slug_prefix'=>'other-']] as $change){$t=new TrustStore(['schema_version'=>'0.1.0','keys'=>[array_replace($key,$change)]]);rejected(fn()=>(new EnvelopeVerifier($t,$now))->verify($envelope,$identity),'publisher_scope_mismatch');}
 foreach([['issued_at'=>'2026-11-01T20:00:00Z'],['expires_at'=>'2026-11-01T08:01:00Z']] as $change){$s=array_replace($statement,$change);rejected(fn()=>$verifier->verify(signed(StrictJson::encode($s),$secret,$keyid),$identity),'statement_not_current');}
 $s=array_replace($statement,['expires_at'=>'2026-12-01T08:00:00Z']);rejected(fn()=>$verifier->verify(signed(StrictJson::encode($s),$secret,$keyid),$identity),'invalid_validity_window');
 $s=$statement;$s['artifact']['size']++;rejected(fn()=>$fileVerifier->verify($archive,signed(StrictJson::encode($s),$secret,$keyid),$identity),'artifact_size_mismatch');
 $s=$statement;$s['artifact']['sha256']=str_repeat('0',64);rejected(fn()=>$fileVerifier->verify($archive,signed(StrictJson::encode($s),$secret,$keyid),$identity),'artifact_digest_mismatch');
 $duplicate=str_replace('"purpose":"artifact-staging"','"purpose":"artifact-staging","purpose":"artifact-staging"',$payload);rejected(fn()=>$verifier->verify(signed($duplicate,$secret,$keyid),$identity),'duplicate_json_key');
 rejected(fn()=>(new ArtifactVerifier($verifier))->verify('php://memory',$envelope,$identity),'local_archive_required');
 foreach(['../escape','/absolute','C:/drive','bad\\name','a//b','a/./b','a/../b','a:stream','CON.txt','aux','trailing.','trailing ','utf8-é.txt'] as $name){zipFile($folder.'/bad.zip',[$name=>'x']);rejected(fn()=>(new ZipInspector())->inspect($folder.'/bad.zip'),'unsafe_zip_path');}
 foreach([['A.txt'=>'a','a.txt'=>'b'],['parent/child'=>'x','parent'=>'file'],['dir/a'=>'x','DIR/b'=>'y']] as $entries){zipFile($folder.'/bad.zip',$entries);rejected(fn()=>(new ZipInspector())->inspect($folder.'/bad.zip'),'zip_path_collision');}
 zipFile($folder.'/bad.zip',['link'=>'target']);$z=new ZipArchive();$z->open($folder.'/bad.zip');$z->setExternalAttributesIndex(0,ZipArchive::OPSYS_UNIX,0120777<<16);$z->close();rejected(fn()=>(new ZipInspector())->inspect($folder.'/bad.zip'),'unsafe_zip_entry_type');
 zipFile($folder.'/bad.zip',['script'=>'x']);$z=new ZipArchive();$z->open($folder.'/bad.zip');$z->setExternalAttributesIndex(0,ZipArchive::OPSYS_UNIX,0104755<<16);$z->close();rejected(fn()=>(new ZipInspector())->inspect($folder.'/bad.zip'),'unsafe_zip_entry_type');
 rejected(fn()=>(new ZipInspector(maxEntries:1))->inspect($archive),'zip_entry_limit');
 rejected(fn()=>(new ZipInspector(maxExpanded:4))->inspect($archive),'zip_expanded_limit');
 rejected(fn()=>(new ZipInspector(maxEntry:4))->inspect($archive),'zip_entry_policy');
 zipFile($folder.'/bomb.zip',['bomb.txt'=>str_repeat('a',200000)]);rejected(fn()=>(new ZipInspector())->inspect($folder.'/bomb.zip'),'zip_entry_policy');
 file_put_contents($folder.'/bad.zip',file_get_contents($archive).'trailer');rejected(fn()=>(new ZipInspector())->inspect($folder.'/bad.zip'),'unsupported_zip_container');
 file_put_contents($folder.'/bad.zip','prefix'.file_get_contents($archive));rejected(fn()=>(new ZipInspector())->inspect($folder.'/bad.zip'),'unsupported_zip_container');
 check(!is_dir($folder.'/src')&&!is_file($folder.'/README.txt'),'No archive extraction');
}finally{
 sodium_memzero($secret);sodium_memzero($pair);
 foreach(['valid.zip','bad.zip','bomb.zip'] as $file)if(is_file($folder.'/'.$file))unlink($folder.'/'.$file);
 rmdir($folder);
}
echo json_encode(['suite'=>'artifact-verifier','checks'=>$checks,'result'=>'passed','php'=>PHP_VERSION],JSON_PRETTY_PRINT)."\n";
