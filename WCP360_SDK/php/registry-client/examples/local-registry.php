<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
// Explicit loopback-only demonstration. No credentials or package installation.
require dirname(__DIR__).'/autoload.php';
use Wcp360\Sdk\Registry\{RegistryClient,CurlTransport};
$client=new RegistryClient('http://localhost/grav/api/v1/wcp360-registry',new CurlTransport(true),acceptCandidate:true,allowLoopbackHttp:true);
echo json_encode(['status'=>$client->status(),'plugins'=>$client->catalog('plugin'),'modules'=>$client->catalog('module'),'apps'=>$client->catalog('app'),'releases'=>$client->releases()],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR)."\n";
