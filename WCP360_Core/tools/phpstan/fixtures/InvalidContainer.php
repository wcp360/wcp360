<?php

declare(strict_types=1);

namespace Wcp360\Core\AnalysisFixtures;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;

final class InvalidContainer
{
    public function missing(ContainerInterface $container): void
    {
        $container->get('wcp360.analysis.nonexistent_service');
    }

    public function privateService(ContainerInterface $container): void
    {
        $container->get(ApplicationServerPolicy::class);
    }
}
