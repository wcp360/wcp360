<?php

declare(strict_types=1);

namespace Wcp360\Security;

interface ScopeGuard
{
    public function allows(
        AuthorizationContext $context,
        AuthorizationRequest $request,
    ): bool;
}
