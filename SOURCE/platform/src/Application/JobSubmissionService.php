<?php

declare(strict_types=1);

namespace Wcp360\Application;

use Wcp360\Ops\AuditRecord;
use Wcp360\Ops\IdempotencyRecord;
use Wcp360\Ops\IdempotencyScope;
use Wcp360\Ops\JobRecord;
use Wcp360\Persistence\DurableJobStore;
use Wcp360\Shared\ContractViolation;

final readonly class JobSubmissionService
{
    public function __construct(private DurableJobStore $store)
    {
    }

    public function submit(JobRecord $job, AuditRecord $audit, ?IdempotencyRecord $idempotency = null): void
    {
        if ((string) $job->correlationId !== (string) $audit->correlationId) {
            throw new ContractViolation('job/audit correlation mismatch');
        }
        if ($job->tenantId !== null) {
            if ($audit->tenantId === null || (string) $job->tenantId !== (string) $audit->tenantId) {
                throw new ContractViolation('job/audit tenant mismatch');
            }
        }
        if ($idempotency?->scopeKind === IdempotencyScope::Tenant) {
            if ($job->tenantId === null || $idempotency->tenantId === null || (string) $job->tenantId !== (string) $idempotency->tenantId) {
                throw new ContractViolation('job/idempotency tenant mismatch');
            }
        }
        $this->store->enqueue($job, $audit, $idempotency);
    }
}
