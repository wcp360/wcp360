<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/** Typed transport distinction; exception text is never parsed to select an error code. */
#[Exclude]
final class IdempotencyConflict extends ConflictHttpException
{
    public function __construct() { parent::__construct('IDEMPOTENCY_CONFLICT'); }
}
