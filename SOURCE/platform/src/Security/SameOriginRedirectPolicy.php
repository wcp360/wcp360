<?php

declare(strict_types=1);

namespace Wcp360\Security;

use Wcp360\Shared\ContractViolation;

final class SameOriginRedirectPolicy
{
    public static function assertRelative(string $target): void
    {
        if ($target === '' || preg_match('/[\\x00-\\x1f\\x7f\\\\]/', $target)) {
            throw new ContractViolation('redirect target invalid');
        }

        $decoded = $target;
        for ($i = 0; $i < 3; $i++) {
            self::assertDecodedRelative($decoded);
            $next = rawurldecode($decoded);
            if ($next === $decoded) {
                return;
            }
            $decoded = $next;
        }
        self::assertDecodedRelative($decoded);
        if (rawurldecode($decoded) !== $decoded) {
            throw new ContractViolation('redirect target excessively encoded');
        }
    }

    private static function assertDecodedRelative(string $target): void
    {
        if (!str_starts_with($target, '/') || str_starts_with($target, '//') || str_contains($target, '\\')) {
            throw new ContractViolation('redirect target must be same-origin relative path');
        }
        $parts = parse_url($target);
        if (!is_array($parts) || isset($parts['scheme'], $parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])) {
            throw new ContractViolation('redirect target contains external authority');
        }
        $path = (string) ($parts['path'] ?? '');
        foreach (explode('/', $path) as $segment) {
            if ($segment === '.' || $segment === '..') {
                throw new ContractViolation('redirect target must be normalized');
            }
        }
    }
}
