<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Infrastructure;

use Symfony\Component\Process\Process;

/** Unprivileged internal parser children. Callers own the fixed binary and argument allowlist. */
class SyntaxProcessRunner
{
    public function run(Process $process): SyntaxProcessResult
    {
        $stdout = '';
        $stderrBytes = 0;
        $limitExceeded = false;
        try {
            $keys = array_keys(array_merge(getenv() ?: [], $_ENV, $_SERVER, $process->getEnv()));
            $environment = array_fill_keys($keys, false);
            foreach (['HOME', 'XDG_CONFIG_HOME', 'XDG_DATA_HOME', 'XDG_CACHE_HOME', 'XDG_STATE_HOME', 'XDG_RUNTIME_DIR'] as $key) {
                $environment[$key] = '/nonexistent';
            }
            $environment['PHP_INI_SCAN_DIR'] = '';
            $process->setTimeout(5.0);
            $process->setIdleTimeout(null);
            $process->disableOutput();
            $process->run(static function (string $type, string $chunk) use (&$stdout, &$stderrBytes, &$limitExceeded): void {
                if ($limitExceeded) return;
                if ($type === Process::OUT) {
                    if (strlen($stdout) + strlen($chunk) > 262144) {
                        $limitExceeded = true;
                        throw new \RuntimeException('Parser output limit.');
                    }
                    $stdout .= $chunk;
                } else {
                    $stderrBytes += strlen($chunk);
                    if ($stderrBytes > 16384) {
                        $limitExceeded = true;
                        throw new \RuntimeException('Parser diagnostic limit.');
                    }
                }
            }, $environment);
            if ($process->isSuccessful()) return new SyntaxProcessResult('passed', $stdout);
        } catch (\Throwable) {
            // Partial output and exception/native diagnostics do not cross the application boundary.
        } finally {
            try { if ($process->isRunning()) $process->stop(0.1); } catch (\Throwable) {}
        }
        return new SyntaxProcessResult('failed');
    }
}
