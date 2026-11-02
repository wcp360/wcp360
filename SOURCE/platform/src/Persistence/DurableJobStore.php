<?php

declare(strict_types=1);

namespace Wcp360\Persistence;

use Wcp360\Ops\AuditRecord;
use Wcp360\Ops\IdempotencyRecord;
use Wcp360\Ops\JobRecord;

interface DurableJobStore
{
    /**
     * Canonical TX-JOB-01 boundary: operation intent + Job + required audit/outbox
     * prerequisite must commit atomically before any external or privileged dispatch.
     */
    public function enqueue(JobRecord $job, AuditRecord $audit, ?IdempotencyRecord $idempotency): void;
}
