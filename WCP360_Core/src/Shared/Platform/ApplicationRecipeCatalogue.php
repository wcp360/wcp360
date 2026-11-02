<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Platform;

/**
 * Fixed recipe descriptions, also readable by native PHP 8.1 without Symfony.
 * Configuration previews do not admit a runtime, install an application or authorize deployment.
 *
 * @phpstan-type ApplicationRecipe array{id:string,label:string,web_server:string,application_server:string,htaccess_supported:bool,artifacts:list<string>}
 */
final class ApplicationRecipeCatalogue
{
    /** @return array{schema:string,scope:string,default_application:string,profiles:list<array{id:string,label:string}>,deployment_available:bool,persisted:bool,performance_measured:bool,recipes:list<ApplicationRecipe>} */
    public static function report(): array
    {
        return [
            'schema' => 'wcp360.candidate.application-recipes/1',
            'scope' => 'configuration_preview',
            'default_application' => 'php',
            'profiles' => self::profiles(),
            'deployment_available' => false,
            'persisted' => false,
            'performance_measured' => false,
            'recipes' => [
                ['id' => 'nginx_php_fpm', 'label' => 'Nginx + PHP-FPM',
                    'web_server' => 'nginx', 'application_server' => 'php_fpm', 'htaccess_supported' => false,
                    'artifacts' => ['nginx', 'php_fpm']],
                ['id' => 'nginx_frankenphp', 'label' => 'Nginx + FrankenPHP classic',
                    'web_server' => 'nginx', 'application_server' => 'frankenphp_classic', 'htaccess_supported' => false,
                    'artifacts' => ['nginx', 'caddy', 'systemd_unit']],
                ['id' => 'apache_php_fpm', 'label' => 'Apache + PHP-FPM',
                    'web_server' => 'apache', 'application_server' => 'php_fpm', 'htaccess_supported' => true,
                    'artifacts' => ['apache', 'php_fpm', 'htaccess']],
                ['id' => 'apache_frankenphp', 'label' => 'Apache + FrankenPHP classic',
                    'web_server' => 'apache', 'application_server' => 'frankenphp_classic', 'htaccess_supported' => false,
                    'artifacts' => ['apache', 'caddy', 'systemd_unit']],
                ['id' => 'caddy_php_fpm', 'label' => 'Caddy + PHP-FPM',
                    'web_server' => 'caddy', 'application_server' => 'php_fpm', 'htaccess_supported' => false,
                    'artifacts' => ['caddy', 'php_fpm']],
                ['id' => 'caddy_frankenphp', 'label' => 'Caddy / FrankenPHP classic (direct)',
                    'web_server' => 'caddy', 'application_server' => 'frankenphp_classic', 'htaccess_supported' => false,
                    'artifacts' => ['caddy', 'systemd_unit']],
            ],
        ];
    }

    /** @return list<array{id:string,label:string}> */
    public static function profiles(): array
    {
        return [['id' => 'php', 'label' => 'PHP traditionnel'],
            ['id' => 'front_controller', 'label' => 'Contrôleur frontal (Symfony, Laravel, autres)'],
            ['id' => 'wordpress', 'label' => 'WordPress — cas de référence']];
    }

    public static function requireProfile(string $application): void
    {
        if (!in_array($application, ['php','front_controller','wordpress'], true)) {
            throw new \InvalidArgumentException('Unknown candidate application profile.');
        }
    }

    /** @return ApplicationRecipe */
    public static function choice(string $recipe): array
    {
        foreach (self::report()['recipes'] as $choice) {
            if ($choice['id'] === $recipe) return $choice;
        }
        throw new \InvalidArgumentException('Unknown candidate Application recipe.');
    }
}
