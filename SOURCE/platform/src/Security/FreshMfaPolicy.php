<?php

declare(strict_types=1);

namespace Wcp360\Security;

use DateTimeImmutable;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Surface;

final class FreshMfaPolicy
{
    public static function isFreshForHighRisk(
        Surface $surface,
        DateTimeImmutable $mfaSatisfiedAt,
        DateTimeImmutable $now,
        bool $required = true,
    ): bool {
        if (!$required && $surface === Surface::User) {
            return true;
        }
        $maxAge = match ($surface) {
            Surface::User => 10 * 60,
            Surface::Admin => 5 * 60,
            default => throw new ContractViolation('fresh MFA policy applies only to USER/ADMIN surfaces'),
        };
        $age = $now->getTimestamp() - $mfaSatisfiedAt->getTimestamp();
        return $age >= 0 && $age <= $maxAge;
    }
}
