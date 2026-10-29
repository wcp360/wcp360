<?php

declare(strict_types=1);

namespace Wcp360\Application;

use Wcp360\Contracts\ActionCatalog;
use Wcp360\Shared\ContractViolation;

final class ActionRegistry
{
    /** @var array<string,ActionHandler> */
    private array $handlers = [];

    /** @param iterable<ActionHandler> $handlers */
    public function __construct(private readonly ActionCatalog $catalog, iterable $handlers = [])
    {
        foreach ($handlers as $handler) {
            $this->register($handler);
        }
    }

    public function register(ActionHandler $handler): void
    {
        $id = $handler->actionId();
        $this->catalog->require($id);
        if (isset($this->handlers[$id])) {
            throw new ContractViolation('duplicate action handler');
        }
        $this->handlers[$id] = $handler;
    }

    public function find(string $actionId): ?ActionHandler
    {
        if (!$this->catalog->has($actionId)) {
            return null;
        }
        return $this->handlers[$actionId] ?? null;
    }
}
