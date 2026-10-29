<?php

declare(strict_types=1);

namespace Wcp360\Contracts;

use JsonException;
use Wcp360\Security\SessionSurfacePolicy;
use Wcp360\Shared\CanonicalErrorCode;
use Wcp360\Shared\ContractViolation;

final class ContractIntegrity
{
    /** @return array{permissions:int,api:int,errors:int,ipc:int,database_tables:int,session_policies:int} */
    public static function verify(string $repositoryRoot, string $contractsDir): array
    {
        $manifest = self::json($contractsDir . '/manifest.json');
        if (($manifest['status'] ?? null) !== 'DERIVED_IMPLEMENTATION_INPUTS_CANDIDATE') {
            throw new ContractViolation('derived contract manifest status mismatch');
        }

        $permissions = self::json($contractsDir . '/permissions.v1.json');
        $api = self::json($contractsDir . '/api-contracts.v1.json');
        $errors = self::json($contractsDir . '/error-codes.v1.json');
        $ipc = self::json($contractsDir . '/ipc-operations.v1.json');
        $database = self::json($contractsDir . '/database-tables.v1.json');
        $sessions = self::json($contractsDir . '/session-surfaces.v1.json');

        self::expectCount($permissions, 'permissions', 82);
        self::expectCount($api, 'contracts', 86);
        self::expectCount($errors, 'error_codes', 23);
        self::expectCount($ipc, 'operations', 47);
        self::expectCount($database, 'tables', 54);
        if (count($sessions['policies'] ?? []) !== 8) {
            throw new ContractViolation('session policy count mismatch');
        }

        self::verifySourceBlob($repositoryRoot, $permissions);
        self::verifySourceBlob($repositoryRoot, $api);
        self::verifySourceBlob($repositoryRoot, $ipc);
        self::verifySourceBlob($repositoryRoot, $database);
        self::verifySourceBlob($repositoryRoot, $sessions);

        self::assertUnique($permissions['permissions'], 'id');
        self::assertUnique($api['contracts'], 'contract_id');
        self::assertUnique($ipc['operations'], 'contract_id');
        self::assertUnique($ipc['operations'], 'ipc_operation_id');
        self::assertUnique($database['tables'], 'table');

        $permissionIds = array_fill_keys(array_column($permissions['permissions'], 'id'), true);
        $usedPermissions = [];
        foreach ($api['contracts'] as $contract) {
            $id = (string) ($contract['permission_id'] ?? '');
            if (!isset($permissionIds[$id])) {
                throw new ContractViolation('API references unknown permission: ' . $id);
            }
            $usedPermissions[$id] = true;
            ApiContractDefinition::fromArray($contract);
        }
        if (count($usedPermissions) !== 82) {
            throw new ContractViolation('API permission coverage is not 82/82');
        }

        $enumErrors = array_map(static fn(CanonicalErrorCode $e): string => $e->value, CanonicalErrorCode::cases());
        $jsonErrors = array_values(array_map('strval', $errors['error_codes']));
        sort($enumErrors);
        sort($jsonErrors);
        if ($enumErrors !== $jsonErrors) {
            throw new ContractViolation('PHP error enum drifted from canonical error registry');
        }

        SessionSurfacePolicy::fromJsonFile($contractsDir . '/session-surfaces.v1.json');

        self::assertManifestBlob($manifest, 'permissions', $permissions);
        self::assertManifestBlob($manifest, 'api', $api);
        self::assertManifestBlob($manifest, 'ipc', $ipc);
        self::assertManifestBlob($manifest, 'database', $database);
        if (($manifest['security_sessions']['blob'] ?? null) !== ($sessions['source_blob_sha'] ?? null)) {
            throw new ContractViolation('session source blob/manifest mismatch');
        }
        if (($manifest['api_errors']['blob'] ?? null) !== ($errors['source_blob_sha'] ?? null)) {
            throw new ContractViolation('error source blob/manifest mismatch');
        }

        return [
            'permissions' => 82,
            'api' => 86,
            'errors' => 23,
            'ipc' => 47,
            'database_tables' => 54,
            'session_policies' => 8,
        ];
    }

    /** @return array<string,mixed> */
    private static function json(string $path): array
    {
        if (!is_file($path)) {
            throw new ContractViolation('derived contract file missing: ' . basename($path));
        }
        try {
            $value = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ContractViolation('invalid derived contract JSON', 0, $e);
        }
        if (!is_array($value)) {
            throw new ContractViolation('derived contract JSON must be object');
        }
        return $value;
    }

    /** @param array<string,mixed> $document */
    private static function expectCount(array $document, string $listKey, int $expected): void
    {
        $items = $document[$listKey] ?? null;
        if (!is_array($items) || count($items) !== $expected || ($document['count'] ?? null) !== $expected) {
            throw new ContractViolation('derived contract count mismatch: ' . $listKey);
        }
    }

    /** @param array<string,mixed> $document */
    private static function verifySourceBlob(string $repositoryRoot, array $document): void
    {
        $relative = (string) ($document['source_path'] ?? '');
        $expected = (string) ($document['source_blob_sha'] ?? '');
        if ($relative === '' || str_contains($relative, '..') || !str_starts_with($relative, 'specifications/')) {
            throw new ContractViolation('invalid canonical source path in derived contract');
        }
        $path = rtrim($repositoryRoot, '/') . '/' . $relative;
        if (!is_file($path)) {
            throw new ContractViolation('canonical source file missing: ' . $relative);
        }
        $content = (string) file_get_contents($path);
        $actual = sha1('blob ' . strlen($content) . "\0" . $content);
        if (!preg_match('/^[0-9a-f]{40}$/', $expected) || !hash_equals($expected, $actual)) {
            throw new ContractViolation('canonical source blob drift: ' . $relative);
        }
    }

    /** @param list<array<string,mixed>> $rows */
    private static function assertUnique(array $rows, string $key): void
    {
        $seen = [];
        foreach ($rows as $row) {
            $value = (string) ($row[$key] ?? '');
            if ($value === '' || isset($seen[$value])) {
                throw new ContractViolation('missing/duplicate derived identifier: ' . $key);
            }
            $seen[$value] = true;
        }
    }

    /** @param array<string,mixed> $manifest @param array<string,mixed> $document */
    private static function assertManifestBlob(array $manifest, string $key, array $document): void
    {
        if (($manifest[$key]['blob'] ?? null) !== ($document['source_blob_sha'] ?? null)) {
            throw new ContractViolation($key . ' source blob/manifest mismatch');
        }
    }
}
