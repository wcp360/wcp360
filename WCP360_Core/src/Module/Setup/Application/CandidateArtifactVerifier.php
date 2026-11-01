<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Application;

use Wcp360\Core\Module\Setup\Infrastructure\ArtifactInput;
use Wcp360\Core\Shared\Platform\TargetPlatform;

/**
 * Operator-pinned candidate evidence, not an approved release trust policy.
 * Standalone PHP 8.1: no application bootstrap, download or installation.
 *
 * @phpstan-type ArtifactPaths array{artifact: string, manifest: string, signature: string}
 * @phpstan-type ExpectedArtifact array{public_key: string, component: string, version: string, target: string}
 * @phpstan-type ArtifactMetadata array{schema: string, component: string, version: string, target: string, architecture: string, size: int, sha256: string}
 */
final class CandidateArtifactVerifier
{
    private const FLAGS = ['artifact', 'manifest', 'signature', 'public-key', 'component', 'version', 'target'];
    private const COMPONENTS = ['core', 'wcpd', 'wcp-privd'];
    private const MANIFEST_KEYS = ['schema', 'component', 'version', 'target', 'architecture', 'size', 'sha256'];

    /**
     * Parsing deliberately does not read a catalogue, a key store or host state.
     * @param list<string> $arguments
     * @return array{paths: ArtifactPaths, expected: ExpectedArtifact}
     */
    public static function parseArguments(array $arguments): array
    {
        $values = [];
        if (count($arguments) !== count(self::FLAGS)) {
            throw new \InvalidArgumentException('Invalid candidate artifact arguments.');
        }
        foreach ($arguments as $argument) {
            if (strlen($argument) > 4120 || preg_match('/\A--([a-z-]+)=([^\r\n\x00]+)\z/D', $argument, $match) !== 1
                || !in_array($match[1], self::FLAGS, true) || isset($values[$match[1]])) {
                throw new \InvalidArgumentException('Invalid candidate artifact arguments.');
            }
            $values[$match[1]] = $match[2];
        }
        if (!isset($values['artifact'], $values['manifest'], $values['signature'], $values['public-key'], $values['component'], $values['version'], $values['target'])) {
            throw new \InvalidArgumentException('Invalid candidate artifact arguments.');
        }
        $paths = ['artifact' => $values['artifact'], 'manifest' => $values['manifest'], 'signature' => $values['signature']];
        $expected = ['public_key' => $values['public-key'], 'component' => $values['component'], 'version' => $values['version'], 'target' => $values['target']];
        if (!self::validInputs($paths, $expected)) {
            throw new \InvalidArgumentException('Invalid candidate artifact arguments.');
        }

        return ['paths' => $paths, 'expected' => $expected];
    }

    /**
     * A valid signature authenticates only these observed bytes under the caller's
     * explicit key pin. A future installer must consume independently reverified,
     * protected bytes and apply the accepted publisher/revocation/freshness policy.
     * @param ArtifactPaths $paths
     * @param ExpectedArtifact $expected
     * @return array<string, mixed>
     */
    public static function verify(array $paths, array $expected): array
    {
        try {
            if (!self::validInputs($paths, $expected)) {
                return self::refused('invalid_inputs');
            }
            if (!function_exists('sodium_crypto_sign_verify_detached')) {
                return self::refused('crypto_unavailable');
            }
            $knownTarget = false;
            foreach (TargetPlatform::profiles() as $profile) {
                $knownTarget = $knownTarget || $profile['id'] === $expected['target'];
            }
            if (!$knownTarget) {
                return self::refused('target_unrecognized');
            }
            $key = hex2bin($expected['public_key']);
            if ($key === false || strlen($key) !== 32) {
                return self::refused('invalid_inputs');
            }
            $manifest = ArtifactInput::read($paths['manifest'], 16384);
            $signature = ArtifactInput::read($paths['signature'], 64);
            // Ordinary Ed25519 over exact bounded bytes; this is not Ed25519ph.
            if (strlen($signature) !== 64 || !sodium_crypto_sign_verify_detached($signature, $manifest, $key)) {
                return self::refused('signature_invalid');
            }
            $metadata = self::metadata($manifest);
            if ($metadata === null) {
                return self::refused('manifest_invalid');
            }
            foreach (['component', 'version', 'target'] as $field) {
                if ($metadata[$field] !== $expected[$field]) {
                    return self::refused('identity_mismatch');
                }
            }
            $digest = ArtifactInput::sha256($paths['artifact'], $metadata['size']);
            if (!hash_equals($metadata['sha256'], $digest)) {
                return self::refused('payload_mismatch');
            }

            return self::boundaries() + [
                'candidate_verified' => true,
                'failure' => null,
                'authenticated_metadata' => $metadata,
                'signing_key_sha256' => hash('sha256', $key),
                'manifest_sha256' => hash('sha256', $manifest),
                'payload_sha256' => $digest,
                'checks' => ['detached_signature' => true, 'expected_identity' => true, 'payload_size' => true, 'payload_sha256' => true, 'observed_file_identity' => true],
            ];
        } catch (\Throwable) {
            // Do not disclose paths, supplied contents, exception text or keys.
            return self::refused('verification_unavailable_or_unsafe');
        }
    }

