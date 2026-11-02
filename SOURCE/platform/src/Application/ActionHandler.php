<?php

declare(strict_types=1);

namespace Wcp360\Application;

use Wcp360\Security\AuthorizationContext;

interface ActionHandler
{
    public function actionId(): string;

    /** @param array<string,mixed> $input @return array<string,mixed> */
    public function handle(array $input, AuthorizationContext $context): array;
}
