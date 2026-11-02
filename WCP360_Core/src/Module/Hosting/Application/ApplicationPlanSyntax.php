<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Application;

use Wcp360\Core\Module\Hosting\Domain\ApplicationPlanDocument;

/** Internal generated documents only; no caller-controlled commands or artifact upload. */
interface ApplicationPlanSyntax
{
    /** @return array<string,string> Ordered artifact => fixed status, never native diagnostics. */
    public function check(ApplicationPlanDocument $document): array;
}
