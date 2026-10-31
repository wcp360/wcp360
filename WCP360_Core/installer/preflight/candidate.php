#!/usr/bin/env php
<?php

declare(strict_types=1);
$target=in_array('--target',$argv,true);
$extensions=['ctype','iconv','pdo','sodium','intl','mbstring','openssl','dom'];
$checks=['php_8_4'=>PHP_VERSION_ID>=80400&&PHP_VERSION_ID<80500];
if(in_array('--dependencies',$argv,true))$checks['composer_dependencies']=is_file(dirname(__DIR__,2).'/vendor/autoload.php');
foreach($extensions as $extension)$checks['ext_'.$extension]=extension_loaded($extension);
if($target){
    $os=is_file('/etc/os-release')?(string)file_get_contents('/etc/os-release'):'';
    $checks['debian_13']=PHP_OS_FAMILY==='Linux'&&preg_match('/^ID=debian$/m',$os)&&preg_match('/^VERSION_ID="?13"?$/m',$os);
    $checks['amd64']=php_uname('m')==='x86_64';
    $checks['pdo_pgsql']=extension_loaded('pdo_pgsql');
}
echo json_encode(['mode'=>$target?'target-preflight':'portable-development-preflight','checks'=>$checks,'qualified_release'=>false],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL;
exit(in_array(false,array_map(static fn($x)=>(bool)$x,$checks),true)?1:0);
