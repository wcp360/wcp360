<?php

declare(strict_types=1);

namespace Wcp360\CanonicalActions;

use Wcp360\Application\ActionHandler;
use Wcp360\Security\AuthorizationContext;

final readonly class ProcessesReadHandler implements ActionHandler
{
    public const ACTION_ID = 'processes.read';

    public function __construct(private ReadOperationPort $port)
    {
    }

    public function actionId(): string
    {
        return self::ACTION_ID;
    }

    /** @param array<string,mixed> $input @return array<string,mixed> */
    public function handle(array $input, AuthorizationContext $context): array
    {
        return $this->port->execute($input, $context);
    }
}
