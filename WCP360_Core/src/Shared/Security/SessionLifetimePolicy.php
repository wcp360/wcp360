<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Security;

use DateTimeImmutable;

final class SessionLifetimePolicy
{
    public static function isExpired(
        SessionKind $kind,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $lastActivityAt,
        DateTimeImmutable $now,
    ): bool {
        $created = $createdAt->getTimestamp();
        $last = $lastActivityAt->getTimestamp();
        $current = $now->getTimestamp();
        // Every caller must reject future activity, not only browser admission.
        if ($last < $created || $current < $created || $last > $current) {
            throw new ContractViolation('session timestamps are not monotonic');
        }

        [$idleSeconds, $absoluteSeconds] = match ($kind) {
            SessionKind::User => [30 * 60, 12 * 60 * 60],
            SessionKind::Admin => [15 * 60, 8 * 60 * 60],
            SessionKind::PrivilegedAdmin => [5 * 60, 15 * 60],
            SessionKind::BreakGlass => [null, 15 * 60],
        };

        if ($current >= $created + $absoluteSeconds) {
            return true;
        }
        if ($idleSeconds !== null && $current >= $last + $idleSeconds) {
            return true;
        }
        return false;
    }

    public static function mfaMandatoryAtLogin(Surface $surface): bool
    {
        // Owner decision #132: neither surface mandates MFA enrollment.
        // Account admission must still enforce an enrolled factor. This helper
        // does not grant authentication strength or waive operation step-up.
        return false;
    }

    public static function rememberMeAllowed(Surface $surface): bool
    {
        // USER remember-me is disabled in the baseline and ADMIN is forbidden.
        return false;
    }
}
