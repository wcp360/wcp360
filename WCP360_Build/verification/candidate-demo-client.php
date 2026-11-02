<?php
declare(strict_types=1);
// Shared HTTP fixture client; no requests execute on include.
function fetchDemo(array $r, string $method = 'GET', string $cookie = '', string $body = ''): array {
    $headers = 'Host: '.$r['hostname']."\r\nCookie: ".$cookie."\r\nContent-Type: application/x-www-form-urlencoded\r\n";
    $context = stream_context_create(['http'=>['method'=>$method,'header'=>$headers,'content'=>$body,'timeout'=>5,'ignore_errors'=>true]]);
    $raw = file_get_contents('http://127.0.0.1:8080/', false, $context);
    $responseHeaders = http_get_last_response_headers();
    $status = (int) explode(' ', $responseHeaders[0] ?? '')[1];
    $session = $cookie;
    foreach ($responseHeaders as $header) if (preg_match('/^Set-Cookie: (PHPSESSID=[^;]+)/i', $header, $match)) $session = $match[1];
    return [$status, $raw === '' || $raw === false ? [] : json_decode($raw,true,16,JSON_THROW_ON_ERROR), $session];
}
