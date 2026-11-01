<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Domain;

/** Candidate application login name; it conveys no Unix identity or role. */
final class CandidateUsername
{
    public static function valid(string $value): bool
    {
        return strlen($value) >= 3 && strlen($value) <= 32 && preg_match('/\A[a-z][a-z0-9_]{2,31}\z/D', $value) === 1;
    }

    public static function require(string $value): string
    {
        if (!self::valid($value)) {
            throw new \InvalidArgumentException('Use 3–32 lowercase ASCII letters, digits or underscores, starting with a letter.');
        }

        return $value;
    }
}
