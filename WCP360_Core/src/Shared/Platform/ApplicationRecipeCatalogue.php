<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Platform;

/**
 * Fixed recipe descriptions, also readable by native PHP 8.1 without Symfony.
 * Configuration previews do not admit a runtime, install an application or authorize deployment.
 *
 * @phpstan-type ApplicationRecipe array{id:string,label:string,web_server:string,application_server:string,htaccess_supported:bool,artifacts:list<string>,plan_schema:string}
 */
final class ApplicationRecipeCatalogue
{
    /** @return array{schema:string,scope:string,default_application:string,profiles:list<array{id:string,label:string}>,deployment_available:bool,persisted:bool,performance_measured:bool,recipes:list<ApplicationRecipe>} */
    public static function report(): array
    {
        return [
            'schema' => 'wcp360.candidate.application-recipes/2',
            'scope' => 'configuration_preview',
            'default_application' => 'php',
            'profiles' => self::profiles(),
            'deployment_available' => false,
            'persisted' => false,
            'performance_measured' => false,
            'recipes' => [
                ['id' => 'nginx_php_fpm', 'label' => 'Nginx + PHP-FPM',
                    'web_server' => 'nginx', 'application_server' => 'php_fpm', 'htaccess_supported' => false,
                    'artifacts' => ['nginx', 'php_fpm'], 'plan_schema' => 'wcp360.candidate.application-plan/1'],
                ['id' => 'nginx_frankenphp', 'label' => 'Nginx + FrankenPHP classic',
                    'web_server' => 'nginx', 'application_server' => 'frankenphp_classic', 'htaccess_supported' => false,
                    'artifacts' => ['nginx', 'caddy', 'systemd_unit'], 'plan_schema' => 'wcp360.candidate.application-plan/1'],
                ['id' => 'apache_php_fpm', 'label' => 'Apache + PHP-FPM',
                    'web_server' => 'apache', 'application_server' => 'php_fpm', 'htaccess_supported' => true,
                    'artifacts' => ['apache', 'php_fpm', 'htaccess'], 'plan_schema' => 'wcp360.candidate.application-plan/1'],
                ['id' => 'apache_frankenphp', 'label' => 'Apache + FrankenPHP classic',
                    'web_server' => 'apache', 'application_server' => 'frankenphp_classic', 'htaccess_supported' => false,
                    'artifacts' => ['apache', 'caddy', 'systemd_unit'], 'plan_schema' => 'wcp360.candidate.application-plan/1'],
                ['id' => 'caddy_php_fpm', 'label' => 'Caddy + PHP-FPM',
                    'web_server' => 'caddy', 'application_server' => 'php_fpm', 'htaccess_supported' => false,
                    'artifacts' => ['caddy', 'php_fpm'], 'plan_schema' => 'wcp360.candidate.application-plan/1'],
                ['id' => 'caddy_frankenphp', 'label' => 'Caddy / FrankenPHP classic (direct)',
                    'web_server' => 'caddy', 'application_server' => 'frankenphp_classic', 'htaccess_supported' => false,
                    'artifacts' => ['caddy', 'systemd_unit'], 'plan_schema' => 'wcp360.candidate.application-plan/1'],
                ['id' => 'nginx_apache_php_fpm', 'label' => 'cible Nginx → Apache event → PHP-FPM (aperçu)',
                    'web_server' => 'nginx_apache', 'application_server' => 'php_fpm', 'htaccess_supported' => true,
                    'artifacts' => ['nginx', 'apache', 'php_fpm', 'htaccess'], 'plan_schema' => 'wcp360.candidate.application-plan/2'],
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

    /** The requested recipe selects its wire contract without changing any durable runtime profile. */
    public static function planSchema(string $recipe): string
    {
        return self::choice($recipe)['plan_schema'];
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
