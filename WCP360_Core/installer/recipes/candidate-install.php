#!/usr/bin/env php
<?php

declare(strict_types=1);

use Wcp360\Core\Module\Setup\Infrastructure\CandidateInstaller;
use Wcp360\Core\Module\Setup\Infrastructure\InstallationInputs;
use Wcp360\Core\Module\Setup\Infrastructure\InstallationJournal;
use Wcp360\Core\Shared\Platform\TargetPlatform;

$root = dirname(__DIR__, 2);
require $root.'/src/Module/Setup/Infrastructure/InstallationJournal.php';

$option = $argv[1] ?? '';
if (count($argv) > 2 || !in_array($option, ['', '--resume', '--status'], true)) {
    fwrite(STDERR, "Usage: php installer/recipes/candidate-install.php [--resume|--status]\n");
    exit(64);
}
try {
    $journal = new InstallationJournal($root);
    if ($option === '--status') {
        echo json_encode($journal->inspect(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
        exit(0);
    }
    if (PHP_OS_FAMILY !== 'Linux' || !function_exists('posix_geteuid') || posix_geteuid() === 0 || PHP_VERSION_ID < 80400 || PHP_VERSION_ID >= 80500 || php_uname('m') !== 'x86_64') {
        throw new RuntimeException('Use a non-root PHP 8.4 CLI on an admitted amd64 Linux target.');
    }
    require $root.'/src/Module/Setup/Infrastructure/InstallationInputs.php';
    require $root.'/src/Module/Setup/Infrastructure/CandidateInstaller.php';
    require $root.'/src/Shared/Platform/TargetPlatform.php';
    $platform = TargetPlatform::detectHost();
    if ($platform === null) {
        throw new RuntimeException('The actual operating system is not an admitted candidate target.');
    }
    foreach (['ctype', 'iconv', 'pdo', 'pdo_pgsql', 'sodium', 'intl', 'mbstring', 'openssl', 'dom'] as $extension) {
        if (!extension_loaded($extension)) {
            throw new RuntimeException('Install the required PHP extensions before preparing this checkout.');
        }
    }
    if (!is_file($root.'/../WCP360_SDK/php/CandidateJobSummary.php') || !is_file($root.'/../WCP360_Plugins/wcp360-reference-report/candidate-plugin.json')) {
        throw new RuntimeException('Use the complete trusted repository checkout.');
    }
    $composer = CandidateInstaller::tool('composer');
    $go = CandidateInstaller::tool('go');
    $php = realpath(PHP_BINARY);
    if ($php === false) {
        throw new RuntimeException('Cannot resolve the executing PHP CLI.');
    }
    $installer = new CandidateInstaller($root, $php, $composer, $go);
    $inputs = new InstallationInputs($root);
    $runtime = ['platform' => $platform['id'], 'architecture' => php_uname('m'), 'php_version' => PHP_VERSION, 'php_binary' => $php, 'composer_binary' => $composer, 'go_binary' => $go];
    $binding = '';
    $currentBinding = static function () use ($inputs, $runtime): string {
        $identity = $runtime;
        foreach (['php_binary', 'composer_binary', 'go_binary'] as $key) {
            $hash = @hash_file('sha256', $runtime[$key]);
            if ($hash === false) {
                throw new RuntimeException('Cannot verify installer tool identity.');
            }
            $identity[$key.'_sha256'] = $hash;
        }
        return $inputs->fingerprint(getenv(), $identity);
    };
    $result = $journal->execute(static function () use ($inputs, $option, $currentBinding, &$binding): string {
        $inputs->ensureEnvironment($option !== '--resume');
        return $binding = $currentBinding();
    }, $option === '--resume', static function (string $step) use ($installer, $currentBinding, &$binding): int {
        if (!hash_equals($binding, $currentBinding())) {
            throw new RuntimeException('Installer inputs changed during execution.');
        }
        fwrite(STDOUT, 'Candidate installer step: '.$step.PHP_EOL);
        return $installer->execute($step);
    });
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
    if (($result['status'] ?? '') !== 'complete') {
        exit(1);
    }
    fwrite(STDOUT, "Development checkout prepared using its configured DATABASE_URL.\nEnroll accounts with WCP_CONTEXT=setup php bin/console wcp:candidate:enroll.\nNo production release, database server, package repository or system service was installed.\n");
} catch (RuntimeException $error) {
    // These bootstrap classes expose fixed domain messages. The journal catches
    // callback exceptions without retaining their message or previous exception.
    fwrite(STDERR, $error->getMessage()."\nInspect --status and installer/recovery/README.md before retrying.\n");
    exit(1);
} catch (Throwable) {
    fwrite(STDERR, "Candidate installation refused or stopped. Inspect --status and the installer recovery guide.\n");
    exit(1);
}
