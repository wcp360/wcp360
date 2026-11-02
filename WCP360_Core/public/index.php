<?php

declare(strict_types=1);
use Symfony\Component\HttpFoundation\Request;
use Wcp360\Core\Kernel;
require dirname(__DIR__).'/config/bootstrap.php';
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
