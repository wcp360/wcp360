<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Application;

interface ApplicationPlanRenderer
{
    /** @return array<string,string> */
    public function renderApplication(string $siteId, string $host, string $recipe, string $application): array;
}
