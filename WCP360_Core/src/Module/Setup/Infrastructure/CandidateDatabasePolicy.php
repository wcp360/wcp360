<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

/** Direct grants for implemented candidate paths, never canonical production IAM policy. */
final class CandidateDatabasePolicy
{
    public const CONTEXTS = ['user', 'admin', 'worker'];
    public const TABLES = [
        'users', 'accounts', 'tenants', 'memberships', 'sites', 'jobs', 'audit', 'plugins',
        'runtime_settings', 'activations', 'activation_grants', 'mfa', 'mfa_recovery',
        'permission_denials', 'user_sessions', 'admin_sessions',
    ];

    /** @return array{user:string,admin:string,worker:string} */
    public static function roles(string $database): array
    {
        $prefix = match ($database) {
            'wcp360_workbench' => 'wcp360_dev_',
            'wcp360_test' => 'wcp360_test_',
            default => throw new \RuntimeException(CandidateDatabaseRoles::REJECTED),
        };

        return ['user' => $prefix.'user', 'admin' => $prefix.'admin', 'worker' => $prefix.'worker'];
    }

    /** @return array<string,list<string>> */
    public static function grants(string $context): array
    {
        $shared = [
            'users' => ['SELECT'], 'tenants' => ['SELECT'], 'memberships' => ['SELECT'],
            'permission_denials' => ['SELECT'], 'audit' => ['INSERT'],
        ];
        return match ($context) {
            'user' => array_replace($shared, [
                'users' => ['SELECT', 'UPDATE(lock_guard)'], 'tenants' => ['SELECT', 'UPDATE(lock_guard)'],
                'sites' => ['SELECT', 'INSERT', 'UPDATE(lock_guard)'], 'jobs' => ['SELECT', 'INSERT'],
                'runtime_settings' => ['SELECT'], 'activations' => ['SELECT', 'INSERT'],
                'activation_grants' => ['SELECT'],
                'user_sessions' => ['SELECT', 'INSERT', 'UPDATE', 'DELETE'],
                'plugins' => ['SELECT'],
            ]),
            'admin' => array_replace($shared, [
                'users' => ['SELECT', 'UPDATE(lock_guard)'], 'tenants' => ['SELECT', 'UPDATE(lock_guard)'],
                'permission_denials' => ['SELECT', 'INSERT', 'DELETE'],
                'runtime_settings' => ['SELECT', 'UPDATE'],
                'plugins' => ['SELECT', 'INSERT', 'UPDATE', 'DELETE'],
                'mfa' => ['SELECT', 'INSERT', 'UPDATE'],
                'mfa_recovery' => ['SELECT', 'INSERT', 'DELETE'],
                'admin_sessions' => ['SELECT', 'INSERT', 'UPDATE', 'DELETE'],
            ]),
            'worker' => array_replace($shared, [
                'sites' => ['SELECT', 'UPDATE'], 'jobs' => ['SELECT', 'UPDATE'],
                'activations' => ['SELECT', 'UPDATE'], 'activation_grants' => ['SELECT'],
            ]),
            default => throw new \RuntimeException(CandidateDatabaseRoles::REJECTED),
        };
    }
}
