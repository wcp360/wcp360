<?php

declare(strict_types=1);

namespace Wcp360\Ops;

use DateTimeImmutable;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Uuid;

final readonly class IdempotencyRecord
{
    public function __construct(
        public Uuid $idempotencyId,
        public IdempotencyScope $scopeKind,
        public ?Uuid $accountId,
        public ?Uuid $tenantId,
        public string $operationNamespace,
        public string $idempotencyKey,
        public string $requestFingerprint,
        public string $state,
        public ?Uuid $resultReference,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $expiresAt,
        public int $revision,
    ) {
        if ($operationNamespace === '' || $idempotencyKey === '' || $requestFingerprint === '' || $state === '') {
            throw new ContractViolation('idempotency typed values must be nonempty');
        }
        if ($expiresAt <= $createdAt) {
            throw new ContractViolation('idempotency expiry must be after creation');
        }
        if ($revision < 1) {
            throw new ContractViolation('idempotency revision must be >=1');
        }
        $validScope = match ($scopeKind) {
            IdempotencyScope::Global => $accountId === null && $tenantId === null,
            IdempotencyScope::Account => $accountId !== null && $tenantId === null,
            IdempotencyScope::Tenant => $tenantId !== null && $accountId === null,
        };
        if (!$validScope) {
            throw new ContractViolation('idempotency scope identifiers violate canonical CHECK');
        }
    }

    public function conflictsWithFingerprint(string $candidate): bool
    {
        return !hash_equals($this->requestFingerprint, $candidate);
    }
}
