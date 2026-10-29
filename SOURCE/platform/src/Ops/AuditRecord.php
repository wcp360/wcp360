<?php

declare(strict_types=1);

namespace Wcp360\Ops;

use DateTimeImmutable;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Surface;
use Wcp360\Shared\Uuid;

final readonly class AuditRecord
{
    private const FORBIDDEN_METADATA_KEYS = [
        'password', 'passwd', 'secret', 'client_secret', 'token', 'access_token',
        'refresh_token', 'bearer_token', 'cookie', 'set_cookie', 'authorization',
        'private_key', 'credential', 'credentials',
    ];

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
        self::assertSecretSafe($metadata);
    }

    /** @param array<mixed> $value */
    private static function assertSecretSafe(array $value): void
    {
        foreach ($value as $key => $child) {
            if (is_string($key)) {
                $normalized = strtolower(str_replace(['-', '.'], '_', $key));
                if (in_array($normalized, self::FORBIDDEN_METADATA_KEYS, true)) {
                    throw new ContractViolation('sensitive audit metadata key forbidden');
                }
            }
            if (is_array($child)) {
                self::assertSecretSafe($child);
            }
        }
    }
}
