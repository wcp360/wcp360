#!/usr/bin/env php
<?php

declare(strict_types=1);

use Wcp360\Core\Shared\Platform\RuntimePlan;
use Wcp360\Core\Shared\Platform\WebStackCatalogue;

require dirname(__DIR__, 2).'/src/Shared/Platform/TargetPlatform.php';
require dirname(__DIR__, 2).'/src/Shared/Platform/RuntimePlan.php';

require dirname(__DIR__, 2).'/src/Shared/Platform/WebStackCatalogue.php';

$usage = static function (): never {
    fwrite(STDERR, "Usage: php installer/preflight/runtime-plan.php [--web-stacks | --web-stack=classic|modern|legacy|advanced] [--worker-plans]\n");
    exit(64);
};
$list = false;
$workerPlans = false;
$stack = null;
$seen = [];
foreach (array_slice($argv, 1) as $argument) {
    if (strlen($argument) > 64) $usage();
    if ($argument === '--web-stacks') {
        if (isset($seen['list'])) $usage();
        $seen['list'] = true;
        $list = true;
    } elseif ($argument === '--worker-plans') {
        if (isset($seen['worker'])) $usage();
        $seen['worker'] = true;
        $workerPlans = true;
    } elseif (str_starts_with($argument, '--web-stack=')) {
        if (isset($seen['stack'])) $usage();
        $seen['stack'] = true;
        $stack = substr($argument, strlen('--web-stack='));
    } else $usage();
}
if (($list && $stack !== null) || ($workerPlans && !$list && $stack === null)) $usage();
if ($list) {
    echo json_encode(WebStackCatalogue::report($workerPlans), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
    exit(0);
}
try {
    $selection = $stack === null ? null : WebStackCatalogue::choice($stack, $workerPlans);
} catch (InvalidArgumentException) {
    $usage();
}
try {
    $plan = RuntimePlan::forHost();
} catch (RuntimeException) {
    fwrite(STDERR, "Runtime plan unavailable: target or provider catalogue is invalid.\n");
    exit(78);
}
$report = [
    'supported_target' => $plan !== null,
    'plan' => $plan,
    'host_changes' => false,
    'qualified_release' => false,
];
if ($selection !== null) {
    $report['hosting_web_stack'] = $selection;
    $report['native_installation_available'] = false;
}
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
// Exit 0 identifies a supported target/profile inspection, never installation approval.
exit($plan !== null && ($selection === null || $selection['selectable_for_plan']) ? 0 : 78);
