<?php

declare(strict_types=1);

namespace Wcp360\Ops;

use DateTimeImmutable;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\SecretSafe;
use Wcp360\Shared\Surface;
use Wcp360\Shared\Uuid;

final readonly class AuditRecord
{
    /** @param array<string,mixed> $metadata */
    public function __construct(
        public Uuid $auditRecordId,
        public string $eventId,
        public ?Uuid $tenantId,
        public ?Uuid $actorId,
        public Surface $originSurface,
        public string $actionId,
        public string $resourceType,
        public ?string $resourceId,
        public Uuid $correlationId,
        public DateTimeImmutable $occurredAt,
        public array $metadata = [],
    ) {
        if ($eventId === '' || $actionId === '' || $resourceType === '') {
            throw new ContractViolation('audit typed identifiers must be nonempty');
        }
        SecretSafe::assertArray($metadata);
    }
}
