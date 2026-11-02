<?php

declare(strict_types=1);

namespace Wcp360\CanonicalActions;

use Wcp360\Security\AuthorizationContext;

interface ReadOperationPort
{
    /** @param array<string,mixed> $input @return array<string,mixed> */
    public function execute(array $input, AuthorizationContext $context): array;
}
