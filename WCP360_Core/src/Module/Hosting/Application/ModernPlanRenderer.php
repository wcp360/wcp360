<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Application;

/** Pure candidate rendering port. Implementations must never activate the result. */
interface ModernPlanRenderer
{
    /** @return array<string,string> */
    public function renderModern(string $siteId, string $host, bool $acknowledged): array;
}
