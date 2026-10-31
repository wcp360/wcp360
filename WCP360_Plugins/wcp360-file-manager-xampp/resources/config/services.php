<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Wcp360\FileManager\Controller\FileController;
return static function(ContainerConfigurator $c): void {
    // Host must bind IdentityProvider and FileBackend, and configure a dedicated safe audit logger.
    $c->services()->defaults()->autowire()->autoconfigure()
        ->set(FileController::class)->tag('controller.service_arguments');
};
