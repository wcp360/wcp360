<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli-server'||($_SERVER['REMOTE_ADDR']??'')!=='127.0.0.1'){http_response_code(404);exit;}
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);header('Content-Type: application/json');header('Cache-Control: private, no-store');
if(str_starts_with($path,'/redirect/')){http_response_code(302);header('Location: https://unreachable.invalid/never-follow');exit;}
if(str_starts_with($path,'/oversize/')){echo str_repeat('x',17000);exit;}
if(str_starts_with($path,'/headers/')){for($n=0;$n<20;$n++)header('X-Fixture-'.$n.': '.str_repeat('x',1000));echo '{}';exit;}
echo json_encode(['cookie'=>isset($_SERVER['HTTP_COOKIE']),'authorization'=>isset($_SERVER['HTTP_AUTHORIZATION'])],JSON_THROW_ON_ERROR);
