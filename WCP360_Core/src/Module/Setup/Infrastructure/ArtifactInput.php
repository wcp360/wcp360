<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

/** Read-only observations of local artifact bytes; never a future-consumption guarantee. */
final class ArtifactInput
{
    public const MAX_ARTIFACT_BYTES = 2147483648;

    private const CHUNK_BYTES = 65536;
    private const ERROR = 'Candidate artifact input is unavailable or unsafe.';
    private const IDENTITY_KEYS = ['dev', 'ino', 'mode', 'uid', 'nlink', 'size', 'mtime', 'ctime'];

    public static function read(string $path, int $maxBytes): string
    {
        if ($maxBytes < 1 || $maxBytes > 16384) {
            throw new \RuntimeException(self::ERROR);
        }
        [$handle, $metadata, $ancestors] = self::open($path, $maxBytes, false);
        try {
            $bytes = @stream_get_contents($handle, $maxBytes + 1);
            if (!is_string($bytes) || strlen($bytes) !== $metadata['size'] || strlen($bytes) > $maxBytes || !feof($handle)) {
                throw new \RuntimeException(self::ERROR);
            }
            self::finish($path, $handle, $metadata, $ancestors);

            return $bytes;
        } finally {
            @flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    public static function sha256(string $path, int $expectedSize): string
    {
        if ($expectedSize < 1 || $expectedSize > self::MAX_ARTIFACT_BYTES) {
            throw new \RuntimeException(self::ERROR);
        }
        [$handle, $metadata, $ancestors] = self::open($path, $expectedSize, true);
        try {
            $hash = hash_init('sha256');
            $count = 0;
            while (!feof($handle)) {
                $chunk = @fread($handle, self::CHUNK_BYTES);
                if ($chunk === false || ($chunk === '' && !feof($handle))) {
                    throw new \RuntimeException(self::ERROR);
                }
                $count += strlen($chunk);
                if ($count > $expectedSize) {
                    throw new \RuntimeException(self::ERROR);
                }
                hash_update($hash, $chunk);
            }
            if ($count !== $expectedSize) {
                throw new \RuntimeException(self::ERROR);
            }
            self::finish($path, $handle, $metadata, $ancestors);

            return hash_final($hash);
        } finally {
            @flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    /**
     * @return array{resource, array<string|int, int>, array<string, array<string|int, int>>}
     */
    private static function open(string &$path, int $limit, bool $exact): array
    {
        $path = self::localPath($path);
        $uid = PHP_OS_FAMILY === 'Windows' ? null : self::effectiveUid();
        $ancestors = self::ancestors($path, $uid);
        $metadata = self::regular($path, $uid);
        if ($metadata['size'] < 0 || ($exact ? $metadata['size'] !== $limit : $metadata['size'] > $limit)) {
            throw new \RuntimeException(self::ERROR);
        }
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException(self::ERROR);
        }
        try {
            if (!@flock($handle, LOCK_SH | LOCK_NB) || !self::same($metadata, @fstat($handle))) {
                throw new \RuntimeException(self::ERROR);
            }
            self::finish($path, $handle, $metadata, $ancestors);
        } catch (\Throwable) {
            @flock($handle, LOCK_UN);
            fclose($handle);
            throw new \RuntimeException(self::ERROR);
        }

        return [$handle, $metadata, $ancestors];
    }

    private static function localPath(string $path): string
    {
        if ($path === '' || strlen($path) > 4096 || preg_match('/[\x00-\x1f\x7f]/', $path) === 1) {
            throw new \RuntimeException(self::ERROR);
        }
        if (PHP_OS_FAMILY === 'Windows' && preg_match('~\A[A-Za-z]:[/\\\\]~D', $path) === 1) {
            $path = str_replace('\\', '/', $path);
            $suffix = substr($path, 3);
        } elseif (PHP_OS_FAMILY !== 'Windows' && str_starts_with($path, '/') && !str_starts_with($path, '//') && !str_contains($path, '\\')) {
            $suffix = substr($path, 1);
        } else {
            throw new \RuntimeException(self::ERROR);
        }
        if (str_contains($suffix, ':')) {
            throw new \RuntimeException(self::ERROR);
        }
        foreach (explode('/', $suffix) as $component) {
            if ($component === '' || $component === '.' || $component === '..') {
                throw new \RuntimeException(self::ERROR);
            }
        }

        return $path;
    }

    /** @return array<string, array<string|int, int>> */
    private static function ancestors(string $path, ?int $uid): array
    {
        $current = str_starts_with($path, '/') ? '/' : substr($path, 0, 3);
        $parent = str_replace('\\', '/', dirname($path));
        $directories = [$current];
        foreach (explode('/', substr($parent, strlen($current))) as $component) {
            if ($component !== '') {
                $current = rtrim($current, '/').'/'.$component;
                $directories[] = $current;
            }
        }
        $observations = [];
        foreach ($directories as $directory) {
            $metadata = self::metadata($directory);
            if ($metadata === false || ($metadata['mode'] & 0170000) !== 0040000 || is_link($directory)) {
                throw new \RuntimeException(self::ERROR);
            }
            if ($uid !== null && (!in_array($metadata['uid'], [0, $uid], true)
                || (($metadata['mode'] & 0022) !== 0 && ($directory === $parent || ($metadata['mode'] & 01000) === 0)))) {
                throw new \RuntimeException(self::ERROR);
            }
            $observations[$directory] = $metadata;
        }

        return $observations;
    }

    /** @return array<string|int, int> */
    private static function regular(string $path, ?int $uid): array
    {
        $metadata = self::metadata($path);
        if ($metadata === false || ($metadata['mode'] & 0170000) !== 0100000 || is_link($path) || $metadata['nlink'] !== 1
            || ($uid !== null && (!in_array($metadata['uid'], [0, $uid], true) || ($metadata['mode'] & 0022) !== 0))) {
            throw new \RuntimeException(self::ERROR);
        }

        return $metadata;
    }

    /**
     * @param resource $handle
     * @param array<string|int, int> $metadata
     * @param array<string, array<string|int, int>> $ancestors
     */
    private static function finish(string $path, $handle, array $metadata, array $ancestors): void
    {
        if (!self::same($metadata, @fstat($handle)) || !self::same($metadata, self::metadata($path)) || is_link($path)) {
            throw new \RuntimeException(self::ERROR);
        }
        foreach ($ancestors as $directory => $before) {
            $after = self::metadata($directory);
            // Directory contents may legitimately change; their identity and protection must not.
            if ($after === false || is_link($directory) || $before['dev'] !== $after['dev'] || $before['ino'] !== $after['ino']
                || $before['mode'] !== $after['mode'] || $before['uid'] !== $after['uid']) {
                throw new \RuntimeException(self::ERROR);
            }
        }
    }

    /**
     * @param array<string|int, int> $before
     * @param array<string|int, int>|false $after
     */
    private static function same(array $before, array|false $after): bool
    {
        if ($after === false) {
            return false;
        }
        foreach (self::IDENTITY_KEYS as $key) {
            if ($before[$key] !== $after[$key]) {
                return false;
            }
        }

        return true;
    }

    private static function effectiveUid(): int
    {
        if (function_exists('posix_geteuid')) {
            return posix_geteuid();
        }
        // Native PHP may omit ext-posix; Linux exposes the effective UID as the second value.
        $stream = @fopen('/proc/self/status', 'rb');
        if ($stream === false) {
            throw new \RuntimeException(self::ERROR);
        }
        try {
            $bytes = @stream_get_contents($stream, 8193);
        } finally {
            fclose($stream);
        }
        if (!is_string($bytes) || strlen($bytes) > 8192 || preg_match('/^Uid:\s+[0-9]+\s+([0-9]+)\s+[0-9]+\s+[0-9]+\s*$/mD', $bytes, $match) !== 1
            || strlen($match[1]) > 10 || (float) $match[1] > PHP_INT_MAX) {
            throw new \RuntimeException(self::ERROR);
        }

        return (int) $match[1];
    }

    /** @return array<string|int, int>|false */
    private static function metadata(string $path): array|false
    {
        clearstatcache(true, $path);

        return @lstat($path);
    }
}
