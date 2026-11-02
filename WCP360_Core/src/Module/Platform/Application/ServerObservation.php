<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\Application;

/**
 * Narrow read-only port: no command, resource selector, credentials or effect.
 * @phpstan-type Capacity array{total_bytes:int,available_bytes:int}
 * @phpstan-type Storage array{path:'/var',total_bytes:int,free_bytes:int}
 * @phpstan-type Observation array{os_family:string,platform:?string,architecture:string,memory:?Capacity,disk:?Storage}
 */
interface ServerObservation
{
    /** @return Observation */
    public function read(): array;
}
