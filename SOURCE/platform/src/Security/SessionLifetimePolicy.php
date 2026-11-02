<?php

declare(strict_types=1);

namespace Wcp360\Security;

use DateTimeImmutable;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Surface;

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
        if ($last < $created || $current < $created) {
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
        return $surface === Surface::Admin;
    }

    public static function rememberMeAllowed(Surface $surface): bool
    {
        // USER remember-me is disabled in the baseline and ADMIN is forbidden.
        return false;
    }
}
