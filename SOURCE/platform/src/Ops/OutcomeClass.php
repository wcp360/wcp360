<?php

declare(strict_types=1);

namespace Wcp360\Ops;

enum OutcomeClass: string
{
    case Succeeded = 'SUCCEEDED';
    case Failed = 'FAILED';
    case PartiallySucceeded = 'PARTIALLY_SUCCEEDED';
    case UnknownOutcome = 'UNKNOWN_OUTCOME';
    case Cancelled = 'CANCELLED';
    case TimedOut = 'TIMED_OUT';
    case CompensationRequired = 'COMPENSATION_REQUIRED';
    case ReconciliationRequired = 'RECONCILIATION_REQUIRED';
}
