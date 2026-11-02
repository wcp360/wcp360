<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Contracts\OperationKind;
use Wcp360\Security\AuthorizationContext;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Uuid;

final readonly class ApiRequestEnvelope
{
    /** @param array<string,mixed> $payload */
    public function __construct(
        public string $contractId,
        public array $payload,
        public AuthorizationContext $authorization,
        public Uuid $requestId,
        public Uuid $correlationId,
        public OperationKind $operationKind,
        public bool $browserRequest,
        public ?string $idempotencyKey = null,
        public ?string $origin = null,
        public ?string $csrfToken = null,
    ) {
        if (!preg_match('/^API-WCP-\d{4}$/', $contractId)) {
            throw new ContractViolation('invalid API request contract id');
        }
        if ($idempotencyKey !== null && ($idempotencyKey === '' || str_contains($idempotencyKey, "\0"))) {
            throw new ContractViolation('invalid idempotency key');
        }
        foreach ([$origin, $csrfToken] as $transportValue) {
            if ($transportValue !== null && ($transportValue === '' || str_contains($transportValue, "\0"))) {
                throw new ContractViolation('invalid browser security transport value');
            }
        }
    }
}
