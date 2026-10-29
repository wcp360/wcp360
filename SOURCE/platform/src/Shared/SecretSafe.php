<?php

declare(strict_types=1);

namespace Wcp360\Shared;

final class SecretSafe
{
    private const FORBIDDEN_KEYS = [
        'password', 'passwd', 'secret', 'client_secret', 'token', 'access_token',
        'refresh_token', 'bearer_token', 'cookie', 'set_cookie', 'authorization',
        'private_key', 'credential', 'credentials', 'database_password', 'db_password',
    ];

    /** @param array<mixed> $value */
    public static function assertArray(array $value): void
    {
        foreach ($value as $key => $child) {
            if (is_string($key)) {
                $normalized = strtolower(str_replace(['-', '.'], '_', $key));
                if (in_array($normalized, self::FORBIDDEN_KEYS, true)) {
                    throw new ContractViolation('sensitive payload key forbidden');
                }
            }
            if (is_array($child)) {
                self::assertArray($child);
            }
        }
    }
}
