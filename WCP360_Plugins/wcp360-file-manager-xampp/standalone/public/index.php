<?php
declare(strict_types=1);
ini_set('display_errors','0');
require dirname(__DIR__).'/bootstrap.php';
use Symfony\Component\HttpFoundation\Request;
$kernel=new Wcp360\FileManager\Standalone\Kernel('prod',false);
$request=Request::createFromGlobals();
$response=$kernel->handle($request); $response->send(); $kernel->terminate($request,$response);
