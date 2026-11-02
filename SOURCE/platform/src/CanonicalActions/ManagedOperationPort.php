<?php

declare(strict_types=1);

namespace Wcp360\CanonicalActions;

use Wcp360\Security\AuthorizationContext;

interface ManagedOperationPort
{
    /**
     * This port is used only by canonical contracts whose profile is
     * "RK | key when mutating". The trusted transport classifies READ versus
     * MUTATION before the handler is reached; the request body never chooses
     * another action ID or bypasses contract policy.
     *
     * @param array<string,mixed> $input
     * @return array<string,mixed>
     */
    public function execute(array $input, AuthorizationContext $context): array;
}
