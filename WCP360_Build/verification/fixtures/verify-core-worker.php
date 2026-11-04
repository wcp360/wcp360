<?php

declare(strict_types=1);

if (getenv('WCP_ISOLATED_TEST_CONTAINER') !== '1' || !is_file('/.dockerenv')) {
    throw new RuntimeException('Disposable worker verification container required.');
}

function requireProbe(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException('Core worker probe failed: '.$message);
    }
}

$nonces = [];
$references = [];
$previous = null;
$recycles = 0;
$cases = ['normal', 'forged', 'early-error', 'subrequest'];
for ($index = 0; $index < 505; ++$index) {
    $surface = $index % 2 === 0 ? 'user' : 'admin';
    $scenario = $cases[intdiv($index, 2) % count($cases)];
    $marker = 'core-probe-'.$index;
    $context = stream_context_create(['http' => [
        'header' => 'Host: worker.test'."\r\n".'X-Request-Marker: '.$marker."\r\n",
        'timeout' => 5,
        'ignore_errors' => true,
        'follow_location' => 0,
    ]]);
    unset($http_response_header);
    $raw = file_get_contents('http://127.0.0.1:8080/core/'.$surface.'/'.$scenario, false, $context);
    requireProbe(is_string($raw), 'missing HTTP response');
    $wireHeaders = [];
    $status = 0;
    foreach ($http_response_header ?? [] as $line) {
        if (preg_match('#^HTTP/\S+ ([0-9]{3})#', $line, $match) === 1) {
            $status = (int) $match[1];
        } elseif (str_contains($line, ':')) {
            [$name, $value] = explode(':', $line, 2);
            $name = strtolower(trim($name));
            requireProbe(!isset($wireHeaders[$name]), 'duplicate response header');
            $wireHeaders[$name] = trim($value);
        }
    }
    $data = json_decode($raw, true, 32, JSON_THROW_ON_ERROR);
    $core = $data['core'] ?? null;
    requireProbe(is_array($core), 'missing real Core component evidence');
    requireProbe(($data['marker'] ?? null) === $marker && array_key_exists('previous', $data) && $data['previous'] === null, 'request marker contamination');
    requireProbe(($data['neighbor_readable'] ?? null) === false && is_int($data['euid'] ?? null) && $data['euid'] > 0, 'OS isolation');
    requireProbe(is_int($data['pid'] ?? null) && $data['pid'] > 0 && is_int($data['requests'] ?? null), 'worker identity');
    requireProbe(($core['surface'] ?? null) === $surface && ($core['scenario'] ?? null) === $scenario, 'scenario mismatch');
    requireProbe(is_string($core['instance'] ?? null) && preg_match('/^[0-9a-f]{32}$/D', $core['instance']) === 1, 'worker boot identity');
    requireProbe(is_int($core['sequence'] ?? null) && $core['sequence'] > 0, 'component request counter');
    requireProbe($status === ($scenario === 'early-error' ? 403 : 200) && ($core['status'] ?? null) === $status, 'HTTP status');
    requireProbe(($core['collected'] ?? null) === true, 'completed cyclic Request retained by live services');

    $nonce = $core['nonce'] ?? null;
    $reference = $core['reference'] ?? null;
    requireProbe(is_string($nonce) && preg_match('/^[A-Za-z0-9+\/]{24}$/D', $nonce) === 1, 'nonce format');
    requireProbe(is_string($reference) && preg_match('/^[0-9a-f]{32}$/D', $reference) === 1, 'reference format');
    requireProbe(!isset($nonces[$nonce]) && !isset($references[$reference]), 'cross-request reuse');
    $nonces[$nonce] = true;
    $references[$reference] = true;
    requireProbe($nonce === ($core['rendered_nonce'] ?? null) && $nonce !== ($core['forged_nonce'] ?? null), 'rendered nonce provenance');
    requireProbe($reference === ($core['early_reference'] ?? null) && $reference === ($core['rendered_reference'] ?? null) && $reference !== ($core['forged_reference'] ?? null), 'reference continuity/provenance');
    requireProbe(($wireHeaders['x-wcp360-request-id'] ?? null) === $reference, 'wire reference mismatch');
    $csp = $wireHeaders['content-security-policy'] ?? '';
    requireProbe(($core['headers']['Content-Security-Policy'] ?? null) === $csp, 'wire CSP mismatch');
    requireProbe(preg_match_all("/'nonce-([^']+)'/", $csp, $matches) === 2 && $matches[1] === [$nonce, $nonce], 'CSP/body nonce mismatch');
    requireProbe(!str_contains($csp, 'unsafe-inline') && !str_contains($csp, 'unsafe-eval'), 'weakened CSP');
    requireProbe(($wireHeaders['x-frame-options'] ?? null) === 'DENY' && str_contains($wireHeaders['cache-control'] ?? '', 'no-store'), 'privacy headers');
    requireProbe(!isset($wireHeaders['strict-transport-security']) && ($core['headers']['Strict-Transport-Security'] ?? null) === null, 'HSTS on HTTP');
    requireProbe(($core['subrequest_untouched'] ?? null) === ($scenario === 'subrequest' ? true : null), 'subrequest boundary');

    if ($previous === null) {
        requireProbe($core['sequence'] === 1, 'unexpected prior component traffic');
    } elseif ($previous['core']['instance'] === $core['instance']) {
        requireProbe($data['pid'] === $previous['pid'] && $data['requests'] === $previous['requests'] + 1 && $core['sequence'] === $previous['core']['sequence'] + 1, 'worker was not persistently reused');
    } else {
        ++$recycles;
        requireProbe($previous['requests'] === 500 && $data['requests'] === 1 && $core['sequence'] === 1, 'unplanned worker restart or state retained across recycle');
    }
    $previous = $data;
}
requireProbe($recycles === 1 && $previous !== null && $previous['requests'] <= 12, 'worker recycle was not exercised');
printf("PASS: 505 real FrankenPHP requests; USER/ADMIN nonce/reference isolation, early errors, subrequests, cyclic Request collection and one planned recycle.\n");
