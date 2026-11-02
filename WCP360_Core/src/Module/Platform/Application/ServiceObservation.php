<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\Application;

use Wcp360\Core\Module\Platform\Domain\ServiceObservationSnapshot;

/** Fixed local service inventory; no caller-supplied unit, property, transport or command. */
interface ServiceObservation
{
    public function read(): ServiceObservationSnapshot;
}
