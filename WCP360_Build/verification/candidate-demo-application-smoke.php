<?php
declare(strict_types=1);
// Trusted test harness inside the disposable guest/container, never a public endpoint.
$rows = json_decode((string) file_get_contents('/tmp/wcp-provision-requests.json'), true, 16, JSON_THROW_ON_ERROR);
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
foreach ($rows as $index=>$r) {
    [$status,$page,$cookie] = fetchDemo($r);
    if ($status !== 200 || $page['application'] !== 'wcp360-demo/1' || $page['site_id'] !== $r['site_id'] || $page['tenant_id'] !== $r['tenant_id'] || $page['counter'] !== 0 || $cookie === '') throw new RuntimeException('Packaged application initialization failed.');
    if (fetchDemo($r,'POST',$cookie,'csrf=invalid&action=increment')[0] !== 403) throw new RuntimeException('CSRF failure accepted.');
    for ($i=0; $i<=$index; ++$i) {
        [$status,$updated] = fetchDemo($r,'POST',$cookie,http_build_query(['csrf'=>$page['csrf'],'action'=>'increment']));
        if ($status !== 200 || $updated['counter'] !== $i+1) throw new RuntimeException('Private application state failed.');
    }
    if (fetchDemo($r)[1]['counter'] !== $index+1) throw new RuntimeException('State did not persist across requests.');
    $base = '/srv/wcp360-candidate/vhosts/'.$r['site_id'];
    $code = stat($base.'/public/index.php');
    $state = stat($base.'/var/demo-counter');
    if ($code['uid'] !== 0 || ($code['mode'] & 0022) !== 0 || $state['uid'] === 0 || ($state['mode'] & 0077) !== 0) throw new RuntimeException('Application ownership boundary failed.');
}
echo "PASS: embedded application, session/CSRF, private persistent counters and immutable code on both tenants.\n";
