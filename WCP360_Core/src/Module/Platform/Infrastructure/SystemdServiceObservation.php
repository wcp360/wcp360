<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\Infrastructure;

use Wcp360\Core\Module\Platform\Application\ServiceObservation;
use Wcp360\Core\Module\Platform\Domain\ServiceObservationSnapshot;
use Wcp360\Core\Module\Platform\Domain\ServiceUnitCatalogue;
use Wcp360\Core\Shared\Platform\TargetPlatform;

/** Bounded unprivileged system-manager reads; no service lifecycle or caller-supplied selectors. */
final class SystemdServiceObservation implements ServiceObservation
{
    private const SYSTEMCTL_PATHS = ['/usr/bin/systemctl', '/bin/systemctl'];
    private const TIMEOUT_NS = 3_000_000_000;
    private const STDOUT_LIMIT = 32768;
    private const STDERR_LIMIT = 8192;

    public function read(): ServiceObservationSnapshot
    {
        try {
            if (PHP_OS_FAMILY !== 'Linux' || !function_exists('posix_geteuid') || posix_geteuid() === 0
                || TargetPlatform::detectHost() === null || !is_dir('/run/systemd/system')) {
                return ServiceObservationSnapshot::notConfigured();
            }
            $manager = @file_get_contents('/proc/1/comm', false, null, 0, 17);
            if ($manager !== "systemd\n" && $manager !== 'systemd') {
                return ServiceObservationSnapshot::notConfigured();
            }
            $binary = self::systemctlBinary();
            if ($binary === null) {
                return ServiceObservationSnapshot::notConfigured();
            }

            return ServiceObservationSnapshot::fromSystemdShow(self::readSystemdShow($binary));
        } catch (\Throwable) {
            // Native diagnostics, partial output and internal failures never reach the caller.
            return ServiceObservationSnapshot::failed();
        }
    }

    private static function systemctlBinary(): ?string
    {
        foreach (self::SYSTEMCTL_PATHS as $path) {
            $resolved = @realpath($path);
            if ($resolved === false || !in_array($resolved, self::SYSTEMCTL_PATHS, true)
                || !is_file($resolved) || !is_executable($resolved)
                || !self::trustedPathChain($path) || !self::trustedPathChain($resolved)) {
                continue;
            }
            $metadata = @stat($resolved);
            if ($metadata !== false && ($metadata['mode'] & 06000) === 0) {
                return $resolved;
            }
        }

        return null;
    }

    private static function trustedPathChain(string $path): bool
    {
        do {
            $metadata = @lstat($path);
            if ($metadata === false || $metadata['uid'] !== 0) {
                return false;
            }
            // Root-owned aliases such as /bin -> /usr/bin have 0777 symlink bits.
            // Their resolved target is checked separately; no writable directory is accepted.
            $symlink = ($metadata['mode'] & 0170000) === 0120000;
            if (!$symlink && (($metadata['mode'] & 0022) !== 0 || is_writable($path))) {
                return false;
            }
            $parent = dirname($path);
            if ($parent === $path) {
                return true;
            }
            $path = $parent;
        } while (true);
    }

