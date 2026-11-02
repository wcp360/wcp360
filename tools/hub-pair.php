<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli')exit(1);
require dirname(__DIR__).'/WCP360_SDK/php/pairing-client/autoload.php';
use Wcp360\Sdk\Pairing\{Client,CurlTransport,Wire,Vault,PairingException};
$action=$argv[1]??'';$options=[];
try{
    if(!in_array($action,['pair','resume','check','local-status'],true))throw new PairingException('usage_pair_resume_check_local-status');
    foreach(array_slice($argv,2) as $arg){if($arg==='--allow-loopback-http'){$options['allow-loopback-http']=true;continue;}if(!preg_match('/^--(state-dir|hub|core-version|os|architecture)=(.+)$/D',$arg,$m)||isset($options[$m[1]]))throw new PairingException('invalid_option');$options[$m[1]]=$m[2];}
    if(!isset($options['state-dir']))throw new PairingException('state_directory_required');$roots=[dirname(__DIR__).'/WCP360_Core/public'];$xampp=dirname(__DIR__,3).'/htdocs';if(is_dir($xampp))$roots[]=$xampp;$vault=new Vault($options['state-dir'],$roots);$state=$vault->read();$local=isset($options['allow-loopback-http']);
    if($action==='local-status'){echo json_encode(['status'=>$state['status']??'not_paired','instance_id'=>$state['receipt']['instance_id']??null,'remote_verification'=>false],JSON_THROW_ON_ERROR).PHP_EOL;exit;}
    $client=new Client(new CurlTransport($local));
    if($action==='pair'){
        if($state!==null)throw new PairingException('existing_state_use_resume_or_check');foreach(['hub','core-version','os','architecture'] as $key)if(!isset($options[$key]))throw new PairingException('explicit_hub_and_metadata_required');
        // Read the one-time code from standard input, never from argv, URL or environment.
        $code=trim((string)fgets(STDIN,256));$keypair=sodium_crypto_sign_keypair();$secret=sodium_crypto_sign_secretkey($keypair);$request=Wire::request('claim',$options['hub'],$secret,['code'=>$code,'core_version'=>$options['core-version'],'os'=>$options['os'],'architecture'=>$options['architecture']],$local);$state=['status'=>'pending','hub'=>$options['hub'],'secret_key'=>bin2hex($secret),'request'=>$request];$vault->save($state);sodium_memzero($secret);sodium_memzero($code);
    }
    if(!$state)throw new PairingException('no_local_identity');Wire::base($state['hub'],$local);
    if($action==='check'){
        if($state['status']!=='paired')throw new PairingException('pending_use_resume');$request=Wire::request('confirm',$state['hub'],hex2bin($state['secret_key']),['instance_id'=>$state['receipt']['instance_id']],$local);$receipt=$client->send('confirm',$request);
    }else{
        if($state['status']!=='pending')throw new PairingException('already_paired');$receipt=$client->send('claim',$state['request']);$state['status']='paired';$state['receipt']=$receipt;unset($state['request']);$vault->save($state);
    }
    echo json_encode(['status'=>'paired','instance_id'=>$receipt['instance_id'],'public_key_sha256'=>$receipt['public_key_sha256'],'verified_now'=>true],JSON_THROW_ON_ERROR).PHP_EOL;
}catch(PairingException $e){fwrite(STDERR,json_encode(['error'=>$e->errorCode,'http_status'=>$e->httpStatus,'hint'=>'Pending state is preserved. Resolve the cause before resume; do not delete the private key after an uncertain response.'],JSON_THROW_ON_ERROR).PHP_EOL);exit(1);}
catch(Throwable){fwrite(STDERR,'{"error":"pairing_client_unavailable"}'.PHP_EOL);exit(1);}
