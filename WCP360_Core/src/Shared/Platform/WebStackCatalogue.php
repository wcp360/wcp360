<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Platform;

/**
 * Fixed topology information shared with the native PHP 8.1 installer inspection.
 * A selectable candidate plan never grants authority to install or migrate a stack.
 *
 * @phpstan-type StackChoice array{id:string,label:string,topology:string,driver:string,selection_value:?string,plan_implemented:bool,requires_worker_opt_in:bool,selectable_for_plan:bool,activation:string}
 */
final class WebStackCatalogue
{
    /** @return array{schema:string,default:string,scope:string,native_installation_available:bool,migration_available:bool,multi_php_available:bool,host_changes:bool,qualified_release:bool,choices:list<StackChoice>} */
    public static function report(bool $workerPlans = false): array
    {
        return [
            'schema' => 'wcp360.candidate.web-stacks/1', 'default' => 'classic',
            'scope' => 'hosting_plan_capabilities', 'native_installation_available' => false,
            'migration_available' => false, 'multi_php_available' => false,
            'host_changes' => false, 'qualified_release' => false,
            'choices' => [
                ['id' => 'classic', 'label' => 'Classic', 'topology' => 'Nginx → PHP-FPM',
                    'driver' => 'nginx_fpm', 'selection_value' => 'php_fpm', 'plan_implemented' => true,
                    'requires_worker_opt_in' => false, 'selectable_for_plan' => true, 'activation' => 'isolated_candidate_only'],
                ['id' => 'modern', 'label' => 'Modern', 'topology' => 'Caddy / FrankenPHP Worker',
                    'driver' => 'frankenphp_direct', 'selection_value' => null, 'plan_implemented' => false,
                    'requires_worker_opt_in' => true, 'selectable_for_plan' => false, 'activation' => 'unimplemented'],
                ['id' => 'legacy', 'label' => 'Legacy / compatibility', 'topology' => 'Apache → PHP-FPM',
                    'driver' => 'apache_fpm', 'selection_value' => null, 'plan_implemented' => false,
                    'requires_worker_opt_in' => false, 'selectable_for_plan' => false, 'activation' => 'unimplemented'],
                ['id' => 'advanced', 'label' => 'Advanced mix', 'topology' => 'Nginx → Caddy / FrankenPHP Worker',
                    'driver' => 'nginx_frankenphp', 'selection_value' => 'frankenphp_worker', 'plan_implemented' => true,
                    'requires_worker_opt_in' => true, 'selectable_for_plan' => $workerPlans, 'activation' => 'unimplemented'],
            ],
        ];
    }

    /** @return StackChoice */
    public static function choice(string $id, bool $workerPlans = false): array
    {
        foreach (self::report($workerPlans)['choices'] as $choice) {
            if ($choice['id'] === $id) return $choice;
        }
        throw new \InvalidArgumentException('Unknown hosting web stack.');
    }

    /** Preserve the meaning of the two existing persisted engine values and plan envelopes. */
    public static function forApplicationServer(string $server): string
    {
        return match ($server) {
            'php_fpm' => 'classic',
            'frankenphp_worker' => 'advanced',
            default => throw new \InvalidArgumentException('Unknown candidate application server.'),
        };
    }
}