    /**
     * This private closed definition is shared by the single read path.
     *
     * @return array{
     *     argv: list<string>,
     *     cwd: string,
     *     environment: array<string, string>,
     *     descriptors: array{0: array{string, string, string}, 1: array{string, string}, 2: array{string, string}},
     *     options: array{bypass_shell: true, suppress_errors: true}
     * }
     */
    private static function processDefinition(string $binary): array
    {
        if (!in_array($binary, self::SYSTEMCTL_PATHS, true)) {
            throw new \InvalidArgumentException('Invalid service observation executable.');
        }

        return [
            'argv' => [$binary, 'show', '--no-pager', '--all',
                '--property=Id,LoadState,ActiveState,SubState,UnitFileState', '--', ...ServiceUnitCatalogue::unitNames()],
            'cwd' => '/',
            // proc_open receives this exact environment; no inherited DBUS_*, SYSTEMD_* or LD_* values.
            'environment' => [
                'LC_ALL' => 'C', 'LANG' => 'C', 'PATH' => '/usr/bin:/bin', 'HOME' => '/nonexistent',
                'XDG_CONFIG_HOME' => '/nonexistent', 'XDG_DATA_HOME' => '/nonexistent',
                'XDG_CACHE_HOME' => '/nonexistent', 'XDG_STATE_HOME' => '/nonexistent',
                'XDG_RUNTIME_DIR' => '/nonexistent',
            ],
            'descriptors' => [0 => ['file', '/dev/null', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            'options' => ['bypass_shell' => true, 'suppress_errors' => true],
        ];
    }

    private static function readSystemdShow(string $binary): string
    {
        $definition = self::processDefinition($binary);
        /** @var array<int, resource> $pipes */
        $pipes = [];
        $process = null;
        $stdout = '';
        $stderrBytes = 0;
        $exitObserved = false;
        $deadline = hrtime(true) + self::TIMEOUT_NS;

        try {
            // A native argv array has no shell wrapper or fallback; stdin is already closed to input.
            $process = @proc_open($definition['argv'], $definition['descriptors'], $pipes, $definition['cwd'],
                $definition['environment'], $definition['options']);
            if (!is_resource($process) || array_keys($pipes) !== [1, 2]) {
                throw new \RuntimeException('Service observation process is unavailable.');
            }
            foreach ($pipes as $pipe) {
                if (!@stream_set_blocking($pipe, false)) {
                    throw new \RuntimeException('Service observation pipe is unavailable.');
                }
            }
            $exitCode = -1;
            while (true) {
                $remaining = $deadline - hrtime(true);
                if ($remaining <= 0) {
                    throw new \RuntimeException('Service observation deadline exceeded.');
                }
                $state = @proc_get_status($process);
                if (!$state['running']) {
                    $exitObserved = true;
                    $exitCode = $state['exitcode'];
                }
                $read = [];
                foreach ($pipes as $pipe) {
                    if (!@feof($pipe)) {
                        $read[] = $pipe;
                    }
                }
                if ($read === [] && !$state['running']) {
                    break;
                }
                $remaining = $deadline - hrtime(true);
                if ($remaining <= 0) {
                    throw new \RuntimeException('Service observation deadline exceeded.');
                }
                $microseconds = min(100000, max(1, (int) ($remaining / 1000)));
                if ($read === []) {
                    usleep(min(10000, $microseconds));
                    continue;
                }
                $write = null;
                $except = null;
                if (@stream_select($read, $write, $except, 0, $microseconds) === false) {
                    throw new \RuntimeException('Service observation pipe failed.');
                }
                foreach ($read as $pipe) {
                    $chunk = @fread($pipe, 4096);
                    if ($chunk === false) {
                        throw new \RuntimeException('Service observation output failed.');
                    }
                    if ($pipe === $pipes[1]) {
                        if (strlen($stdout) + strlen($chunk) > self::STDOUT_LIMIT) {
                            throw new \RuntimeException('Service observation output exceeded its limit.');
                        }
                        $stdout .= $chunk;
                    } else {
                        $stderrBytes += strlen($chunk);
                        if ($stderrBytes > self::STDERR_LIMIT) {
                            throw new \RuntimeException('Service observation diagnostics exceeded their limit.');
                        }
                    }
                }
            }
            foreach ($pipes as $key => $pipe) {
                if (!@fclose($pipe)) {
                    throw new \RuntimeException('Service observation pipe failed to close.');
                }
                unset($pipes[$key]);
            }
            if (hrtime(true) >= $deadline) {
                throw new \RuntimeException('Service observation deadline exceeded.');
            }
            $closedExitCode = @proc_close($process);
            $process = null;
            if ($exitCode !== 0 || $closedExitCode !== 0) {
                throw new \RuntimeException('Service observation process failed.');
            }

            return $stdout;
        } finally {
            foreach ($pipes as $pipe) {
                try { @fclose($pipe); } catch (\Throwable) {}
            }
            if (is_resource($process)) {
                // A reaped child may already have had its PID recycled; never signal it again.
                if (!$exitObserved) {
                    try { @proc_terminate($process, 9); } catch (\Throwable) {}
                }
                try { @proc_close($process); } catch (\Throwable) {}
            }
        }
    }
}
