<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Wcp360\FileManager\Contract\{FileBackend,IdentityProvider};
use Wcp360\FileManager\Infrastructure\LocalBackend;
use Wcp360\FileManager\Security\StandaloneIdentityProvider;
use Wcp360\FileManager\Controller\FileController;
use Wcp360\FileManager\Standalone\LoginController;
return static function(ContainerConfigurator $c): void {
    $s=$c->services()->defaults()->autowire()->autoconfigure();
    $s->set(LocalBackend::class)->arg('$root','%env(FM_ROOT)%')->arg('$state','%env(FM_STATE)%')->arg('$owner','%env(FM_USERNAME)%');
    $s->alias(FileBackend::class,LocalBackend::class);
    $s->set(StandaloneIdentityProvider::class); $s->alias(IdentityProvider::class,StandaloneIdentityProvider::class);
    $s->set(FileController::class)->arg('$logger',\Symfony\Component\DependencyInjection\Loader\Configurator\service('monolog.logger.file_manager'))->tag('controller.service_arguments');
    $s->set(LoginController::class)->tag('controller.service_arguments');
    $s->set(\Wcp360\FileManager\Standalone\ShareController::class)->tag('controller.service_arguments');
};
