#!/usr/bin/env php
<?php

declare(strict_types=1);

use Wcp360\Core\Module\Setup\Application\NativeInstallationPlan;

$root = dirname(__DIR__, 2);

try {
    // An incomplete checkout must not disclose local paths through PHP warnings.
    @require $root.'/src/Shared/Platform/TargetPlatform.php';
    @require $root.'/src/Shared/Platform/RuntimePlan.php';
    @require $root.'/src/Shared/Platform/LinuxHostInventory.php';
    @require $root.'/src/Module/Setup/Application/NativeInstallationPlan.php';
    $report = NativeInstallationPlan::forHost(array_slice($argv, 1));
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
    // Inspection is not approval. There is deliberately no native executor.
    exit(78);
} catch (InvalidArgumentException) {
    fwrite(STDERR, "Usage: native-plan.php [--tcp=80,443|--tcp=none] [--udp=53|--udp=none] [--memory-mib=N] [--disk-mib=N]\n");
    exit(64);
} catch (Throwable) {
    fwrite(STDERR, "Native installation inspection is unavailable. No installation was performed.\n");
    exit(1);
}
