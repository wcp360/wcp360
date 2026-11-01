<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

/** Fixed non-root development commands, independent of Symfony/vendor bootstrap. */
final readonly class CandidateInstaller
{
    public function __construct(private string $root, private string $php, private string $composer, private string $go) {}

    /** @return array{argv: list<string>, cwd: string, overrides: array<string, string>} */
    public function command(string $step): array
    {
        $command = match ($step) {
            'composer_install' => [[$this->php, $this->composer, 'install', '--no-interaction', '--prefer-dist', '--no-progress'], $this->root, []],
            'dependencies' => [[$this->php, $this->root.'/installer/preflight/candidate.php', '--target', '--dependencies'], $this->root, []],
            'planner_build' => [[$this->go, 'build', '-trimpath', '-o', $this->root.'/var/bin/wcp-plan', './cmd/wcp-plan'], $this->root.'/agents/wcpd/candidate/planner', ['CGO_ENABLED' => '0']],
            'importmap_install' => [[$this->php, $this->root.'/bin/console', 'importmap:install', '--no-interaction'], $this->root, []],
            'asset_compile' => [[$this->php, $this->root.'/bin/console', 'asset-map:compile', '--no-interaction'], $this->root, []],
            'database_migrations' => [[$this->php, $this->root.'/bin/console', 'doctrine:migrations:migrate', '--no-interaction'], $this->root, []],
            'database_verify' => [[$this->php, $this->root.'/bin/console', 'doctrine:migrations:up-to-date', '--fail-on-unregistered', '--no-interaction'], $this->root, []],
            default => throw new \InvalidArgumentException('Unknown fixed installation step.'),
        };
        return ['argv' => $command[0], 'cwd' => $command[1], 'overrides' => $command[2]];
    }

    public function execute(string $step): int
    {
        $command = $this->command($step);
        if ($step === 'planner_build') {
            $path = $this->root.'/var/bin';
            clearstatcache(true, $path);
            if (!file_exists($path) && !is_link($path) && !mkdir($path, 0700)) {
                throw new \RuntimeException('Cannot prepare the candidate planner directory.');
            }
            $stat = @lstat($path);
            $binary = $path.'/wcp-plan';
            clearstatcache(true, $binary);
            $binaryStat = @lstat($binary);
            if ($stat === false || ($stat['mode'] & 0170000) !== 0040000 || is_link($path)
                || ($binaryStat !== false && (($binaryStat['mode'] & 0170000) !== 0100000 || $binaryStat['nlink'] !== 1 || is_link($binary)))) {
                throw new \RuntimeException('Planner output must remain inside a real checkout directory.');
            }
            foreach ([$stat, $binaryStat] as $metadata) {
                if ($metadata !== false && PHP_OS_FAMILY !== 'Windows'
                    && (!function_exists('posix_geteuid') || $metadata['uid'] !== posix_geteuid() || ($metadata['mode'] & 0022) !== 0)) {
                    throw new \RuntimeException('Planner output must belong to the operator and refuse group/other writes.');
                }
            }
        }
        $environment = getenv();
        // Dependency/bootstrap tools can include dotenv lines, URLs or credentials
        // in diagnostics. Keep only the fixed step and exit code, never raw output.
        $null = PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null';
        $process = proc_open($command['argv'], [0 => ['file', $null, 'r'], 1 => ['file', $null, 'w'], 2 => ['file', $null, 'w']], $pipes, $command['cwd'], array_replace($environment, $command['overrides']), ['bypass_shell' => true]);
        if (!is_resource($process)) {
            throw new \RuntimeException('Cannot start the fixed installation step.');
        }
        $exit = proc_close($process);
        return $exit < 0 ? 1 : $exit;
    }

    /** Resolve a fixed tool name using PATH once, then execute its absolute identity. */
    public static function tool(string $name): string
    {
        if (!in_array($name, ['composer', 'go'], true)) {
            throw new \InvalidArgumentException('Unknown installer tool.');
        }
        foreach (explode(PATH_SEPARATOR, getenv('PATH') ?: '') as $directory) {
            if ($directory === '' || !str_starts_with($directory, '/')) {
                continue;
            }
            $path = realpath($directory.'/'.$name);
            if ($path !== false && is_file($path) && is_executable($path)) {
                return $path;
            }
        }
        throw new \RuntimeException('A required installer tool is unavailable.');
    }
}
