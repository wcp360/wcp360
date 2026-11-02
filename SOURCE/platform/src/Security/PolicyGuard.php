<?php

declare(strict_types=1);

namespace Wcp360\Security;

interface PolicyGuard
{
    public function evaluate(
        AuthorizationContext $context,
        AuthorizationRequest $request,
    ): AuthorizationDecision;
}
