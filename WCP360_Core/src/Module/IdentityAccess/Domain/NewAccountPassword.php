<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Domain;

/** Shared candidate enrollment/change policy; no composition or trimming rule. */
final class NewAccountPassword
{
    public static function require(#[\SensitiveParameter] string $value): string
    {
        if (strlen($value) > 256 || str_contains($value, "\0") || !mb_check_encoding($value, 'UTF-8') || mb_strlen($value, 'UTF-8') < 14) {
            throw new \InvalidArgumentException('Use at least 14 characters, at most 256 bytes and no null characters.');
        }

        return $value;
    }
}
