<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Security\SessionKind;
use Wcp360\Core\Shared\Security\SessionLifetimePolicy;
use Wcp360\Core\Shared\Security\Surface;

/** Product login policy only; enabled-factor enforcement belongs to admission. */
final class OptionalLoginMfaPolicyTest extends TestCase
{
    public function testNeitherSurfaceRequiresMfaEnrollmentAtOrdinaryLogin(): void
    {
        self::assertFalse(SessionLifetimePolicy::mfaMandatoryAtLogin(Surface::User));
        self::assertFalse(SessionLifetimePolicy::mfaMandatoryAtLogin(Surface::Admin));
    }

    public function testOptionalEnrollmentDoesNotEnableRememberMe(): void
    {
        self::assertFalse(SessionLifetimePolicy::rememberMeAllowed(Surface::User));
        self::assertFalse(SessionLifetimePolicy::rememberMeAllowed(Surface::Admin));
    }

    public function testOrdinarySessionLifetimeBoundariesRemainUnchanged(): void
    {
        $created = new DateTimeImmutable('2026-11-04T00:00:00+00:00');
        foreach ([[SessionKind::User, 1800, 43200], [SessionKind::Admin, 900, 28800]] as [$kind, $idle, $absolute]) {
            $beforeIdle = $created->modify('+'.($idle - 1).' seconds');
            $atIdle = $created->modify('+'.$idle.' seconds');
            self::assertFalse(SessionLifetimePolicy::isExpired($kind, $created, $created, $beforeIdle));
            self::assertTrue(SessionLifetimePolicy::isExpired($kind, $created, $created, $atIdle));

            // A recent heartbeat cannot extend the original absolute deadline.
            $beforeAbsolute = $created->modify('+'.($absolute - 1).' seconds');
            $atAbsolute = $created->modify('+'.$absolute.' seconds');
            self::assertFalse(SessionLifetimePolicy::isExpired($kind, $created, $beforeAbsolute, $beforeAbsolute));
            self::assertTrue(SessionLifetimePolicy::isExpired($kind, $created, $beforeAbsolute, $atAbsolute));
        }
    }
}
