<?php
declare(strict_types=1);
require __DIR__.'/bootstrap.php';
if(($_SERVER['REQUEST_METHOD']??'GET')!=='GET'){http_response_code(405);exit;}
header('Content-Type: application/json; charset=UTF-8'); header('Cache-Control: no-store');
if(empty($_SESSION['csrf'])) rotate_csrf();
echo json_encode(['token'=>$_SESSION['csrf']],JSON_UNESCAPED_SLASHES);
