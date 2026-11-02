<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Application;

use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueSnapshot;

interface ApplicationCatalogueSource
{
    public function read(): ApplicationCatalogueSnapshot;
}
