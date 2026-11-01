#!/usr/bin/env php
<?php

declare(strict_types=1);

use Wcp360\Core\Shared\Platform\RuntimePlan;

require dirname(__DIR__, 2).'/src/Shared/Platform/TargetPlatform.php';
require dirname(__DIR__, 2).'/src/Shared/Platform/RuntimePlan.php';

if (count($argv) !== 1) {
    fwrite(STDERR, "Usage: php installer/preflight/runtime-plan.php\n");
    exit(64);
}
try {
    $plan = RuntimePlan::forHost();
} catch (RuntimeException) {
    fwrite(STDERR, "Runtime plan unavailable: target or provider catalogue is invalid.\n");
    exit(78);
}
echo json_encode([
    'supported_target' => $plan !== null,
    'plan' => $plan,
    'host_changes' => false,
    'qualified_release' => false,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
exit($plan === null ? 78 : 0);
