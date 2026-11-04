<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\Infrastructure;

use Wcp360\Core\Module\Platform\Application\ServerObservation;
use Wcp360\Core\Shared\Platform\LinuxHostInventory;
use Wcp360\Core\Shared\Platform\LinuxRuntimeMetrics;

final class LocalServerObservation implements ServerObservation
{
    public function read(): array
    {
        // No network-table read is needed by this summary. The caller sees no UID, managed-path inventory or listener ports.
        $host = LinuxHostInventory::collect(false);

        return ['os_family' => PHP_OS_FAMILY, 'platform' => $host['platform'],
            'architecture' => substr($host['architecture'], 0, 64), 'memory' => $host['memory'], 'disk' => $host['disk'],
            'runtime' => LinuxRuntimeMetrics::collect()];
    }
}
