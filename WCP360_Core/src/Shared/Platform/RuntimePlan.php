<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Platform;

/** Read-only development runtime selection; compatible with native PHP 8.1. */
final class RuntimePlan
{
    /**
     * @param array<string, mixed> $platform A profile returned by TargetPlatform.
     * @return array<string, mixed>
     */
    public static function forPlatform(array $platform, ?string $providerCataloguePath = null): array
    {
        $trusted = null;
        foreach (TargetPlatform::profiles() as $profile) {
            if ($profile === $platform) {
                $trusted = $profile;
                break;
            }
        }
        if ($trusted === null) {
            throw new \RuntimeException('A recognised target platform is required.');
        }
        $providers = self::providers($providerCataloguePath);
        $prefix = $providers['php']['prefix'];

        return [
            'schema' => 'wcp360.candidate-runtime-plan/1',
            'platform' => $trusted['id'],
            'architecture' => $trusted['architecture'],
            'package_manager' => $trusted['package_manager'],
            'native_php_series' => $trusted['native_php_series'],
            'native_core_php_matches' => $trusted['native_php_series'] === '8.4',
            'php' => $providers['php'] + [
                'cli_binary' => $prefix.'/bin/php',
                'fpm_binary' => $prefix.'/sbin/php-fpm',
                'fpm_configuration' => $prefix.'/etc/php-fpm.conf',
                'fpm_pool_directory' => $prefix.'/etc/php-fpm.d',
                'service_profile' => 'not_installed',
                'native_packages_modified' => false,
            ],
            'postgresql' => $providers['postgresql'] + [
                'local_service_installed' => false,
                'connection_must_be_verified' => true,
            ],
            'host_changes' => false,
            'runtime_validation' => 'not_checked',
            'qualified_release' => false,
        ];
    }

    /** @return array<string, mixed>|null */
    public static function forHost(): ?array
    {
        $platform = PHP_OS_FAMILY === 'Linux' ? TargetPlatform::detectHost() : null;

        return $platform === null ? null : self::forPlatform($platform);
    }

    /**
     * @return array{php: array{version: string, series: string, source_url: string, sha256: string, prefix: string, status: string}, postgresql: array{major: int, image: string, mode: string}}
     */
    private static function providers(?string $path): array
    {
        $content = @file_get_contents($path ?? dirname(__DIR__, 3).'/config/platforms/runtime-providers.json');
        if (!is_string($content) || strlen($content) > 8192) {
            throw new \RuntimeException('Runtime provider catalogue is unavailable or oversized.');
        }
        try {
            $catalogue = json_decode($content, true, 16, JSON_THROW_ON_ERROR);
        } catch (\JsonException $error) {
            throw new \RuntimeException('Runtime provider catalogue is invalid.', 0, $error);
        }
        // The manifest owns the patch/hash pin. Its fields never select arbitrary commands.
        if (!is_array($catalogue)
            || !self::hasKeys($catalogue, ['schema', 'php', 'postgresql'])
            || $catalogue['schema'] !== 'wcp360.candidate-runtime-providers/1'
            || !is_array($catalogue['php'])
            || !self::hasKeys($catalogue['php'], ['version', 'series', 'source_url', 'sha256', 'prefix', 'status'])
            || !is_array($catalogue['postgresql'])
            || !self::hasKeys($catalogue['postgresql'], ['major', 'image', 'mode'])) {
            throw new \RuntimeException('Runtime provider catalogue does not match the reviewed development profile.');
        }
        $php = $catalogue['php'];
        $postgresql = $catalogue['postgresql'];
        foreach ($php as $value) {
            if (!is_string($value)) {
                throw new \RuntimeException('Runtime provider catalogue does not match the reviewed development profile.');
            }
        }
        if (preg_match('/\A8\.4\.(?:0|[1-9][0-9]{0,2})\z/D', $php['version']) !== 1
            || $php['series'] !== '8.4'
            || $php['source_url'] !== 'https://www.php.net/distributions/php-'.$php['version'].'.tar.xz'
            || preg_match('/\A[a-f0-9]{64}\z/D', $php['sha256']) !== 1
            || $php['prefix'] !== '/opt/wcp360/runtime/php/8.4'
            || $php['status'] !== 'development-source-build'
            || $postgresql['major'] !== 17
            || $postgresql['image'] !== 'postgres:17-trixie'
            || $postgresql['mode'] !== 'isolated-verification-service') {
            throw new \RuntimeException('Runtime provider catalogue does not match the reviewed development profile.');
        }

        return [
            'php' => [
                'version' => (string) $php['version'],
                'series' => (string) $php['series'],
                'source_url' => (string) $php['source_url'],
                'sha256' => (string) $php['sha256'],
                'prefix' => (string) $php['prefix'],
                'status' => (string) $php['status'],
            ],
            'postgresql' => ['major' => 17, 'image' => 'postgres:17-trixie', 'mode' => 'isolated-verification-service'],
        ];
    }

    /**
     * @param array<array-key, mixed> $actual
     * @param list<string> $expected
     */
    private static function hasKeys(array $actual, array $expected): bool
    {
        $keys = array_keys($actual);
        sort($keys);
        sort($expected);

        return $keys === $expected;
    }
}
