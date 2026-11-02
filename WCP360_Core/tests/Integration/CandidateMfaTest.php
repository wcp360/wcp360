<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class CandidateMfaTest extends KernelTestCase
{
    private const KEY = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    private WorkbenchStore $store;
    private CandidateMfa $mfa;
    private CandidateMfaClock $clock;
    private string $admin;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->admin = $this->fixture['users']['admin'];
        $this->clock = new CandidateMfaClock(1793534400);
        $this->mfa = new CandidateMfa($this->store, self::KEY, $this->clock);
    }

    public function testMissingOrMalformedExplicitKeyFailsClosedWithoutCreatingFactors(): void
    {
        foreach (['', 'private-invalid-key', str_repeat('g', 64), str_repeat('a', 63), str_repeat('a', 64)."\n"] as $key) {
            $service = new CandidateMfa($this->store, $key, $this->clock);
            self::assertSame(['enabled' => false, 'revision' => 0, 'configured' => false], $service->status($this->admin));
            $this->unavailable(fn () => $service->startEnrollment($this->admin));
            $this->unavailable(fn () => $service->check($this->admin, '123456'));
            $this->unavailable(fn () => $service->reset($this->admin));
        }
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa'));
    }

    public function testEnrollmentSealsSeedAndOnlyReturnsRecoveryCodesOnConfirmation(): void
    {
        self::assertSame(['enabled' => false, 'revision' => 0, 'configured' => true], $this->mfa->status($this->admin));
        $pending = $this->mfa->startEnrollment($this->admin);
        self::assertSame($this->clock->timestamp + 300, $pending['expires_at']);
        self::assertStringStartsWith('otpauth://totp/', $pending['uri']);
        self::assertStringContainsString('secret='.$pending['secret'], $pending['uri']);
        self::assertFalse($this->mfa->status($this->admin)['enabled']);
        self::assertStringStartsWith('v1.', $this->factor()['pending_secret']);
        self::assertStringNotContainsString($pending['secret'], json_encode($this->factor(), JSON_THROW_ON_ERROR));

        $result = $this->mfa->check($this->admin, $this->code($pending['secret']), 1);
        self::assertNotNull($result);
        self::assertSame('enrollment', $result['method']);
        self::assertSame(2, $result['revision']);
        self::assertCount(10, $result['recovery_codes']);
        self::assertCount(10, array_unique($result['recovery_codes']));
        self::assertSame(['enabled' => true, 'revision' => 2, 'configured' => true], $this->mfa->status($this->admin));
        self::assertNull($this->factor()['pending_secret']);
        self::assertNull($this->factor()['pending_expires_at']);
        self::assertSame(intdiv($this->clock->timestamp, 30), (int) $this->factor()['last_counter']);
        $stored = json_encode([
            $this->factor(),
            $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery'),
            $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit'),
        ], JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString($pending['secret'], $stored);
        foreach ($result['recovery_codes'] as $code) {
            self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/D', $code);
            self::assertStringNotContainsString($code, $stored);
        }

        self::assertNull($this->mfa->check($this->admin, $this->code($pending['secret']), 2));
        $this->clock->timestamp += 30;
        self::assertSame(['revision' => 2, 'method' => 'totp'], $this->mfa->check($this->admin, $this->code($pending['secret']), 2));
    }

    public function testActiveFactorCannotBeRetrievedThroughEnrollment(): void
    {
        $this->enroll();
        $before = $this->factor();
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('MFA enrollment is unavailable.');
        try {
            $this->mfa->startEnrollment($this->admin);
        } finally {
            self::assertSame($before, $this->factor());
            self::assertCount(3, $this->mfa->status($this->admin));
        }
    }

    public function testOneStepClockDriftAndMonotonicReplayCounter(): void
    {
        [$pending] = $this->enroll();
        $this->clock->timestamp += 90;
        foreach ([-30, 0, 30] as $offset) {
            $code = $this->code($pending['secret'], $this->clock->timestamp + $offset);
            self::assertSame(['revision' => 2, 'method' => 'totp'], $this->mfa->check($this->admin, $code, 2));
            self::assertNull($this->mfa->check($this->admin, $code, 2));
        }
        $this->clock->timestamp += 120;
        $outside = $this->distinctOutsideCode($pending['secret'], $this->clock->timestamp);
        self::assertNull($this->mfa->check($this->admin, $outside, 2));
    }

    public function testFailedChallengesRemainThrottledAcrossServiceInstancesUntilWindowEnds(): void
    {
        [$pending] = $this->enroll();
        $this->clock->timestamp += 30;
        for ($i = 0; $i < 5; $i++) {
            self::assertNull($this->mfa->check($this->admin, 'malformed', 2));
        }
        self::assertSame(5, (int) $this->factor()['failed_attempts']);
        $reopened = new CandidateMfa($this->store, self::KEY, $this->clock);
        self::assertNull($reopened->check($this->admin, $this->code($pending['secret']), 2));
        self::assertSame(5, (int) $this->factor()['failed_attempts']);
        $this->clock->timestamp += 899;
        self::assertNull($reopened->check($this->admin, $this->code($pending['secret']), 2));
        $this->clock->timestamp++;
        self::assertSame(['revision' => 2, 'method' => 'totp'], $reopened->check($this->admin, $this->code($pending['secret']), 2));
        self::assertSame(0, (int) $this->factor()['failed_attempts']);
    }

    public function testEnrollmentRestartsHaveIndependentDurableLimitAndKeepFailureCounters(): void
    {
        $previous = null;
        for ($i = 0; $i < 5; $i++) {
            $pending = $this->mfa->startEnrollment($this->admin);
            if ($previous !== null) {
                self::assertNotSame($previous, $pending['secret']);
            }
            $previous = $pending['secret'];
            self::assertNull($this->mfa->check($this->admin, 'bad-code'));
        }
        self::assertSame(5, (int) $this->factor()['failed_attempts']);
        self::assertSame(5, (int) $this->factor()['enrollment_attempts']);
        $before = $this->factor();
        try {
            (new CandidateMfa($this->store, self::KEY, $this->clock))->startEnrollment($this->admin);
            self::fail('Sixth enrollment restart was accepted.');
        } catch (\RuntimeException $error) {
            self::assertSame('MFA enrollment is unavailable.', $error->getMessage());
        }
        self::assertSame($before, $this->factor());
        self::assertNull($this->mfa->check($this->admin, $this->code($pending['secret'])));
        $this->clock->timestamp += 900;
        $new = $this->mfa->startEnrollment($this->admin);
        self::assertSame(1, (int) $this->factor()['enrollment_attempts']);
        self::assertSame(5, (int) $this->factor()['failed_attempts']);
        self::assertNotNull($this->mfa->check($this->admin, $this->code($new['secret'])));
    }

    public function testPendingSeedExpiresAtExactDeadlineAndCanBeReplaced(): void
    {
        $pending = $this->mfa->startEnrollment($this->admin);
        $this->clock->timestamp += 300;
        self::assertNull($this->mfa->check($this->admin, $this->code($pending['secret'])));
        self::assertFalse($this->mfa->status($this->admin)['enabled']);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
        $new = $this->mfa->startEnrollment($this->admin);
        self::assertNotSame($pending['secret'], $new['secret']);
        self::assertNotNull($this->mfa->check($this->admin, $this->code($new['secret'])));
    }

    public function testRecoveryCodeIsSingleUseAndInvalidatesEarlierAuthenticationRevision(): void
    {
        [$pending, $enrolled] = $this->enroll();
        $code = $enrolled['recovery_codes'][0];
        self::assertSame(['revision' => 3, 'method' => 'recovery'], $this->mfa->check($this->admin, $code, 2));
        self::assertSame(9, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
        self::assertNull($this->mfa->check($this->admin, $code, 3));
        self::assertNull($this->mfa->check($this->admin, $enrolled['recovery_codes'][1], 2));
        self::assertSame(9, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
        self::assertSame(['revision' => 4, 'method' => 'recovery'], $this->mfa->check($this->admin, $enrolled['recovery_codes'][1], 3));
        $this->clock->timestamp += 30;
        self::assertNull($this->mfa->check($this->admin, $this->code($pending['secret']), 3));
        self::assertSame(['revision' => 4, 'method' => 'totp'], $this->mfa->check($this->admin, $this->code($pending['secret']), 4));
    }

    public function testStaleRevisionCannotConsumePendingEnrollmentOrRecoveryCode(): void
    {
        $pending = $this->mfa->startEnrollment($this->admin);
        $before = $this->factor();
        self::assertNull($this->mfa->check($this->admin, $this->code($pending['secret']), 0));
        self::assertSame($before, $this->factor());
        self::assertNotNull($this->mfa->check($this->admin, $this->code($pending['secret']), 1));
        self::assertNull($this->mfa->check($this->admin, 'not-a-code', 1));
        self::assertSame(0, (int) $this->factor()['failed_attempts']);
    }

    public function testResetRetainsMonotonicRevisionAndDestroysBothSecretStatesAndAllCodes(): void
    {
        [, $enrolled] = $this->enroll();
        $this->mfa->reset($this->admin);
        self::assertSame(['enabled' => false, 'revision' => 3, 'configured' => true], $this->mfa->status($this->admin));
        self::assertNull($this->factor()['sealed_secret']);
        self::assertNull($this->factor()['pending_secret']);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
        self::assertNull($this->mfa->check($this->admin, $enrolled['recovery_codes'][0]));
        $pending = $this->mfa->startEnrollment($this->admin);
        self::assertSame(3, $this->mfa->status($this->admin)['revision']);
        self::assertNull($this->mfa->check($this->admin, $this->code($pending['secret']), 2));
        self::assertSame(4, $this->mfa->check($this->admin, $this->code($pending['secret']), 3)['revision']);
        $this->mfa->reset($this->admin);
        $this->mfa->reset($this->admin);
        self::assertSame(6, $this->mfa->status($this->admin)['revision']);
    }

    public function testInactiveUserOrUserSurfaceIdentityNeverObtainsFactorAuthority(): void
    {
        $this->enroll();
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->admin]);
        foreach ([$this->admin, $this->fixture['users']['alice'], WorkbenchStore::id()] as $actor) {
            foreach ([fn () => $this->mfa->status($actor), fn () => $this->mfa->startEnrollment($actor), fn () => $this->mfa->check($actor, '123456'), fn () => $this->mfa->reset($actor)] as $action) {
                try {
                    $action();
                    self::fail('Unauthorized MFA operation was accepted.');
                } catch (AccessDeniedHttpException) {
                    self::assertTrue(true);
                }
            }
        }
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa'));
        self::assertSame(10, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
    }

    public function testEncryptedSeedRejectsChangedKeyOrActorBindingWithoutExposingSecret(): void
    {
        [$pending] = $this->enroll();
        $this->clock->timestamp += 30;
        $wrongKey = new CandidateMfa($this->store, str_repeat('b', 64), $this->clock);
        self::assertFalse($wrongKey->status($this->admin)['configured']);
        self::assertTrue($this->mfa->status($this->admin)['configured']);
        $this->unavailable(fn () => $wrongKey->check($this->admin, $this->code($pending['secret'])));
        $other = WorkbenchStore::id();
        $this->store->db->insert('wcp_candidate_users', ['id' => $other, 'username' => 'other_admin', 'email' => 'other-admin@example.test', 'password_hash' => 'synthetic-unused', 'context' => 'admin', 'active' => 1]);
        $this->mfa->startEnrollment($other);
        $this->store->db->update('wcp_candidate_mfa', ['sealed_secret' => $this->factor()['sealed_secret'], 'pending_secret' => null, 'pending_expires_at' => null], ['user_id' => $other]);
        $this->unavailable(fn () => $this->mfa->check($other, $this->code($pending['secret'])));
        self::assertSame(2, $this->mfa->status($this->admin)['revision']);
        self::assertSame(10, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
    }

    public function testAuditFailureRollsBackRecoveryConsumptionAndRevision(): void
    {
        [, $enrolled] = $this->enroll();
        $sqlite = $this->store->db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform;
        if ($sqlite) {
            $this->store->db->executeStatement("CREATE TRIGGER reject_mfa_audit BEFORE INSERT ON wcp_candidate_audit BEGIN SELECT RAISE(ABORT,'audit unavailable'); END");
        } else {
            $this->store->db->executeStatement("CREATE OR REPLACE FUNCTION wcp_candidate_reject_mfa_audit() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN RAISE EXCEPTION ''audit unavailable''; END;'");
            $this->store->db->executeStatement('CREATE TRIGGER reject_mfa_audit BEFORE INSERT ON wcp_candidate_audit FOR EACH ROW EXECUTE FUNCTION wcp_candidate_reject_mfa_audit()');
        }
        try {
            $this->mfa->check($this->admin, $enrolled['recovery_codes'][0]);
            self::fail('MFA success without durable audit was accepted.');
        } catch (\Doctrine\DBAL\Exception) {
            self::assertSame(2, $this->mfa->status($this->admin)['revision']);
            self::assertSame(10, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_mfa_recovery'));
        } finally {
            $this->store->db->executeStatement('DROP TRIGGER reject_mfa_audit'.($sqlite ? '' : ' ON wcp_candidate_audit'));
            if (!$sqlite) {
                $this->store->db->executeStatement('DROP FUNCTION wcp_candidate_reject_mfa_audit()');
            }
        }
        self::assertSame(['revision' => 3, 'method' => 'recovery'], $this->mfa->check($this->admin, $enrolled['recovery_codes'][0]));
    }

    /** @return array{array{secret: string, uri: string, expires_at: int}, array{revision: int, method: string, recovery_codes: list<string>}} */
    private function enroll(): array
    {
        $pending = $this->mfa->startEnrollment($this->admin);
        $result = $this->mfa->check($this->admin, $this->code($pending['secret']));
        self::assertNotNull($result);
        self::assertSame('enrollment', $result['method']);

        return [$pending, $result];
    }

    private function code(string $secret, ?int $timestamp = null): string
    {
        return TOTP::createFromSecret($secret, $this->clock)->at($timestamp ?? $this->clock->timestamp);
    }

    private function distinctOutsideCode(string $secret, int $timestamp): string
    {
        $inside = [$this->code($secret, $timestamp - 30), $this->code($secret, $timestamp), $this->code($secret, $timestamp + 30)];
        for ($offset = 60; $offset <= 600; $offset += 30) {
            $candidate = $this->code($secret, $timestamp + $offset);
            if (!in_array($candidate, $inside, true)) {
                return $candidate;
            }
        }
        throw new \RuntimeException('Could not choose a distinct test code.');
    }

    private function factor(): array
    {
        $row = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id = ?', [$this->admin]);
        self::assertIsArray($row);

        return $row;
    }

    private function unavailable(callable $action): void
    {
        try {
            $action();
            self::fail('Unavailable MFA configuration was accepted.');
        } catch (\RuntimeException $error) {
            self::assertSame('MFA is unavailable.', $error->getMessage());
        }
    }
}

final class CandidateMfaClock implements ClockInterface
{
    public function __construct(public int $timestamp) {}

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('@'.$this->timestamp);
    }
}
