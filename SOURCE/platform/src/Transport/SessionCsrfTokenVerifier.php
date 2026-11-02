<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Security\AuthorizationContext;

interface SessionCsrfTokenVerifier
{
    public function verify(AuthorizationContext $authorization, string $presentedToken): bool;
}
