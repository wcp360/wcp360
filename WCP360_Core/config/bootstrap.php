<?php

declare(strict_types=1);
require dirname(__DIR__).'/vendor/autoload.php';
$dotenv=new Symfony\Component\Dotenv\Dotenv();
if(is_file(dirname(__DIR__).'/.env')) $dotenv->bootEnv(dirname(__DIR__).'/.env');
else $dotenv->populate(['APP_ENV'=>$_SERVER['APP_ENV']??$_ENV['APP_ENV']??getenv('APP_ENV')?:'dev','APP_DEBUG'=>$_SERVER['APP_DEBUG']??$_ENV['APP_DEBUG']??getenv('APP_DEBUG')?:'0']);
