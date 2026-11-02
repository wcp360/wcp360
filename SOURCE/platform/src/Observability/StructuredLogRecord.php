<?php

declare(strict_types=1);

namespace Wcp360\Observability;

use DateTimeImmutable;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\SecretSafe;
use Wcp360\Shared\Uuid;

final readonly class StructuredLogRecord
{
    /** @param array<string,mixed> $context */
    public function __construct(
        public DateTimeImmutable $timestamp,
        public string $level,
        public string $component,
        public string $eventId,
        public Uuid $correlationId,
        public ?Uuid $requestId = null,
        public ?Uuid $jobId = null,
        public ?Uuid $tenantId = null,
        public array $context = [],
    ) {
        if (!in_array($level, ['DEBUG', 'INFO', 'NOTICE', 'WARNING', 'ERROR', 'CRITICAL'], true)) {
            throw new ContractViolation('unsupported structured log level');
        }
        if ($component === '' || $eventId === '') {
            throw new ContractViolation('structured log component/event required');
        }
        SecretSafe::assertArray($context);
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'timestamp' => $this->timestamp->format(DATE_ATOM),
            'level' => $this->level,
            'component' => $this->component,
            'event_id' => $this->eventId,
            'correlation_id' => (string) $this->correlationId,
            'request_id' => $this->requestId?->value,
            'job_id' => $this->jobId?->value,
            'tenant_id' => $this->tenantId?->value,
            'context' => $this->context,
        ];
    }
}
