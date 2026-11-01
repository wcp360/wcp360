<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Platform;

/**
 * Bounded read-only observations, not installation admission or service discovery.
 * The memory/disk/network view belongs to this process; cgroup limits are not checked.
 * This standalone collector deliberately remains compatible with PHP 8.1.
 *
 * @phpstan-type MemoryObservation array{total_bytes: int, available_bytes: int}
 * @phpstan-type DiskObservation array{path: '/var', total_bytes: int, free_bytes: int}
 * @phpstan-type HostObservation array{scope: 'current_process_namespace', platform: string|null, architecture: string, effective_uid: int|null, memory: MemoryObservation|null, disk: DiskObservation|null, network: array{tcp4: list<int>|null, tcp6: list<int>|null, udp4: list<int>|null, udp6: list<int>|null}, managed_paths: array<string, 'absent'|'present'|'unknown'>, systemd_marker: bool|null, host_changes: false}
 */
final class LinuxHostInventory
{
    public const MANAGED_PATHS = ['/usr/lib/wcp360', '/var/lib/wcp360', '/run/wcp360', '/var/cache/wcp360'];
    private const MAX_BYTES = 2097152;
    private const NETWORK_HEADER = ['sl', 'local_address', 'rem_address', 'st', 'tx_queue', 'rx_queue', 'tr', 'tm->when', 'retrnsmt', 'uid', 'timeout', 'inode'];

    /** @return HostObservation */
    public static function collect(): array
    {
        $linux = PHP_OS_FAMILY === 'Linux';
        $platform = null;
        if ($linux) {
            try {
                $profile = TargetPlatform::detectHost();
                $platform = $profile['id'] ?? null;
            } catch (\RuntimeException) {
                // An unreadable/invalid catalogue cannot establish a supported host.
            }
        }
        $memory = $linux ? self::readBounded('/proc/meminfo') : null;
        $status = $linux ? self::readBounded('/proc/self/status') : null;
        $network = [];
        foreach (['tcp4' => 'tcp', 'tcp6' => 'tcp6', 'udp4' => 'udp', 'udp6' => 'udp6'] as $key => $file) {
            $content = $linux ? self::readBounded('/proc/net/'.$file) : null;
            $network[$key] = $content === null ? null : self::parseNetwork($content, substr($key, 0, 3), substr($key, -1));
        }
        $paths = [];
        foreach (self::MANAGED_PATHS as $path) {
            $paths[$path] = $linux ? self::pathPresence($path) : 'unknown';
        }
        $systemd = $linux ? self::pathPresence('/run/systemd/system') : 'unknown';

        return [
            'scope' => 'current_process_namespace',
            'platform' => $platform,
            'architecture' => php_uname('m'),
            'effective_uid' => $status === null ? null : self::effectiveUid($status),
            'memory' => $memory === null ? null : self::parseMemory($memory),
            'disk' => $linux ? self::disk() : null,
            'network' => [
                'tcp4' => $network['tcp4'], 'tcp6' => $network['tcp6'],
                'udp4' => $network['udp4'], 'udp6' => $network['udp6'],
            ],
            'managed_paths' => $paths,
            // The directory is only an observed marker, never proof of an active manager.
            'systemd_marker' => $systemd === 'unknown' ? null : ($systemd === 'present' && is_dir('/run/systemd/system')),
            'host_changes' => false,
        ];
    }

    /** @return MemoryObservation|null */
    public static function parseMemory(string $content): ?array
    {
        if (!self::validText($content)) {
            return null;
        }
        $values = [];
        foreach (explode("\n", substr($content, 0, -1)) as $line) {
            if (preg_match('/\A([A-Za-z][A-Za-z0-9_()]*):[ \t]+([0-9]+)(?:[ \t]+(kB))?[ \t]*\z/D', $line, $match) !== 1
                || array_key_exists($match[1], $values)) {
                return null;
            }
            $values[$match[1]] = [$match[2], $match[3] ?? null];
        }
        if (!isset($values['MemTotal'], $values['MemAvailable'])
            || $values['MemTotal'][1] !== 'kB' || $values['MemAvailable'][1] !== 'kB') {
            return null;
        }
        $total = self::decimal($values['MemTotal'][0], intdiv(PHP_INT_MAX, 1024));
        $available = self::decimal($values['MemAvailable'][0], intdiv(PHP_INT_MAX, 1024));
        if ($total === null || $total === 0 || $available === null || $available > $total) {
            return null;
        }

        return ['total_bytes' => $total * 1024, 'available_bytes' => $available * 1024];
    }

