<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Contracts\ApiContractDefinition;

interface IdempotencyGuard
{
    /** False means same logical scope/key was already seen with conflicting semantic request. */
    public function allows(ApiRequestEnvelope $request, ApiContractDefinition $contract): bool;
}
