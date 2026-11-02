<?php

declare(strict_types=1);

namespace Wcp360\Ops;

use DateTimeImmutable;
use Wcp360\Shared\CanonicalErrorCode;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Uuid;

final readonly class JobRecord
{
    public function __construct(
        public Uuid $jobId,
        public ?Uuid $tenantId,
        public string $jobType,
        public string $actionId,
        public ?Uuid $resourceId,
        public ?Uuid $requestedByUserId,
        public string $jobState,
        public ?OutcomeClass $outcomeClass,
        public ?CanonicalErrorCode $errorCode,
        public int $attemptCount,
        public int $maxAttempts,
        public DateTimeImmutable $availableAt,
        public ?DateTimeImmutable $startedAt,
        public ?DateTimeImmutable $completedAt,
        public ?DateTimeImmutable $timeoutAt,
        public Uuid $correlationId,
        public int $revision,
    ) {
        if ($jobType === '' || $actionId === '' || $jobState === '') {
            throw new ContractViolation('job typed identifiers/state must be nonempty');
        }
        if ($attemptCount < 0 || $maxAttempts < 1 || $attemptCount > $maxAttempts) {
            throw new ContractViolation('invalid job attempt counters');
        }
        if ($revision < 1) {
            throw new ContractViolation('job revision must be >=1');
        }
    }
}
