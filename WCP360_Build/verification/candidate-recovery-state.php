<?php
declare(strict_types=1);
require '/opt/wcp360/WCP360_Core/vendor/autoload.php';
Wcp360\Core\Module\Hosting\Infrastructure\DisposableExecution::requireEnvironment();
require __DIR__.'/candidate-demo-client.php';
$root='/var/lib/wcp360-candidate-recovery-test';
$rows=json_decode((string)file_get_contents($root.'/requests.json'),true,16,JSON_THROW_ON_ERROR);
if (($argv[1]??'')==='capture') {
    $snapshot=['files'=>[], 'sites'=>[]];
    foreach (['/etc/passwd','/etc/group'] as $file) $snapshot['files'][$file]=hash_file('sha256',$file);
    foreach($rows as $index=>$r){
        $site=$r['site_id'];$base='/srv/wcp360-candidate/vhosts/'.$site;
        foreach ([$base.'/public/index.php',$base.'/public/__wcp_probe.php','/var/lib/wcp360-provision-candidate/records/'.$site.'.json','/var/lib/wcp360-provision-candidate/nginx-sites/'.$site.'.conf','/var/lib/wcp360-provision-candidate/fpm-pools/'.$site.'.conf'] as $file) $snapshot['files'][$file]=hash_file('sha256',$file);
        [$status,$page,$cookie]=fetchDemo($r);
        if($status!==200||$page['counter']!==$index+1)throw new RuntimeException('Initial application state missing.');
        $snapshot['sites'][$site]=['cookie'=>$cookie,'csrf'=>$page['csrf'],'counter'=>$page['counter'],'uid'=>fileowner($base.'/var/demo-counter')];
    }
    umask(0077);file_put_contents($root.'/snapshot.json',json_encode($snapshot,JSON_THROW_ON_ERROR));
} elseif (($argv[1]??'')==='verify') {
    $snapshot=json_decode((string)file_get_contents($root.'/snapshot.json'),true,16,JSON_THROW_ON_ERROR);
    foreach($snapshot['files'] as $file=>$hash) if(hash_file('sha256',$file)!==$hash)throw new RuntimeException('Recovery rewrote persistent identity/configuration/journal/code.');
    foreach($rows as $r){
        $expected=$snapshot['sites'][$r['site_id']];[$status,$page]=fetchDemo($r,'GET',$expected['cookie']);
        if($status!==200||$page['site_id']!==$r['site_id']||$page['tenant_id']!==$r['tenant_id']||$page['counter']!==$expected['counter']||$page['csrf']!==$expected['csrf']||fileowner('/srv/wcp360-candidate/vhosts/'.$r['site_id'].'/var/demo-counter')!==$expected['uid'])throw new RuntimeException('Recovered state/session/UID mismatch.');
    }
    echo "PASS: recovery preserves identities, code, configuration, journals, native sessions and private application state.\n";
} else throw new RuntimeException('Unknown snapshot mode.');
