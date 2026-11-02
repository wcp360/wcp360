<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Correlation;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

/** Opaque diagnostic reference; never an identity, credential or authorization proof. */
#[Exclude]
final class RequestId
{
    public static function generate(): string
    {
        return bin2hex(random_bytes(16));
    }

    public static function valid(?string $value): bool
    {
        return $value !== null && preg_match('/\A[a-f0-9]{32}\z/D', $value) === 1;
    }

    public static function assertValid(?string $value): void
    {
        if ($value !== null && !self::valid($value)) {
            throw new \InvalidArgumentException('Invalid internal request reference.');
        }
    }
}
