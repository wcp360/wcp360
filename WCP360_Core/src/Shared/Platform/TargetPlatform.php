<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Platform;

/**
 * Read-only target recognition; it does not qualify runtimes or authorize host changes.
 * Kept compatible with PHP 8.1 so native distribution probes need no Composer tree.
 *
 * @phpstan-type TargetProfile array{id: string, os_id: string, version: string, codename: string, image: string, native_php_series: string, architecture: string, package_manager: string}
 */
final class TargetPlatform
{
    private const PROFILE_KEYS = ['id', 'os_id', 'version', 'codename', 'image', 'native_php_series', 'architecture', 'package_manager'];
    private const TARGET_IDS = ['debian-12', 'debian-13', 'ubuntu-22.04', 'ubuntu-24.04', 'ubuntu-26.04'];

    /** @return list<TargetProfile> */
    public static function profiles(?string $cataloguePath = null): array
    {
        $path = $cataloguePath ?? dirname(__DIR__, 3).'/config/platforms/targets.json';
        $content = @file_get_contents($path, false, null, 0, 16385);
        if (!is_string($content) || strlen($content) > 16384) {
            throw new \RuntimeException('Target platform catalogue is unavailable or oversized.');
        }
        try {
            $catalogue = json_decode($content, true, 16, JSON_THROW_ON_ERROR);
        } catch (\JsonException $error) {
            throw new \RuntimeException('Target platform catalogue is invalid.', 0, $error);
        }
        if (!is_array($catalogue)
            || !self::hasExactKeys($catalogue, ['schema', 'core_php_series', 'core_postgresql_major', 'targets'])
            || $catalogue['schema'] !== 'wcp360.target-platforms/1'
            || $catalogue['core_php_series'] !== '8.4'
            || $catalogue['core_postgresql_major'] !== 17
            || !is_array($catalogue['targets'])
            || !array_is_list($catalogue['targets'])
            || count($catalogue['targets']) !== count(self::TARGET_IDS)) {
            throw new \RuntimeException('Target platform catalogue schema is invalid.');
        }
        $profiles = [];
        $seen = [];
        foreach ($catalogue['targets'] as $profile) {
            if (!is_array($profile) || !self::hasExactKeys($profile, self::PROFILE_KEYS)) {
                throw new \RuntimeException('Target platform profile schema is invalid.');
            }
            foreach (self::PROFILE_KEYS as $key) {
                if (!is_string($profile[$key]) || preg_match('/\A[a-z0-9][a-z0-9.:-]*\z/D', $profile[$key]) !== 1) {
                    throw new \RuntimeException('Target platform profile contains an invalid value.');
                }
            }
            if (!in_array($profile['id'], self::TARGET_IDS, true)
                || $profile['id'] !== $profile['os_id'].'-'.$profile['version']
                || isset($seen[$profile['id']])
                || $profile['architecture'] !== 'amd64'
                || $profile['package_manager'] !== 'apt'
                || preg_match('/\A8\.[0-9]+\z/D', $profile['native_php_series']) !== 1
                || $profile['image'] !== $profile['os_id'].':'.$profile['version'].($profile['os_id'] === 'debian' ? '-slim' : '')) {
                throw new \RuntimeException('Target platform profile identity is invalid.');
            }
            $seen[$profile['id']] = true;
            // These fields remain descriptive data; none becomes a shell command or executable path.
            $profiles[] = [
                'id' => (string) $profile['id'],
                'os_id' => (string) $profile['os_id'],
                'version' => (string) $profile['version'],
                'codename' => (string) $profile['codename'],
                'image' => (string) $profile['image'],
                'native_php_series' => (string) $profile['native_php_series'],
                'architecture' => (string) $profile['architecture'],
                'package_manager' => (string) $profile['package_manager'],
            ];
        }

        return $profiles;
    }

    /** @return TargetProfile|null */
    public static function detect(string $osRelease, ?string $cataloguePath = null): ?array
    {
        $identity = self::parseRelease($osRelease);
        if ($identity === null || !isset($identity['ID'], $identity['VERSION_ID'])) {
            return null;
        }
        foreach (self::profiles($cataloguePath) as $profile) {
            if ($profile['os_id'] === $identity['ID'] && $profile['version'] === $identity['VERSION_ID']) {
                return $profile;
            }
        }

        return null;
    }

    /**
     * /etc takes precedence even when unreadable or invalid: never merge or silently replace it.
     * Optional paths permit deterministic read-only tests; the CLI exposes no path override.
     *
     * @return TargetProfile|null
     */
    public static function detectHost(?string $etcPath = null, ?string $fallbackPath = null): ?array
    {
        $etcPath = $etcPath ?? '/etc/os-release';
        $fallbackPath = $fallbackPath ?? '/usr/lib/os-release';
        $path = file_exists($etcPath) || is_link($etcPath) ? $etcPath : $fallbackPath;
        $release = @file_get_contents($path, false, null, 0, 65537);

        return is_string($release) && strlen($release) <= 65536 ? self::detect($release) : null;
    }

    /** @return array<string, string>|null */
    private static function parseRelease(string $release): ?array
    {
        $release = str_replace("\r\n", "\n", $release);
        if (strlen($release) > 65536 || preg_match('//u', $release) !== 1 || preg_match('/[\x00-\x09\x0B-\x1F\x7F]/', $release) === 1) {
            return null;
        }
        $values = [];
        foreach (explode("\n", $release) as $line) {
            $line = trim($line, ' ');
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            if (preg_match('/\A([A-Z][A-Z0-9_]*)=(.*)\z/Du', $line, $match) !== 1 || array_key_exists($match[1], $values)) {
                return null;
            }
            $value = self::decodeValue($match[2]);
            if ($value === null) {
                return null;
            }
            $values[$match[1]] = $value;
        }

        return $values;
    }

    private static function decodeValue(string $value): ?string
    {
        if (str_starts_with($value, '"')) {
            if (preg_match('/\A"((?:[^"\\\\]|\\\\["\\\\$`])*)"\z/Du', $value, $match) !== 1) {
                return null;
            }

            return preg_replace('/\\\\(["\\\\$`])/', '$1', $match[1]);
        }
        if (str_starts_with($value, "'")) {
            return preg_match("/\\A'([^']*)'\\z/Du", $value, $match) === 1 ? $match[1] : null;
        }

        return preg_match('/\A[^\s\'"\\\\`$;|&()<>#]*\z/Du', $value) === 1 ? $value : null;
    }

    /**
     * @param array<array-key, mixed> $value
     * @param list<string> $expected
     */
    private static function hasExactKeys(array $value, array $expected): bool
    {
        $keys = array_keys($value);
        sort($keys);
        sort($expected);

        return $keys === $expected;
    }
}
