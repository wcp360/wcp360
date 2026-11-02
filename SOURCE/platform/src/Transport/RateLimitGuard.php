<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Contracts\ApiContractDefinition;

interface RateLimitGuard
{
    public function allows(ApiRequestEnvelope $request, ApiContractDefinition $contract): bool;
}
