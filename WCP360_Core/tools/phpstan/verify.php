<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;

require dirname(__DIR__, 2).'/vendor/autoload.php';

$core = dirname(__DIR__, 2);
$process = new Process([
    PHP_BINARY,
    $core.'/vendor/phpstan/phpstan/phpstan',
    'analyse',
    '--configuration='.$core.'/tools/phpstan/fixtures.neon',
    '--error-format=json',
    '--no-progress',
    // CLI paths replace the normal source scope for this deliberate-error run.
    $core.'/tools/phpstan/fixtures/ValidContainer.php',
    $core.'/tools/phpstan/fixtures/InvalidContainer.php',
    $core.'/tools/phpstan/fixtures/ConsoleProbe.php',
], $core, null, null, 120);
$process->run();

$report = json_decode($process->getOutput(), true, 32, JSON_THROW_ON_ERROR);
if (!is_array($report) || $process->getExitCode() !== 1 || ($report['errors'] ?? []) !== []) {
    throw new RuntimeException('PHPStan fixture analysis failed unexpectedly: '.$process->getErrorOutput());
}

$files = $report['files'] ?? [];
if (!is_array($files)) {
    throw new RuntimeException('PHPStan did not return file diagnostics.');
}

$observed = [];
foreach ($files as $file => $diagnostic) {
    if (!is_string($file) || !is_array($diagnostic)) {
        throw new RuntimeException('Unexpected PHPStan fixture diagnostics.');
    }
    $messages = $diagnostic['messages'] ?? [];
    if (!is_array($messages) || $messages === []) {
        throw new RuntimeException('Missing PHPStan diagnostic identifiers.');
    }
    $identifiers = [];
    foreach ($messages as $message) {
        if (!is_array($message) || !is_string($message['identifier'] ?? null)) {
            throw new RuntimeException('Malformed PHPStan diagnostic identifier.');
        }
        $identifiers[] = $message['identifier'];
    }
    sort($identifiers);
    $observed[basename($file)] = $identifiers;
}

$expected = [
    'ConsoleProbe.php' => ['symfonyConsole.optionNotFound'],
    'InvalidContainer.php' => ['symfonyContainer.privateService', 'symfonyContainer.serviceNotFound'],
];
ksort($observed);
if ($observed !== $expected) {
    throw new RuntimeException('Symfony rules must accept valid lookups/options and reject exactly the missing service, private service and unknown option: '.json_encode($observed, JSON_THROW_ON_ERROR));
}

fwrite(STDOUT, "PHPStan Symfony regression passed: valid lookups/options accepted; missing/private services and unknown option rejected.\n");
