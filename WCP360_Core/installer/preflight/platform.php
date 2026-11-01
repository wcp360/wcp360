#!/usr/bin/env php
<?php

declare(strict_types=1);

use Wcp360\Core\Shared\Platform\TargetPlatform;

require dirname(__DIR__, 2).'/src/Shared/Platform/TargetPlatform.php';

if (count($argv) !== 1) {
    fwrite(STDERR, "Usage: php installer/preflight/platform.php\n");
    exit(64);
}
try {
    $platform = PHP_OS_FAMILY === 'Linux' ? TargetPlatform::detectHost() : null;
} catch (RuntimeException $error) {
    fwrite(STDERR, $error->getMessage().PHP_EOL);
    exit(78);
}
echo json_encode([
    'supported_target' => $platform !== null,
    'platform' => $platform,
    'host_architecture' => php_uname('m'),
    'architecture_supported' => php_uname('m') === 'x86_64',
    'runtime_validation' => 'not_checked',
    'qualified_release' => false,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
exit($platform === null ? 78 : 0);
