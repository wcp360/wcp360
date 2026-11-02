<?php

declare(strict_types=1);

namespace Wcp360\Core\AnalysisFixtures;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

final class ValidContainer
{
    public function router(ContainerInterface $container): RouterInterface
    {
        return $container->get('router');
    }
}
