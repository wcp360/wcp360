<?php

declare(strict_types=1);

namespace Wcp360\Contracts;

use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Surface;

final readonly class ApiContractDefinition
{
    /** @param list<string> $requiredFields @param list<string> $optionalFields */
    public function __construct(
        public string $contractId,
        public string $version,
        public string $surface,
        public string $authentication,
        public string $permissionId,
        public string $actionId,
        public string $resourceType,
        public string $tenantScope,
        public string $requestSchema,
        public string $responseSchema,
        public string $errorProfile,
        public string $timeout,
        public string $retry,
        public string $idempotency,
        public string $auditProfile,
        public string $securityProfile,
        public string $compatibility,
        public string $testId,
        public array $requiredFields,
        public array $optionalFields,
    ) {
        if (!preg_match('/^API-WCP-\d{4}$/', $contractId)) {
            throw new ContractViolation('invalid API contract id');
        }
        if ($version !== '1.0') {
            throw new ContractViolation('unsupported API contract version');
        }
        if (!in_array($surface, ['WCP-USER', 'WCP-ADMIN', 'BOTH', 'POLICY', 'EMERGENCY'], true)) {
            throw new ContractViolation('unsupported API contract surface');
        }
        if (!in_array($authentication, ['AUTH-U', 'AUTH-A', 'AUTH-B', 'AUTH-P', 'AUTH-PRIV', 'AUTH-E'], true)) {
            throw new ContractViolation('unsupported API authentication profile');
        }
        foreach ([$permissionId, $actionId, $resourceType, $authentication, $tenantScope, $testId] as $typedValue) {
            if ($typedValue === '' || str_contains($typedValue, "\0")) {
                throw new ContractViolation('API contract typed value missing/invalid');
            }
        }
    }

    /** @param array<string,mixed> $row */
    public static function fromArray(array $row): self
    {
        [$required, $optional] = RequestSchema::parse((string) $row['request_schema']);
        return new self(
            (string) $row['contract_id'],
            (string) $row['version'],
            (string) $row['surface'],
            (string) $row['authentication'],
            (string) $row['permission_id'],
            (string) $row['action_id'],
            (string) $row['resource_type'],
            (string) $row['tenant_scope'],
            (string) $row['request_schema'],
            (string) $row['response_schema'],
            (string) $row['errors'],
            (string) $row['timeout'],
            (string) $row['retry'],
            (string) $row['idempotency'],
            (string) $row['audit'],
            (string) $row['security'],
            (string) $row['compatibility'],
            (string) $row['test_id'],
            $required,
            $optional,
        );
    }

    public function allowsSurface(Surface $caller): bool
    {
        return match ($this->surface) {
            'WCP-USER' => $caller === Surface::User,
            'WCP-ADMIN' => $caller === Surface::Admin,
            'BOTH' => $caller === Surface::User || $caller === Surface::Admin,
            'POLICY' => $caller === Surface::Policy,
            'EMERGENCY' => $caller === Surface::Emergency,
            default => false,
        };
    }
}