    /**
     * TCP includes only listeners; UDP includes all bound ports, including connected sockets.
     * Ports are conservatively occupied regardless of their local bind address.
     *
     * @return list<int>|null Null means unknown, not an empty socket table.
     */
    public static function parseNetwork(string $content, string $protocol, string $family): ?array
    {
        if (!in_array($protocol, ['tcp', 'udp'], true) || !in_array($family, ['4', '6'], true) || !self::validText($content)) {
            return null;
        }
        $lines = explode("\n", substr($content, 0, -1));
        $header = preg_split('/[ \t]+/', trim((string) array_shift($lines), " \t"));
        if (!is_array($header)) {
            return null;
        }
        if (($header[2] ?? '') === 'remote_address') {
            $header[2] = 'rem_address';
        }
        if (array_slice($header, 0, count(self::NETWORK_HEADER)) !== self::NETWORK_HEADER
            || !in_array(array_slice($header, count(self::NETWORK_HEADER)), $protocol === 'udp' ? [[], ['ref', 'pointer', 'drops']] : [[]], true)) {
            return null;
        }
        $addressLength = $family === '4' ? 8 : 32;
        $endpoint = '/\A[0-9A-Fa-f]{'.$addressLength.'}:([0-9A-Fa-f]{4})\z/D';
        $ports = [];
        foreach ($lines as $line) {
            if (strlen($line) > 2048) {
                return null;
            }
            $fields = preg_split('/[ \t]+/', trim($line, " \t"));
            if (!is_array($fields) || count($fields) < 10
                || preg_match('/\A[0-9]+:\z/D', $fields[0]) !== 1
                || preg_match($endpoint, $fields[1], $local) !== 1
                || preg_match($endpoint, $fields[2]) !== 1
                || preg_match('/\A[0-9A-Fa-f]{2}\z/D', $fields[3]) !== 1
                || preg_match('/\A[0-9A-Fa-f]{8}:[0-9A-Fa-f]{8}\z/D', $fields[4]) !== 1
                || preg_match('/\A[0-9A-Fa-f]{2}:[0-9A-Fa-f]{8}\z/D', $fields[5]) !== 1
                || preg_match('/\A[0-9A-Fa-f]{8}\z/D', $fields[6]) !== 1
                || self::decimal($fields[7], PHP_INT_MAX) === null
                || self::decimal($fields[8], PHP_INT_MAX) === null
                || self::decimal($fields[9], PHP_INT_MAX) === null) {
                return null;
            }
            foreach (array_slice($fields, 10) as $field) {
                if (preg_match('/\A-?[0-9A-Fa-f]+\z/D', $field) !== 1) {
                    return null;
                }
            }
            $port = (int) hexdec($local[1]);
            if ($port > 0 && ($protocol === 'udp' || strtoupper($fields[3]) === '0A')) {
                $ports[$port] = $port;
            }
        }
        sort($ports, SORT_NUMERIC);

        return $ports;
    }

    private static function validText(string $content): bool
    {
        return $content !== '' && strlen($content) <= self::MAX_BYTES && str_ends_with($content, "\n")
            && preg_match('/[^\x09\x0A\x20-\x7E]/', $content) !== 1;
    }

    private static function decimal(string $value, int $maximum): ?int
    {
        if (preg_match('/\A[0-9]+\z/D', $value) !== 1) {
            return null;
        }
        $value = ltrim($value, '0');
        $limit = (string) $maximum;
        if (strlen($value) > strlen($limit) || (strlen($value) === strlen($limit) && strcmp($value, $limit) > 0)) {
            return null;
        }

        return (int) $value;
    }

    private static function readBounded(string $path): ?string
    {
        $stream = @fopen($path, 'rb');
        if ($stream === false) {
            return null;
        }
        try {
            $content = stream_get_contents($stream, self::MAX_BYTES + 1);

            return is_string($content) && strlen($content) <= self::MAX_BYTES && feof($stream) ? $content : null;
        } finally {
            fclose($stream);
        }
    }

    private static function effectiveUid(string $status): ?int
    {
        if (!self::validText($status) || preg_match_all('/^Uid:[ \t]+([0-9]+)[ \t]+([0-9]+)[ \t]+([0-9]+)[ \t]+([0-9]+)[ \t]*$/m', $status, $matches) !== 1) {
            return null;
        }

        return self::decimal($matches[2][0], PHP_INT_MAX);
    }

    /** @return DiskObservation|null */
    private static function disk(): ?array
    {
        $total = @disk_total_space('/var');
        $free = @disk_free_space('/var');
        if (!is_float($total) || !is_float($free) || !is_finite($total) || !is_finite($free)
            || $total <= 0 || $free < 0 || $free > $total || $total >= PHP_INT_MAX || $free >= PHP_INT_MAX) {
            return null;
        }

        return ['path' => '/var', 'total_bytes' => (int) $total, 'free_bytes' => (int) $free];
    }

    /** @return 'absent'|'present'|'unknown' */
    private static function pathPresence(string $path): string
    {
        clearstatcache(true, $path);
        if (@lstat($path) !== false) {
            return 'present';
        }
        $parent = dirname($path);
        if (is_dir($parent) && is_readable($parent) && is_executable($parent)) {
            return 'absent';
        }
        // A missing intermediate directory also establishes absence, but an
        // existing inaccessible/file/link parent must remain unknown.
        if ($parent !== $path && @lstat($parent) === false && self::pathPresence($parent) === 'absent') {
            return 'absent';
        }

        return 'unknown';
    }
}
