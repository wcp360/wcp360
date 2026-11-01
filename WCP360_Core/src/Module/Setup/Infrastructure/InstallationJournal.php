<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

/** Standalone, non-root checkout progress; never a privileged operation registry. */
final class InstallationJournal
{
    public const STEPS = ['composer_install', 'dependencies', 'planner_build', 'importmap_install', 'asset_compile', 'database_migrations'];

    private const SCHEMA = 'wcp360.candidate-install-journal/1';
    private const MAX_BYTES = 32768;
    private readonly string $coreRoot;
    private readonly string $directory;
    private readonly string $statePath;
    private readonly string $lockPath;

    public function __construct(string $coreRoot)
    {
        $path = rtrim(str_replace('\\', '/', $coreRoot), '/');
        if ($path === '' || preg_match('~\A(?:/|[a-zA-Z]:/)~D', $path) !== 1 || preg_match('~(?:\A|/)\.{1,2}(?:/|\z)~D', $path) === 1) {
            throw new \RuntimeException('The installer requires a canonical absolute checkout path.');
        }
        $this->assertAncestorDirectories($path);
        $canonical = realpath($path);
        if ($canonical === false) {
            throw new \RuntimeException('The installer checkout directory is unavailable.');
        }
        $this->coreRoot = rtrim(str_replace('\\', '/', $canonical), '/');
        $this->directory = $this->coreRoot.'/var/installer';
        $this->statePath = $this->directory.'/candidate-install.json';
        $this->lockPath = $this->directory.'/.lock';
        $this->assertDirectory($this->coreRoot, false);
    }

    /** @return array<string, mixed> Sanitized inspection never creates a directory or lock. */
    public function inspect(): array
    {
        if (!$this->inspectDirectories()) {
            return $this->report(null);
        }
        if ($this->metadata($this->lockPath) !== null) {
            $this->assertRegularFile($this->lockPath);
        }
        if ($this->metadata($this->statePath) === null) {
            return $this->report(null);
        }

        return $this->report($this->readState());
    }

