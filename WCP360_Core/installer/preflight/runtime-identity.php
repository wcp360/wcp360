#!/usr/bin/env php
<?php

declare(strict_types=1);

use Wcp360\Core\Shared\Platform\RuntimeIdentity;

require dirname(__DIR__, 2).'/src/Shared/Platform/RuntimeIdentity.php';

if (count($argv) !== 2 || !in_array($argv[1], RuntimeIdentity::profiles(), true)) {
    fwrite(STDERR, "Usage: php installer/preflight/runtime-identity.php user|admin|worker|wcpd|wcp-privd\n");
    exit(64);
}
try {
    // No .env/bootstrap, arbitrary path/UID overrides, database or subprocess.
    $report = RuntimeIdentity::inspect($argv[1]);
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
    exit($report['matches_profile'] ? 0 : 1);
} catch (\Throwable) {
    fwrite(STDERR, "Runtime identity inspection unavailable.\n");
    exit(1);
}
