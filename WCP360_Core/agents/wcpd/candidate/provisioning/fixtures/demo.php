<?php
declare(strict_types=1);
// Reviewed, embedded demonstration package. No upload or customer code is accepted.
umask(0077);
header('Content-Type: application/json');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
if (!session_start()) { http_response_code(503); exit; }
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
$method = $_SERVER['REQUEST_METHOD'] ?? '';
if (!in_array($method, ['GET', 'POST'], true)) { http_response_code(405); exit; }
if ($method === 'POST' && (array_keys($_POST) !== ['csrf', 'action'] || !is_string($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf']) || $_POST['action'] !== 'increment')) { http_response_code(403); exit; }
$file = fopen(dirname(__DIR__).'/var/demo-counter', 'c+');
if ($file === false || !flock($file, LOCK_EX)) { http_response_code(503); exit; }
try {
    $raw = stream_get_contents($file, 32);
    if ($raw === false || ($raw !== '' && !ctype_digit($raw))) { http_response_code(503); exit; }
    $counter = (int) $raw;
    if ($method === 'POST') {
        $counter = min($counter + 1, 1000000);
        rewind($file);
        if (!ftruncate($file, 0) || fwrite($file, (string) $counter) === false || !fflush($file)) { http_response_code(503); exit; }
    }
} finally { flock($file, LOCK_UN); fclose($file); }
echo json_encode(['application'=>'wcp360-demo/1','site_id'=>'{{SITE_ID}}','tenant_id'=>'{{TENANT_ID}}','counter'=>$counter,'csrf'=>$_SESSION['csrf']], JSON_THROW_ON_ERROR);
