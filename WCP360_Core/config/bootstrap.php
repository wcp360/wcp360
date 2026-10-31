<?php

declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
$dotenv=new Symfony\Component\Dotenv\Dotenv();
if(is_file(dirname(__DIR__).'/.env')){
    $dotenv->bootEnv(dirname(__DIR__).'/.env');
}else{
    // Packaged/container deployments can supply ENV without populating SERVER.
    $environment=$_SERVER['APP_ENV']??$_ENV['APP_ENV']??getenv('APP_ENV')?:'dev';
    $debug=$_SERVER['APP_DEBUG']??$_ENV['APP_DEBUG']??getenv('APP_DEBUG')?:'0';
    $values=['APP_ENV'=>$environment,'APP_DEBUG'=>filter_var($debug,FILTER_VALIDATE_BOOLEAN)?'1':'0'];
    $dotenv->populate($values);
    foreach($values as $name=>$value)$_SERVER[$name]=$_ENV[$name]=$value;
}