    /**
     * The caller supplies a fingerprint, never raw configuration. A resumed run
     * regenerates all safe outputs; a successful migration is only verified.
     * @param string|callable(): string $binding
     * @param callable(string): int $runStep
     * @return array<string, mixed>
     */
    public function execute(string|callable $binding, bool $resume, callable $runStep): array
    {
        if (is_string($binding) && preg_match('/\A[a-f0-9]{64}\z/D', $binding) !== 1) {
            throw new \RuntimeException('The installer input fingerprint is invalid.');
        }
        // Missing --resume state must not create even a lock or directory.
        if ($resume && $this->inspect()['status'] === 'absent') {
            throw new \RuntimeException('There is no installation journal to resume.');
        }
        $this->prepareDirectories();
        $lock = $this->openLock();
        try {
            if (!flock($lock, LOCK_EX | LOCK_NB)) {
                throw new \RuntimeException('Another checkout installation holds the installer lock.');
            }
            // Recheck directory/file identity after acquiring the exclusive lock.
            $this->inspectDirectories();
            $this->assertHandleIdentity($this->lockPath, $lock);
            $exists = $this->metadata($this->statePath) !== null;
            if (!$resume && $exists) {
                throw new \RuntimeException('An installation journal already exists; inspect it and use --resume.');
            }
            if ($resume && !$exists) {
                throw new \RuntimeException('There is no installation journal to resume.');
            }
            $state = $exists ? $this->readState() : null;
            $migration = $state['steps'][5]['status'] ?? 'PENDING';
            if (in_array($migration, ['RUNNING', 'FAILED'], true)) {
                throw new \RuntimeException('The database migration outcome requires manual reconciliation; no steps were executed.');
            }
            try {
                $fingerprint = is_string($binding) ? $binding : (static fn (): string => $binding())();
            } catch (\Throwable) {
                throw new \RuntimeException('The installer input fingerprint could not be prepared.');
            }
            if (preg_match('/\A[a-f0-9]{64}\z/D', $fingerprint) !== 1) {
                throw new \RuntimeException('The installer input fingerprint is invalid.');
            }
            if ($state !== null && !hash_equals($state['binding'], $fingerprint)) {
                throw new \RuntimeException('Installer inputs changed; this journal cannot be resumed.');
            }
            $state ??= $this->newState($fingerprint);
            if ($resume) {
                foreach (range(0, 4) as $index) {
                    $state['steps'][$index] = $this->step(self::STEPS[$index]);
                }
                $state['verification'] = $this->verification();
            }
            $this->writeState($state);
            foreach (self::STEPS as $index => $id) {
                if ($id === 'database_migrations' && $migration === 'SUCCEEDED') {
                    $state['verification'] = ['status' => 'RUNNING', 'exit_code' => null];
                    $this->writeState($state);
                    $exitCode = $this->invoke($runStep, 'database_verify');
                    $state['verification'] = ['status' => $exitCode === 0 ? 'SUCCEEDED' : 'FAILED', 'exit_code' => $exitCode];
                } else {
                    $state['steps'][$index] = ['id' => $id, 'status' => 'RUNNING', 'exit_code' => null];
                    $this->writeState($state);
                    $exitCode = $this->invoke($runStep, $id);
                    $state['steps'][$index] = ['id' => $id, 'status' => $exitCode === 0 ? 'SUCCEEDED' : 'FAILED', 'exit_code' => $exitCode];
                }
                $this->writeState($state);
                if ($exitCode !== 0) {
                    return $this->report($state);
                }
            }

            return $this->report($state);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /** @param callable(string): int $runStep */
    private function invoke(callable $runStep, string $id): int
    {
        try {
            // A typed return boundary also rejects callbacks violating their contract.
            $invoke = static fn (): int => $runStep($id);
            $code = $invoke();

            return $code >= 0 && $code <= 255 ? $code : 1;
        } catch (\Throwable) {
            // Journal data and diagnostics must never contain exception/configuration text.
            return 1;
        }
    }

    /** @return array{id: string, status: string, exit_code: int|null} */
    private function step(string $id): array
    {
        return ['id' => $id, 'status' => 'PENDING', 'exit_code' => null];
    }

    /** @return array{status: string, exit_code: int|null} */
    private function verification(): array
    {
        return ['status' => 'PENDING', 'exit_code' => null];
    }

    /** @return array{schema: string, binding: string, steps: list<array{id: string, status: string, exit_code: int|null}>, verification: array{status: string, exit_code: int|null}} */
    private function newState(string $binding): array
    {
        return [
            'schema' => self::SCHEMA,
            'binding' => $binding,
            'steps' => array_map(fn (string $id): array => $this->step($id), self::STEPS),
            'verification' => $this->verification(),
        ];
    }

    /**
     * @param array{schema: string, binding: string, steps: list<array{id: string, status: string, exit_code: int|null}>, verification: array{status: string, exit_code: int|null}}|null $state
     * @return array<string, mixed>
     */
    private function report(?array $state): array
    {
        $status = $state === null ? 'absent' : 'pending';
        $active = null;
        $manual = false;
        if ($state !== null) {
            foreach ($state['steps'] as $step) {
                if (in_array($step['status'], ['RUNNING', 'FAILED'], true)) {
                    $status = $step['status'] === 'RUNNING' ? 'uncertain' : 'failed';
                    $active = $step['id'];
                    $manual = $step['id'] === 'database_migrations';
                    break;
                }
            }
            if ($active === null && $state['steps'][5]['status'] === 'SUCCEEDED') {
                $verification = $state['verification']['status'];
                $safeComplete = count(array_filter(array_slice($state['steps'], 0, 5), static fn (array $step): bool => $step['status'] === 'SUCCEEDED')) === 5;
                if (in_array($verification, ['RUNNING', 'FAILED'], true)) {
                    $status = $verification === 'RUNNING' ? 'uncertain' : 'failed';
                    $active = 'database_verify';
                } elseif ($safeComplete) {
                    $status = 'complete';
                }
            }
        }

        return [
            'mode' => 'candidate-checkout-install',
            'status' => $status,
            'active_step' => $active,
            'steps' => $state['steps'] ?? [],
            'verification' => $state['verification'] ?? $this->verification(),
            'requires_manual_reconciliation' => $manual,
            'qualified_release' => false,
        ];
    }

    private function inspectDirectories(): bool
    {
        $this->assertAncestorDirectories($this->coreRoot);
        $this->assertDirectory($this->coreRoot, false);
        foreach ([$this->coreRoot.'/var', $this->directory] as $directory) {
            if ($this->metadata($directory) === null) {
                return false;
            }
            $this->assertDirectory($directory, $directory === $this->directory);
        }

        return true;
    }

    private function prepareDirectories(): void
    {
        $this->assertAncestorDirectories($this->coreRoot);
        $this->assertDirectory($this->coreRoot, false);
        foreach ([$this->coreRoot.'/var', $this->directory] as $directory) {
            if ($this->metadata($directory) === null) {
                $previous = umask(0077);
                try {
                    $created = @mkdir($directory, 0700);
                } finally {
                    umask($previous);
                }
                if (!$created && $this->metadata($directory) === null) {
                    throw new \RuntimeException('The private installer directory could not be created.');
                }
                if ($created) {
                    $this->syncDirectory(dirname($directory));
                }
            }
            $this->assertDirectory($directory, $directory === $this->directory);
        }
    }

    /** @return resource */
    private function openLock()
    {
        $exists = $this->metadata($this->lockPath) !== null;
        if ($exists) {
            $this->assertRegularFile($this->lockPath);
        }
        $previous = umask(0077);
        try {
            $handle = @fopen($this->lockPath, $exists ? 'r+b' : 'x+b');
        } finally {
            umask($previous);
        }
        // Another fresh caller may have created the same lock. Open only after validation.
        if ($handle === false && !$exists && $this->metadata($this->lockPath) !== null) {
            $this->assertRegularFile($this->lockPath);
            $handle = @fopen($this->lockPath, 'r+b');
        }
        if ($handle === false) {
            throw new \RuntimeException('The installer lock could not be opened.');
        }
        try {
            $this->assertHandleIdentity($this->lockPath, $handle);
        } catch (\Throwable $error) {
            fclose($handle);
            throw $error;
        }

        return $handle;
    }

    /** @return array{schema: string, binding: string, steps: list<array{id: string, status: string, exit_code: int|null}>, verification: array{status: string, exit_code: int|null}} */
    private function readState(): array
    {
        $metadata = $this->assertRegularFile($this->statePath);
        if ($metadata['size'] > self::MAX_BYTES) {
            throw new \RuntimeException('The installation journal exceeds its size limit.');
        }
        $handle = @fopen($this->statePath, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('The installation journal could not be read.');
        }
        try {
            $this->assertHandleIdentity($this->statePath, $handle);
            $bytes = stream_get_contents($handle, self::MAX_BYTES + 1);
        } finally {
            fclose($handle);
        }
        if ($bytes === false || strlen($bytes) > self::MAX_BYTES) {
            throw new \RuntimeException('The installation journal exceeds its size limit.');
        }
        try {
            $state = json_decode($bytes, true, 16, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new \RuntimeException('The installation journal is malformed.');
        }
        if (!$this->validState($state)) {
            throw new \RuntimeException('The installation journal schema or step order is invalid.');
        }

        return $state;
    }

    /** @phpstan-assert-if-true array{schema: string, binding: string, steps: list<array{id: string, status: string, exit_code: int|null}>, verification: array{status: string, exit_code: int|null}} $state */
    private function validState(mixed $state): bool
    {
        if (!is_array($state) || !$this->keys($state, ['schema', 'binding', 'steps', 'verification'])
            || $state['schema'] !== self::SCHEMA || !is_string($state['binding']) || preg_match('/\A[a-f0-9]{64}\z/D', $state['binding']) !== 1
            || !is_array($state['steps']) || !array_is_list($state['steps']) || count($state['steps']) !== count(self::STEPS)
            || !$this->validRecord($state['verification'], false)) {
            return false;
        }
        $stopped = false;
        foreach ($state['steps'] as $index => $record) {
            if (!$this->validRecord($record, true) || ($record['id'] ?? null) !== self::STEPS[$index]) {
                return false;
            }
            // The successful migration marker survives regeneration of safe outputs.
            if ($index === 5 && $record['status'] === 'SUCCEEDED') {
                continue;
            }
            if ($stopped && $record['status'] !== 'PENDING') {
                return false;
            }
            if ($record['status'] !== 'SUCCEEDED') {
                $stopped = true;
            }
        }
        if ($state['verification']['status'] !== 'PENDING') {
            foreach ($state['steps'] as $record) {
                if ($record['status'] !== 'SUCCEEDED') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param array<array-key, mixed> $value
     * @param list<string> $keys
     */
    private function keys(array $value, array $keys): bool
    {
        $actual = array_keys($value);
        sort($actual);
        sort($keys);

        return $actual === $keys;
    }

    /** @phpstan-assert-if-true array{id?: string, status: string, exit_code: int|null} $record */
    private function validRecord(mixed $record, bool $step): bool
    {
        if (!is_array($record) || !$this->keys($record, $step ? ['id', 'status', 'exit_code'] : ['status', 'exit_code'])
            || ($step && !is_string($record['id'])) || !in_array($record['status'], ['PENDING', 'RUNNING', 'SUCCEEDED', 'FAILED'], true)) {
            return false;
        }

        return match ($record['status']) {
            'PENDING', 'RUNNING' => $record['exit_code'] === null,
            'SUCCEEDED' => $record['exit_code'] === 0,
            'FAILED' => is_int($record['exit_code']) && $record['exit_code'] >= 1 && $record['exit_code'] <= 255,
        };
    }

    /** @param array{schema: string, binding: string, steps: list<array{id: string, status: string, exit_code: int|null}>, verification: array{status: string, exit_code: int|null}} $state */
    private function writeState(array $state): void
    {
        $this->inspectDirectories();
        if ($this->metadata($this->statePath) !== null) {
            $this->assertRegularFile($this->statePath);
        }
        $bytes = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
        if (strlen($bytes) > self::MAX_BYTES) {
            throw new \RuntimeException('The installation journal exceeds its size limit.');
        }
        $temporary = $this->directory.'/.candidate-install-'.bin2hex(random_bytes(12)).'.tmp';
        $previous = umask(0077);
        try {
            $handle = @fopen($temporary, 'x+b');
        } finally {
            umask($previous);
        }
        if ($handle === false) {
            throw new \RuntimeException('The installation checkpoint could not be staged.');
        }
        try {
            $this->assertHandleIdentity($temporary, $handle);
            $remaining = $bytes;
            while ($remaining !== '') {
                $written = fwrite($handle, $remaining);
                if ($written === false || $written === 0) {
                    throw new \RuntimeException('The installation checkpoint could not be written.');
                }
                $remaining = substr($remaining, $written);
            }
            if (!fflush($handle) || !fsync($handle)) {
                throw new \RuntimeException('The installation checkpoint could not be synchronized.');
            }
            fclose($handle);
            $handle = null;
            if ($this->metadata($this->statePath) !== null) {
                $this->assertRegularFile($this->statePath);
            }
            if (!@rename($temporary, $this->statePath)) {
                throw new \RuntimeException('The installation checkpoint could not be committed.');
            }
            $this->syncDirectory($this->directory);
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
            if ($this->metadata($temporary) !== null) {
                @unlink($temporary);
            }
        }
    }

    private function syncDirectory(string $directory): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return;
        }
        $handle = @fopen($directory, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('The installation checkpoint directory could not be synchronized.');
        }
        try {
            if (!fsync($handle)) {
                throw new \RuntimeException('The installation checkpoint directory could not be synchronized.');
            }
        } finally {
            fclose($handle);
        }
    }

    private function assertAncestorDirectories(string $path): void
    {
        $current = str_starts_with($path, '/') ? '/' : substr($path, 0, 3);
        $suffix = substr($path, strlen($current));
        foreach (explode('/', $suffix) as $component) {
            if ($component === '') {
                continue;
            }
            $current = rtrim($current, '/').'/'.$component;
            $metadata = $this->metadata($current);
            if ($metadata === null || ($metadata['mode'] & 0170000) !== 0040000 || is_link($current)) {
                throw new \RuntimeException('Installer path ancestors must be existing directories without links.');
            }
        }
    }

    private function assertDirectory(string $path, bool $private): void
    {
        $metadata = $this->metadata($path);
        if ($metadata === null || ($metadata['mode'] & 0170000) !== 0040000 || is_link($path)) {
            throw new \RuntimeException('Installer directories must be real directories without links.');
        }
        $this->assertOwner($metadata);
        if (PHP_OS_FAMILY !== 'Windows' && $private && ($metadata['mode'] & 0077) !== 0) {
            throw new \RuntimeException('The installer directory must be private to its owner.');
        }
        if (PHP_OS_FAMILY !== 'Windows' && $path === $this->coreRoot && ($metadata['mode'] & 0022) !== 0) {
            throw new \RuntimeException('The checkout must not be writable by other identities.');
        }
        if (PHP_OS_FAMILY !== 'Windows' && $path === $this->coreRoot.'/var' && ($metadata['mode'] & 0022) !== 0 && ($metadata['mode'] & 01000) === 0) {
            throw new \RuntimeException('A shared writable var directory requires the sticky bit.');
        }
    }

    /** @return array<string|int, int> */
    private function assertRegularFile(string $path): array
    {
        $metadata = $this->metadata($path);
        if ($metadata === null || ($metadata['mode'] & 0170000) !== 0100000 || is_link($path) || $metadata['nlink'] !== 1) {
            throw new \RuntimeException('Installer files must be regular files without links.');
        }
        $this->assertOwner($metadata);
        if (PHP_OS_FAMILY !== 'Windows' && ($metadata['mode'] & 0077) !== 0) {
            throw new \RuntimeException('Installer files must be private to their owner.');
        }

        return $metadata;
    }

    /** @param array<string|int, int> $metadata */
    private function assertOwner(array $metadata): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return;
        }
        if (!function_exists('posix_geteuid') || $metadata['uid'] !== posix_geteuid()) {
            throw new \RuntimeException('Installer paths must belong to the executing operator.');
        }
    }

    /** @param resource $handle */
    private function assertHandleIdentity(string $path, $handle): void
    {
        $metadata = $this->assertRegularFile($path);
        $opened = fstat($handle);
        if ($opened === false || $opened['dev'] !== $metadata['dev'] || $opened['ino'] !== $metadata['ino'] || $opened['nlink'] !== 1) {
            throw new \RuntimeException('The installer file identity changed.');
        }
    }

    /**
     * Observations can change after mkdir, fopen or another process's mutation.
     * @phpstan-impure
     * @return array<string|int, int>|null
     */
    private function metadata(string $path): ?array
    {
        clearstatcache(true, $path);
        $metadata = @lstat($path);

        return $metadata === false ? null : $metadata;
    }
}
