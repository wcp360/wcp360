<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Security\ContractViolation;
use Wcp360\Core\Shared\Security\SessionKind;
use Wcp360\Core\Shared\Security\SessionLifetimePolicy;

final class SessionTimestampConsistencyTest extends TestCase
{
    /** @return iterable<string, array{SessionKind}> */
    public static function sessionKinds(): iterable
    {
        foreach (SessionKind::cases() as $kind) {
            yield $kind->value => [$kind];
        }
    }

    #[DataProvider('sessionKinds')]
    public function testFutureActivityIsRejectedForEverySessionKind(SessionKind $kind): void
    {
        $this->expectException(ContractViolation::class);
        SessionLifetimePolicy::isExpired($kind, new DateTimeImmutable('@1000'),
            new DateTimeImmutable('@1002'), new DateTimeImmutable('@1001'));
    }

    #[DataProvider('sessionKinds')]
    public function testActivityBeforeCreationIsRejected(SessionKind $kind): void
    {
        $this->expectException(ContractViolation::class);
        SessionLifetimePolicy::isExpired($kind, new DateTimeImmutable('@1000'),
            new DateTimeImmutable('@999'), new DateTimeImmutable('@1001'));
    }

    #[DataProvider('sessionKinds')]
    public function testFutureCreationIsRejected(SessionKind $kind): void
    {
        $this->expectException(ContractViolation::class);
        SessionLifetimePolicy::isExpired($kind, new DateTimeImmutable('@1001'),
            new DateTimeImmutable('@1001'), new DateTimeImmutable('@1000'));
    }

    #[DataProvider('sessionKinds')]
    public function testActivityAtCurrentTimeRemainsValid(SessionKind $kind): void
    {
        self::assertFalse(SessionLifetimePolicy::isExpired($kind,
            new DateTimeImmutable('@1000'), new DateTimeImmutable('@1001'),
            new DateTimeImmutable('@1001')));
    }

    public function testAllAbsoluteDeadlinesRemainExact(): void
    {
        foreach ([[SessionKind::User, 43200], [SessionKind::Admin, 28800],
            [SessionKind::PrivilegedAdmin, 900], [SessionKind::BreakGlass, 900]] as [$kind, $seconds]) {
            $created = new DateTimeImmutable('@1000');
            $before = new DateTimeImmutable('@'.(1000 + $seconds - 1));
            $deadline = new DateTimeImmutable('@'.(1000 + $seconds));
            self::assertFalse(SessionLifetimePolicy::isExpired($kind, $created, $before, $before));
            self::assertTrue(SessionLifetimePolicy::isExpired($kind, $created, $deadline, $deadline));
        }
    }

    public function testIdleDeadlinesAndBreakGlassPolicyRemainUnchanged(): void
    {
        $created = new DateTimeImmutable('@1000');
        foreach ([[SessionKind::User, 1800], [SessionKind::Admin, 900],
            [SessionKind::PrivilegedAdmin, 300]] as [$kind, $seconds]) {
            self::assertFalse(SessionLifetimePolicy::isExpired($kind, $created, $created,
                new DateTimeImmutable('@'.(1000 + $seconds - 1))));
            self::assertTrue(SessionLifetimePolicy::isExpired($kind, $created, $created,
                new DateTimeImmutable('@'.(1000 + $seconds))));
        }
        // Break-glass has no idle deadline, but its absolute deadline still applies.
        self::assertFalse(SessionLifetimePolicy::isExpired(SessionKind::BreakGlass,
            $created, $created, new DateTimeImmutable('@1899')));
        self::assertTrue(SessionLifetimePolicy::isExpired(SessionKind::BreakGlass,
            $created, $created, new DateTimeImmutable('@1900')));
    }
}
