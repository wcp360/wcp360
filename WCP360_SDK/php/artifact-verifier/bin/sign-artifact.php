<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require dirname(__DIR__).'/autoload.php';
use Wcp360\Sdk\Artifacts\{ArtifactException,EnvelopeVerifier,Statement,StrictJson,ZipInspector};

// Offline build tool. Secret key arrives on stdin only; never argv, web, disk or output.
$secret=null;$input=null;$exitCode=0;
try {
    if($argc!==4) throw new ArtifactException('usage_archive_descriptor_new_envelope');
    if(!extension_loaded('sodium')) throw new ArtifactException('sodium_required');
    [$script,$archive,$descriptor,$output]=$argv;
    foreach([$archive,$descriptor,$output] as $path) if(str_contains($path,'://') || str_contains($path,"\0") || is_link($path)) throw new ArtifactException('local_file_required');
    if(!is_file($archive) || !is_file($descriptor) || filesize($descriptor)>4096) throw new ArtifactException('invalid_signing_input');
    $json=file_get_contents($descriptor,false,null,0,4097);
    if($json===false) throw new ArtifactException('invalid_signing_input');
    $d=StrictJson::object($json,4096);StrictJson::keys($d,['package','issued_at','expires_at']);
    $zip=(new ZipInspector())->inspect($archive);
    clearstatcache(true,$archive);$size=filesize($archive);$digest=hash_file('sha256',$archive);
    $statement=Statement::validate(['schema_version'=>'0.1.0','purpose'=>'artifact-staging','package'=>$d['package'],
        'artifact'=>['media_type'=>'application/zip','sha256'=>$digest,'size'=>$size],'issued_at'=>$d['issued_at'],'expires_at'=>$d['expires_at']]);
    $input=stream_get_contents(STDIN,257);
    if($input===false || strlen($input)>256) throw new ArtifactException('invalid_stdin_key');
    $secret=EnvelopeVerifier::base64(trim($input),64);sodium_memzero($input);
    if(strlen($secret)!==SODIUM_CRYPTO_SIGN_SECRETKEYBYTES) throw new ArtifactException('invalid_stdin_key');
    $public=sodium_crypto_sign_publickey_from_secretkey($secret);$keyid=hash('sha256',$public);
    $payload=StrictJson::encode($statement);$pae=EnvelopeVerifier::pae(Statement::PAYLOAD_TYPE,$payload);
    $sig=sodium_crypto_sign_detached($pae,$secret);
    if(!sodium_crypto_sign_verify_detached($sig,$pae,$public)) throw new ArtifactException('invalid_stdin_key');
    $bytes=StrictJson::encode(['payloadType'=>Statement::PAYLOAD_TYPE,'payload'=>base64_encode($payload),'signatures'=>[['keyid'=>$keyid,'sig'=>base64_encode($sig)]]])."\n";
    // Recheck the local snapshot after signing; build workspace must be OS-trusted.
    if(filesize($archive)!==$size || !hash_equals($digest,(string)hash_file('sha256',$archive))) throw new ArtifactException('signing_input_changed');
    $file=@fopen($output,'xb');if(!$file) throw new ArtifactException('exclusive_write_failed');$complete=false;
    try {
        $offset=0;while($offset<strlen($bytes)) {$n=fwrite($file,substr($bytes,$offset));if(!$n) throw new ArtifactException('envelope_write_failed');$offset+=$n;}
        if(!fflush($file) || (function_exists('fsync')&&!fsync($file))) throw new ArtifactException('envelope_write_failed');$complete=true;
    } finally {fclose($file);if(!$complete) @unlink($output);}
    echo StrictJson::encode(['result'=>'signed','keyid'=>$keyid,'sha256'=>$digest,'size'=>$size,'zip'=>$zip])."\n";
} catch(ArtifactException $e) {fwrite(STDERR,StrictJson::encode(['result'=>'rejected','error'=>$e->errorCode])."\n");$exitCode=1;}
catch(Throwable) {fwrite(STDERR,"{\"result\":\"rejected\",\"error\":\"signing_failed\"}\n");$exitCode=1;}
finally {if(is_string($secret)) sodium_memzero($secret);if(is_string($input)) sodium_memzero($input);}
exit($exitCode);