    /** @return array<string, mixed> */
    private static function refused(string $failure): array
    {
        return self::boundaries() + ['candidate_verified' => false, 'failure' => $failure];
    }

    /** @return array<string, mixed> */
    private static function boundaries(): array
    {
        return [
            'schema' => 'wcp360.candidate-artifact-verification/1',
            'scope' => 'operator_pinned_offline_artifact',
            'trust_source' => 'explicit_operator_public_key_pin',
            'official_publisher_trust' => 'not_established',
            'revocation_freshness' => 'not_checked',
            'rollback_protection' => 'not_checked',
            'archive_contents' => 'not_checked',
            'future_consumption' => 'requires_reverification_and_protected_staging',
            'host_compatibility' => 'not_checked',
            'file_guard_scope' => PHP_OS_FAMILY === 'Linux' ? 'linux_posix_metadata' : 'local_metadata_only_no_acl_qualification',
            'canonical_execution_plan' => false,
            'native_apply_allowed' => false,
            'host_changes' => false,
            'qualified_release' => false,
        ];
    }

    /** @return ArtifactMetadata|null */
    private static function metadata(string $bytes): ?array
    {
        try {
            $value = json_decode($bytes, true, 4, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }
        if (!is_array($value) || !self::keys($value, self::MANIFEST_KEYS)
            || $value['schema'] !== 'wcp360.candidate-artifact/1'
            || !is_string($value['component']) || !in_array($value['component'], self::COMPONENTS, true)
            || !is_string($value['version']) || !self::version($value['version'])
            || !is_string($value['target']) || !self::target($value['target'])
            || $value['architecture'] !== 'amd64'
            || !is_int($value['size']) || $value['size'] < 1 || $value['size'] > ArtifactInput::MAX_ARTIFACT_BYTES
            || !is_string($value['sha256']) || preg_match('/\A[0-9a-f]{64}\z/D', $value['sha256']) !== 1) {
            return null;
        }
        // Compact JSON plus one LF: rejects duplicate keys, BOM, alternate scalar
        // representations and unsigned interpretation differences without JCS claims.
        if ($bytes !== json_encode($value, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n") {
            return null;
        }

        return [
            'schema' => $value['schema'], 'component' => $value['component'],
            'version' => $value['version'], 'target' => $value['target'],
            'architecture' => $value['architecture'], 'size' => $value['size'], 'sha256' => $value['sha256'],
        ];
    }

    /**
     * @param array<string, mixed> $paths
     * @param array<string, mixed> $expected
     */
    private static function validInputs(array $paths, array $expected): bool
    {
        if (!self::keys($paths, ['artifact', 'manifest', 'signature']) || !self::keys($expected, ['public_key', 'component', 'version', 'target'])) {
            return false;
        }
        foreach ($paths as $path) {
            if (!is_string($path) || $path === '' || strlen($path) > 4096 || preg_match('/[\x00-\x1F\x7F]/', $path) === 1
                || (substr($path, 0, 1) !== '/' && preg_match('/\A[A-Za-z]:[\\\\\/]/D', $path) !== 1)
                || str_starts_with($path, '//') || str_contains($path, '://')) {
                return false;
            }
        }

        return is_string($expected['public_key']) && preg_match('/\A[0-9a-f]{64}\z/D', $expected['public_key']) === 1
            && is_string($expected['component']) && in_array($expected['component'], self::COMPONENTS, true)
            && is_string($expected['version']) && self::version($expected['version'])
            && is_string($expected['target']) && self::target($expected['target']);
    }

    private static function version(string $value): bool
    {
        if (strlen($value) > 64 || preg_match('/\A(?:0|[1-9][0-9]{0,8})\.(?:0|[1-9][0-9]{0,8})\.(?:0|[1-9][0-9]{0,8})(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?\z/D', $value, $match) !== 1) {
            return false;
        }
        foreach (isset($match[1]) ? explode('.', $match[1]) : [] as $part) {
            if (preg_match('/\A[0-9]+\z/D', $part) === 1 && strlen($part) > 1 && $part[0] === '0') {
                return false;
            }
        }

        return true;
    }

    private static function target(string $value): bool
    {
        return strlen($value) <= 32 && preg_match('/\A[a-z]+-[0-9]+(?:\.[0-9]+)?\z/D', $value) === 1;
    }

    /**
     * @param array<array-key, mixed> $value
     * @param list<string> $expected
     */
    private static function keys(array $value, array $expected): bool
    {
        $actual = array_keys($value);
        sort($actual);
        sort($expected);

        return $actual === $expected;
    }
}
