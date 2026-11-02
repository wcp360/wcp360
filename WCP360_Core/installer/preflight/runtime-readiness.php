#!/usr/bin/env php
<?php

declare(strict_types=1);

use Wcp360\Core\Shared\Platform\RuntimeReadiness;

require dirname(__DIR__, 2).'/src/Shared/Platform/RuntimeReadiness.php';

if (count($argv) !== 1) {
    fwrite(STDERR, "Usage: php installer/preflight/runtime-readiness.php\n");
    exit(64);
}
// Inspect explicit process input only. Never load an operator .env file or test override.
$databaseUrl = getenv('DATABASE_URL');
$report = RuntimeReadiness::check(is_string($databaseUrl) ? $databaseUrl : null);
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
exit($report['ready'] ? 0 : 1);
