<?php

declare(strict_types=1);

namespace Wcp360\Application;

use Wcp360\Shared\ContractViolation;

final class ActionRegistry
{
    /** @var array<string,ActionHandler> */
    private array $handlers = [];

    /** @param iterable<ActionHandler> $handlers */
    public function __construct(iterable $handlers = [])
    {
        foreach ($handlers as $handler) {
            $this->register($handler);
        }
    }

    public function register(ActionHandler $handler): void
    {
        $id = $handler->actionId();
        if ($id === '' || isset($this->handlers[$id])) {
            throw new ContractViolation('invalid or duplicate action handler');
        }
        $this->handlers[$id] = $handler;
    }

    public function find(string $actionId): ?ActionHandler
    {
        return $this->handlers[$actionId] ?? null;
    }
}
