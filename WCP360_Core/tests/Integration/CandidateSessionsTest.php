<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Psr\Clock\ClockInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class CandidateSessionsTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private CandidateSessions $sessions;
    private CandidateSessionClock $clock;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->clock = new CandidateSessionClock(1793534400);
        $this->sessions = new CandidateSessions($this->store, $this->clock);
    }

    public function testIssuanceSeparatesSurfacesAndNeverImplicitlyGrantsAdministratorMfa(): void
    {
        $alice = $this->fixture['users']['alice'];
        $admin = $this->fixture['users']['admin'];
        $user = $this->sessions->issue($alice, 'user', $this->sid(1));
        $pending = $this->sessions->issue($admin, 'admin', $this->sid(2));
        self::assertInstanceOf(\Symfony\Component\Uid\UuidV7::class, Uuid::fromString($user));
        self::assertNotSame($user, $pending);
        self::assertSame('WCP-USER', $this->row('user', $user)['surface_claim']);
        self::assertSame('WCP-ADMIN', $this->row('admin', $pending)['surface_claim']);
        self::assertSame(1, (int) $this->row('admin', $pending)['auth_strength']);
        self::assertSame($this->clock->timestamp + 43200, (int) $this->row('user', $user)['expires_at']);
        self::assertSame($this->clock->timestamp + 28800, (int) $this->row('admin', $pending)['expires_at']);
        self::assertTrue($this->sessions->current($alice, 'user', $user, $this->sid(1)));
        self::assertTrue($this->sessions->current($admin, 'admin', $pending, $this->sid(2)));
        self::assertFalse($this->sessions->current($admin, 'admin', $pending, $this->sid(2), 2));
        self::assertSame('active', $this->sessions->listing($alice, 'user', $user)['items'][0]['state']);
        self::assertSame('active', $this->sessions->listing($admin, 'admin', $pending)['items'][0]['state']);
        self::assertSame(['candidate.session.opened', 'candidate.session.opened'], array_column($this->audits(), 'kind'));
        self::assertSame([$user, $pending], array_column($this->audits(), 'resource_id'));
        self::assertSame([null, null], array_column($this->audits(), 'tenant_id'));
    }

    public function testBindingsAreDomainSeparatedAndRejectMalformedCookieIdentifiers(): void
    {
        $alice = $this->fixture['users']['alice'];
        $bob = $this->fixture['users']['bob'];
        $sid = $this->sid(1);
        $binding = CandidateSessions::binding($alice, 'user', $sid);
        self::assertMatchesRegularExpression('/\A[a-f0-9]{64}\z/D', $binding);
        self::assertNotSame($sid, $binding);
        self::assertSame($binding, CandidateSessions::binding($alice, 'user', $sid));
        self::assertNotSame($binding, CandidateSessions::binding($alice, 'admin', $sid));
        self::assertNotSame($binding, CandidateSessions::binding($bob, 'user', $sid));
        foreach ([str_repeat('a', 16), str_repeat('z', 256), 'aB9,-aB9,-aB9,-aB9,-'] as $valid) {
            self::assertSame(64, strlen(CandidateSessions::binding($alice, 'user', $valid)));
        }
        foreach (['', str_repeat('a', 15), str_repeat('a', 257), 'private-cookie/value', 'private_cookie_value', "private-cookie-value\n", "private-cookie-\0value", 'private-cookie-évalue'] as $invalid) {
            try {
                CandidateSessions::binding($alice, 'user', $invalid);
                self::fail('Malformed session identifier was accepted.');
            } catch (\InvalidArgumentException $error) {
                self::assertSame('Invalid candidate session binding.', $error->getMessage());
                self::assertStringNotContainsString($invalid === '' ? 'empty-cookie' : $invalid, $error->getMessage());
            }
            self::assertFalse($this->sessions->current($alice, 'user', WorkbenchStore::id(), $invalid));
        }
        self::assertSame([], $this->audits());
    }

    public function testCurrentSessionRequiresExactAccountSurfaceSelectorAndCookieWithoutAutoadoption(): void
    {
        $alice = $this->fixture['users']['alice'];
        $id = $this->sessions->issue($alice, 'user', $this->sid(1));
        foreach (['', 'invalid', WorkbenchStore::id()] as $unknown) {
            self::assertFalse($this->sessions->current($alice, 'user', $unknown, $this->sid(1)));
            self::assertFalse($this->sessions->current($unknown, 'user', $id, $this->sid(1)));
        }
        self::assertFalse($this->sessions->current($this->fixture['users']['bob'], 'user', $id, $this->sid(1)));
        self::assertFalse($this->sessions->current($alice, 'admin', $id, $this->sid(1)));
        self::assertFalse($this->sessions->current($alice, 'worker', $id, $this->sid(1)));
        self::assertFalse($this->sessions->current($alice, 'user', $id, $this->sid(2)));
        foreach ([-1, 0, 3, PHP_INT_MAX] as $strength) {
            self::assertFalse($this->sessions->current($alice, 'user', $id, $this->sid(1), $strength));
        }
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_user_sessions'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions'));
        self::assertCount(1, $this->audits());
    }

    public function testOnlyActiveAccountsOfTheExactSurfaceCanIssueOrManageSessions(): void
    {
        $alice = $this->fixture['users']['alice'];
        $admin = $this->fixture['users']['admin'];
        $id = $this->sessions->issue($alice, 'user', $this->sid(1));
        $this->denied(fn () => $this->sessions->issue($alice, 'admin', $this->sid(2)));
        $this->denied(fn () => $this->sessions->issue($admin, 'user', $this->sid(3)));
        $this->denied(fn () => $this->sessions->listing($admin, 'user', $id));
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $alice]);
        self::assertFalse($this->sessions->current($alice, 'user', $id, $this->sid(1)));
        $this->denied(fn () => $this->sessions->issue($alice, 'user', $this->sid(4)));
        $this->denied(fn () => $this->sessions->listing($alice, 'user', $id));
        $this->denied(fn () => $this->sessions->revoke($alice, 'user', $id));
        $this->denied(fn () => $this->sessions->revokeAll($alice, 'user'));
        self::assertNull($this->row('user', $id)['revoked_at']);
        self::assertTrue($this->sessions->retire($alice, 'user', $id, CandidateSessions::binding($alice, 'user', $this->sid(1))));
        self::assertFalse($this->sessions->retire($alice, 'user', $id, CandidateSessions::binding($alice, 'user', $this->sid(1))));
        $this->store->db->update('wcp_candidate_users', ['active' => 1], ['id' => $alice]);
        self::assertFalse($this->sessions->current($alice, 'user', $id, $this->sid(1)));
    }

    public function testChangingAnAccountSurfaceNeverMovesAnExistingSessionToTheOtherTable(): void
    {
        $alice = $this->fixture['users']['alice'];
        $id = $this->sessions->issue($alice, 'user', $this->sid(1));
        $this->store->db->update('wcp_candidate_users', ['context' => 'admin'], ['id' => $alice]);
        self::assertFalse($this->sessions->current($alice, 'user', $id, $this->sid(1)));
        self::assertFalse($this->sessions->current($alice, 'admin', $id, $this->sid(1)));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions'));
    }

    public function testIdleExpiryIsExactForBothSurfacesAndNeverRevivedByTouch(): void
    {
        foreach (['user' => ['alice', 1800], 'admin' => ['admin', 900]] as $surface => [$name, $idle]) {
            $actor = $this->fixture['users'][$name];
            $sid = $this->sid($idle);
            $id = $this->sessions->issue($actor, $surface, $sid);
            $this->clock->timestamp += $idle - 1;
            self::assertTrue($this->sessions->current($actor, $surface, $id, $sid, 1, false));
            ++$this->clock->timestamp;
            self::assertFalse($this->sessions->current($actor, $surface, $id, $sid));
            self::assertSame('expired', $this->sessions->listing($actor, $surface, $id)['items'][0]['state']);
        }
    }

    public function testActivityExtendsIdleTimeButNeverTheAbsoluteDeadline(): void
    {
        foreach (['user' => ['alice', 1799, 43200], 'admin' => ['admin', 899, 28800]] as $surface => [$name, $step, $absolute]) {
            $actor = $this->fixture['users'][$name];
            $sid = $this->sid($absolute);
            $created = $this->clock->timestamp;
            $id = $this->sessions->issue($actor, $surface, $sid);
            while ($this->clock->timestamp + $step < $created + $absolute) {
                $this->clock->timestamp += $step;
                self::assertTrue($this->sessions->current($actor, $surface, $id, $sid));
            }
            $this->clock->timestamp = $created + $absolute - 1;
            self::assertTrue($this->sessions->current($actor, $surface, $id, $sid));
            ++$this->clock->timestamp;
            self::assertFalse($this->sessions->current($actor, $surface, $id, $sid));
            self::assertSame($created, (int) $this->row($surface, $id)['created_at']);
            self::assertSame($created + $absolute, (int) $this->row($surface, $id)['expires_at']);
        }
        self::assertCount(2, $this->audits());
    }

    public function testReadOnlyValidationDoesNotTouchAndClockRegressionFailsClosed(): void
    {
        $actor = $this->fixture['users']['alice'];
        $created = $this->clock->timestamp;
        $id = $this->sessions->issue($actor, 'user', $this->sid(1));
        $this->clock->timestamp += 60;
        self::assertTrue($this->sessions->current($actor, 'user', $id, $this->sid(1), 1, false));
        self::assertSame($created, (int) $this->row('user', $id)['last_seen_at']);
        self::assertTrue($this->sessions->current($actor, 'user', $id, $this->sid(1)));
        self::assertSame($created + 60, (int) $this->row('user', $id)['last_seen_at']);
        --$this->clock->timestamp;
        self::assertFalse($this->sessions->current($actor, 'user', $id, $this->sid(1)));
        self::assertSame($created + 60, (int) $this->row('user', $id)['last_seen_at']);
        self::assertCount(1, $this->audits());
    }

    public function testAConcurrentHeartbeatDoesNotInvalidateTheEarlierRequestOrMoveActivityBackwards(): void
    {
        [$db, $store, $fixture] = $this->interleavingStore();
        try {
            $sessions = new CandidateSessions($store, $this->clock);
            $actor = $fixture['users']['alice'];
            $sid = $this->sid(700);
            $id = $sessions->issue($actor, 'user', $sid);
            ++$this->clock->timestamp;
            $newerActivity = $this->clock->timestamp + 1;
            $db->beforeSessionUpdate = static function (Connection $connection) use ($id, $newerActivity): void {
                $connection->executeStatement('UPDATE wcp_candidate_user_sessions SET last_seen_at = ? WHERE id = ?', [$newerActivity, $id]);
            };

            self::assertTrue($sessions->current($actor, 'user', $id, $sid));
            self::assertSame(1, $db->interleavings);
            self::assertNull($db->beforeSessionUpdate);
            self::assertSame($newerActivity, (int) $db->fetchOne('SELECT last_seen_at FROM wcp_candidate_user_sessions WHERE id = ?', [$id]));
            self::assertNull($db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id = ?', [$id]));
            self::assertSame(1, (int) $db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        } finally {
            $db->close();
        }
    }

    public function testConcurrentHeartbeatsCannotPreventAnyRevocationOrViolateItsTimestampConstraint(): void
    {
        foreach (['retire', 'revoke', 'revokeAll'] as $operation) {
            [$db, $store, $fixture] = $this->interleavingStore();
            try {
                $sessions = new CandidateSessions($store, $this->clock);
                $actor = $fixture['users']['alice'];
                $sid = $this->sid(701);
                $id = $sessions->issue($actor, 'user', $sid);
                $newerActivity = $this->clock->timestamp + 1;
                $db->beforeSessionUpdate = static function (Connection $connection) use ($id, $newerActivity): void {
                    $connection->executeStatement('UPDATE wcp_candidate_user_sessions SET last_seen_at = ? WHERE id = ?', [$newerActivity, $id]);
                };

                $result = match ($operation) {
                    'retire' => $sessions->retire($actor, 'user', $id, CandidateSessions::binding($actor, 'user', $sid)),
                    'revoke' => $sessions->revoke($actor, 'user', $id),
                    'revokeAll' => $sessions->revokeAll($actor, 'user'),
                };
                self::assertSame($operation === 'revokeAll' ? 1 : true, $result, $operation);
                self::assertSame(1, $db->interleavings, $operation);
                self::assertNull($db->beforeSessionUpdate);
                $row = $db->fetchAssociative('SELECT last_seen_at, revoked_at FROM wcp_candidate_user_sessions WHERE id = ?', [$id]);
                self::assertIsArray($row);
                self::assertSame($newerActivity, (int) $row['last_seen_at']);
                self::assertNotNull($row['revoked_at']);
                self::assertGreaterThanOrEqual((int) $row['last_seen_at'], (int) $row['revoked_at'], $operation);
                self::assertFalse($sessions->current($actor, 'user', $id, $sid));
                self::assertSame(['candidate.session.opened', 'candidate.session.revoked'], $db->fetchFirstColumn('SELECT kind FROM wcp_candidate_audit ORDER BY id'));
            } finally {
                $db->close();
            }
        }
    }

    public function testMfaUpgradeRotatesBindingWithoutResettingTheAbsoluteLifetime(): void
    {
        $admin = $this->fixture['users']['admin'];
        $id = $this->sessions->issue($admin, 'admin', $this->sid(1));
        $before = $this->row('admin', $id);
        $old = CandidateSessions::binding($admin, 'admin', $this->sid(1));
        $this->denied(fn () => $this->sessions->upgrade($admin, 'admin', $id, $old, $this->sid(1)));
        $this->denied(fn () => $this->sessions->upgrade($admin, 'admin', $id, str_repeat('0', 64), $this->sid(2)));
        $this->denied(fn () => $this->sessions->upgrade($admin, 'user', $id, $old, $this->sid(2)));
        $this->denied(fn () => $this->sessions->upgrade($this->fixture['users']['alice'], 'admin', $id, $old, $this->sid(2)));
        self::assertSame($before, $this->row('admin', $id));
        $this->clock->timestamp += 60;
        $this->sessions->upgrade($admin, 'admin', $id, $old, $this->sid(2));
        self::assertSame(2, (int) $this->row('admin', $id)['auth_strength']);
        self::assertSame($before['created_at'], $this->row('admin', $id)['created_at']);
        self::assertSame($before['expires_at'], $this->row('admin', $id)['expires_at']);
        self::assertFalse($this->sessions->current($admin, 'admin', $id, $this->sid(1)));
        self::assertTrue($this->sessions->current($admin, 'admin', $id, $this->sid(2), 2));
        self::assertSame('active', $this->sessions->listing($admin, 'admin', $id)['items'][0]['state']);
        self::assertSame(['candidate.session.opened', 'candidate.session.rotated'], array_column($this->audits(), 'kind'));
        $this->denied(fn () => $this->sessions->upgrade($admin, 'admin', $id, CandidateSessions::binding($admin, 'admin', $this->sid(2)), $this->sid(3)));
    }

    public function testRevokedOrExpiredPendingSessionCannotBeResurrectedByMfaCompletion(): void
    {
        $admin = $this->fixture['users']['admin'];
        $revoked = $this->sessions->issue($admin, 'admin', $this->sid(1));
        $old = CandidateSessions::binding($admin, 'admin', $this->sid(1));
        self::assertTrue($this->sessions->revoke($admin, 'admin', $revoked));
        $this->denied(fn () => $this->sessions->upgrade($admin, 'admin', $revoked, $old, $this->sid(2)));
        self::assertFalse($this->sessions->current($admin, 'admin', $revoked, $this->sid(2)));
        $expired = $this->sessions->issue($admin, 'admin', $this->sid(3));
        $this->clock->timestamp += 900;
        $this->denied(fn () => $this->sessions->upgrade($admin, 'admin', $expired, CandidateSessions::binding($admin, 'admin', $this->sid(3)), $this->sid(4)));
        self::assertSame(1, (int) $this->row('admin', $expired)['auth_strength']);
        self::assertSame(2, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_admin_sessions'));
    }

    public function testRetirementRequiresTheExactCurrentBindingAndIsIdempotent(): void
    {
        $actor = $this->fixture['users']['alice'];
        $id = $this->sessions->issue($actor, 'user', $this->sid(1));
        $binding = CandidateSessions::binding($actor, 'user', $this->sid(1));
        self::assertFalse($this->sessions->retire($actor, 'user', $id, str_repeat('0', 64)));
        self::assertFalse($this->sessions->retire($this->fixture['users']['bob'], 'user', $id, $binding));
        self::assertFalse($this->sessions->retire($actor, 'user', WorkbenchStore::id(), $binding));
        self::assertFalse($this->sessions->retire($actor, 'user', 'malformed', $binding));
        self::assertFalse($this->sessions->retire($actor, 'user', $id, 'malformed'));
        self::assertTrue($this->sessions->retire($actor, 'user', $id, $binding));
        self::assertFalse($this->sessions->retire($actor, 'user', $id, $binding));
        self::assertFalse($this->sessions->current($actor, 'user', $id, $this->sid(1)));
        self::assertSame('revoked', $this->sessions->listing($actor, 'user', $id)['items'][0]['state']);
        self::assertCount(2, $this->audits());
    }

    public function testRevokeOneOrAllNeverAffectsOtherAccountsOrSurfaces(): void
    {
        $alice = $this->fixture['users']['alice'];
        $bob = $this->fixture['users']['bob'];
        $admin = $this->fixture['users']['admin'];
        $first = $this->sessions->issue($alice, 'user', $this->sid(1));
        $second = $this->sessions->issue($alice, 'user', $this->sid(2));
        $other = $this->sessions->issue($bob, 'user', $this->sid(3));
        $privileged = $this->sessions->issue($admin, 'admin', $this->sid(4));
        $this->denied(fn () => $this->sessions->revoke($alice, 'user', $other));
        $this->denied(fn () => $this->sessions->revoke($alice, 'user', WorkbenchStore::id()));
        self::assertTrue($this->sessions->revoke($alice, 'user', $first));
        self::assertFalse($this->sessions->revoke($alice, 'user', $first));
        self::assertFalse($this->sessions->current($alice, 'user', $first, $this->sid(1)));
        self::assertTrue($this->sessions->current($alice, 'user', $second, $this->sid(2)));
        self::assertSame(1, $this->sessions->revokeAll($alice, 'user'));
        self::assertSame(0, $this->sessions->revokeAll($alice, 'user'));
        self::assertFalse($this->sessions->current($alice, 'user', $second, $this->sid(2)));
        self::assertTrue($this->sessions->current($bob, 'user', $other, $this->sid(3)));
        self::assertTrue($this->sessions->current($admin, 'admin', $privileged, $this->sid(4)));
        self::assertCount(6, $this->audits());
    }

    public function testRegistrySurvivesServiceReconstructionWithoutPersistingRawCookieIdentifiers(): void
    {
        $actor = $this->fixture['users']['alice'];
        $sid = 'PrivateCookieIdentifierMustNeverBePersisted123456789';
        $id = $this->sessions->issue($actor, 'user', $sid);
        $rebuilt = new CandidateSessions(new WorkbenchStore($this->store->db), $this->clock);
        self::assertTrue($rebuilt->current($actor, 'user', $id, $sid));
        $list = $rebuilt->listing($actor, 'user', $id);
        self::assertTrue($list['items'][0]['current']);
        self::assertSame(['id', 'created_at', 'last_seen_at', 'expires_at', 'revoked_at', 'state', 'current'], array_keys($list['items'][0]));
        $stored = json_encode([$this->row('user', $id), $this->audits(), $list], JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString($sid, $stored);
        self::assertStringNotContainsString(CandidateSessions::binding($actor, 'user', $sid), json_encode([$this->audits(), $list], JSON_THROW_ON_ERROR));
        self::assertTrue($rebuilt->revoke($actor, 'user', $id));
        self::assertFalse($this->sessions->current($actor, 'user', $id, $sid));
    }

    public function testReauthenticationQuotaIsDurableAndRestartsOnlyAfterFifteenMinutes(): void
    {
        $actor = $this->fixture['users']['alice'];
        $id = $this->sessions->issue($actor, 'user', $this->sid(1));
        $started = $this->clock->timestamp;
        for ($attempt = 1; $attempt <= 5; ++$attempt) {
            self::assertTrue($this->sessions->allowReauthentication($actor, 'user', $id, $this->sid(1)));
            self::assertSame($attempt, (int) $this->row('user', $id)['reauth_attempts']);
        }
        $full = $this->row('user', $id);
        self::assertFalse($this->sessions->allowReauthentication($actor, 'user', $id, $this->sid(1)));
        $rebuilt = new CandidateSessions(new WorkbenchStore($this->store->db), $this->clock);
        self::assertFalse($rebuilt->allowReauthentication($actor, 'user', $id, $this->sid(1)));
        self::assertSame($full, $this->row('user', $id));
        $this->clock->timestamp = $started + 899;
        self::assertFalse($rebuilt->allowReauthentication($actor, 'user', $id, $this->sid(1)));
        self::assertCount(6, $this->audits());
        ++$this->clock->timestamp;
        self::assertTrue($rebuilt->allowReauthentication($actor, 'user', $id, $this->sid(1)));
        self::assertSame(1, (int) $this->row('user', $id)['reauth_attempts']);
        self::assertSame($started + 900, (int) $this->row('user', $id)['reauth_window_started']);
        self::assertCount(7, $this->audits());
        self::assertSame('candidate.session.reauthentication_attempted', $this->audits()[6]['kind']);
    }

    public function testReauthenticationCannotConsumeAnotherBindingOrAnInvalidSession(): void
    {
        $actor = $this->fixture['users']['alice'];
        $id = $this->sessions->issue($actor, 'user', $this->sid(1));
        $before = $this->row('user', $id);
        foreach ([
            fn () => $this->sessions->allowReauthentication($actor, 'user', $id, $this->sid(2)),
            fn () => $this->sessions->allowReauthentication($this->fixture['users']['bob'], 'user', $id, $this->sid(1)),
            fn () => $this->sessions->allowReauthentication($actor, 'admin', $id, $this->sid(1)),
            fn () => $this->sessions->allowReauthentication($actor, 'worker', $id, $this->sid(1)),
            fn () => $this->sessions->allowReauthentication($actor, 'user', WorkbenchStore::id(), $this->sid(1)),
            fn () => $this->sessions->allowReauthentication($actor, 'user', $id, 'invalid-cookie'),
        ] as $operation) {
            self::assertFalse($operation());
            self::assertSame($before, $this->row('user', $id));
        }
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $actor]);
        self::assertFalse($this->sessions->allowReauthentication($actor, 'user', $id, $this->sid(1)));
        $this->store->db->update('wcp_candidate_users', ['active' => 1], ['id' => $actor]);
        $this->clock->timestamp += 1800;
        self::assertFalse($this->sessions->allowReauthentication($actor, 'user', $id, $this->sid(1)));
        self::assertSame($before, $this->row('user', $id));
        self::assertSame(0, (int) $this->row('user', $id)['reauth_attempts']);
        self::assertCount(1, $this->audits());
    }

    public function testMfaRotationPreservesConsumedReauthenticationAttempts(): void
    {
        $admin = $this->fixture['users']['admin'];
        $id = $this->sessions->issue($admin, 'admin', $this->sid(1));
        self::assertTrue($this->sessions->allowReauthentication($admin, 'admin', $id, $this->sid(1)));
        self::assertTrue($this->sessions->allowReauthentication($admin, 'admin', $id, $this->sid(1)));
        $started = $this->row('admin', $id)['reauth_window_started'];
        $this->sessions->upgrade($admin, 'admin', $id, CandidateSessions::binding($admin, 'admin', $this->sid(1)), $this->sid(2));
        self::assertSame(2, (int) $this->row('admin', $id)['reauth_attempts']);
        self::assertSame($started, $this->row('admin', $id)['reauth_window_started']);
        self::assertFalse($this->sessions->allowReauthentication($admin, 'admin', $id, $this->sid(1)));
        for ($attempt = 3; $attempt <= 5; ++$attempt) {
            self::assertTrue($this->sessions->allowReauthentication($admin, 'admin', $id, $this->sid(2)));
        }
        self::assertFalse($this->sessions->allowReauthentication($admin, 'admin', $id, $this->sid(2)));
        self::assertSame(5, (int) $this->row('admin', $id)['reauth_attempts']);
        self::assertTrue($this->sessions->revoke($admin, 'admin', $id));
        self::assertFalse($this->sessions->allowReauthentication($admin, 'admin', $id, $this->sid(2)));
    }

    public function testInventoryHasStableBoundedPaginationAndNoForeignRows(): void
    {
        $actor = $this->fixture['users']['alice'];
        $ids = [];
        for ($i = 0; $i < 101; ++$i) {
            $ids[] = $this->sessions->issue($actor, 'user', $this->sid($i));
        }
        $other = $this->sessions->issue($this->fixture['users']['bob'], 'user', $this->sid(1000));
        $first = $this->sessions->listing($actor, 'user', $ids[0]);
        $second = $this->sessions->listing($actor, 'user', $ids[0], 2);
        self::assertCount(100, $first['items']);
        self::assertTrue($first['has_next']);
        self::assertSame(1, $first['page']);
        self::assertCount(1, $second['items']);
        self::assertFalse($second['has_next']);
        self::assertSame(2, $second['page']);
        self::assertSame($first, $this->sessions->listing($actor, 'user', $ids[0]));
        $items = [...$first['items'], ...$second['items']];
        self::assertCount(101, array_unique(array_column($items, 'id')));
        self::assertNotContains($other, array_column($items, 'id'));
        self::assertCount(1, array_filter($items, static fn (array $item): bool => $item['current']));
        self::assertSame(['items' => [], 'page' => 3, 'has_next' => false], $this->sessions->listing($actor, 'user', $ids[0], 3));
        foreach ([0, -1, 10001, PHP_INT_MAX] as $invalid) {
            try {
                $this->sessions->listing($actor, 'user', $ids[0], $invalid);
                self::fail('Unbounded page accepted.');
            } catch (\InvalidArgumentException) {
                self::assertCount(102, $this->audits());
            }
        }
    }

    public function testAllSessionMutationsRollbackTogetherWithTheirAudit(): void
    {
        $alice = $this->fixture['users']['alice'];
        $admin = $this->fixture['users']['admin'];
        $first = $this->sessions->issue($alice, 'user', $this->sid(1));
        $second = $this->sessions->issue($alice, 'user', $this->sid(2));
        $pending = $this->sessions->issue($admin, 'admin', $this->sid(3));
        $before = $this->snapshot();
        $this->rejectAuditInserts();
        try {
            foreach ([
                fn () => $this->sessions->issue($alice, 'user', $this->sid(4)),
                fn () => $this->sessions->upgrade($admin, 'admin', $pending, CandidateSessions::binding($admin, 'admin', $this->sid(3)), $this->sid(5)),
                fn () => $this->sessions->revoke($alice, 'user', $first),
                fn () => $this->sessions->retire($alice, 'user', $second, CandidateSessions::binding($alice, 'user', $this->sid(2))),
                fn () => $this->sessions->revokeAll($alice, 'user'),
                fn () => $this->sessions->allowReauthentication($alice, 'user', $first, $this->sid(1)),
            ] as $operation) {
                try {
                    $operation();
                    self::fail('An unaudited session mutation committed.');
                } catch (Exception) {
                    self::assertSame($before, $this->snapshot());
                }
            }
        } finally {
            $this->allowAuditInserts();
        }
        self::assertTrue($this->sessions->current($alice, 'user', $first, $this->sid(1)));
        self::assertTrue($this->sessions->current($alice, 'user', $second, $this->sid(2)));
        self::assertTrue($this->sessions->current($admin, 'admin', $pending, $this->sid(3)));
    }

    public function testUnknownSurfacesAreRejectedBeforeTheyCanSelectATable(): void
    {
        $actor = $this->fixture['users']['alice'];
        foreach (['', 'USER', 'worker', 'setup', 'user_sessions', "user'; DROP TABLE wcp_candidate_users; --"] as $surface) {
            try {
                $this->sessions->issue($actor, $surface, $this->sid(1));
                self::fail('Unknown surface accepted.');
            } catch (\InvalidArgumentException $error) {
                self::assertSame('Invalid candidate session surface.', $error->getMessage());
            }
            self::assertFalse($this->sessions->current($actor, $surface, WorkbenchStore::id(), $this->sid(1)));
        }
        self::assertSame(3, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_users'));
        self::assertSame([], $this->audits());
    }

    private function sid(int $number): string
    {
        return hash('sha256', 'candidate-session-test-cookie-'.$number);
    }

    /** @return array{CandidateSessionInterleavingConnection, WorkbenchStore, array} */
    private function interleavingStore(): array
    {
        // Isolated real SQL storage: this schedule runs identically in SQLite and PostgreSQL CI jobs.
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true, 'wrapperClass' => CandidateSessionInterleavingConnection::class]);
        $store = new WorkbenchStore($db);

        return [$db, $store, WorkbenchFixture::reset($db)];
    }

    private function row(string $surface, string $selector): array
    {
        $row = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_'.$surface.'_sessions WHERE id = ?', [$selector]);
        self::assertIsArray($row);

        return $row;
    }

    private function audits(): array
    {
        return $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
    }

    private function snapshot(): array
    {
        return [
            $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_user_sessions ORDER BY id'),
            $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_admin_sessions ORDER BY id'),
            $this->audits(),
        ];
    }

    private function denied(callable $operation): void
    {
        try {
            $operation();
            self::fail('Unauthorized session mutation accepted.');
        } catch (AccessDeniedHttpException $error) {
            self::assertSame(403, $error->getStatusCode());
        }
    }

    private function rejectAuditInserts(): void
    {
        $db = $this->store->db;
        if ($db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $db->executeStatement("CREATE OR REPLACE FUNCTION wcp_test_session_audit_unavailable() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN RAISE EXCEPTION ''test audit unavailable''; END;'");
            $db->executeStatement('CREATE TRIGGER wcp_test_session_audit_unavailable BEFORE INSERT ON wcp_candidate_audit FOR EACH ROW EXECUTE FUNCTION wcp_test_session_audit_unavailable()');
        } else {
            $db->executeStatement("CREATE TRIGGER wcp_test_session_audit_unavailable BEFORE INSERT ON wcp_candidate_audit BEGIN SELECT RAISE(ABORT, 'test audit unavailable'); END");
        }
    }

    private function allowAuditInserts(): void
    {
        $db = $this->store->db;
        if ($db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $db->executeStatement('DROP TRIGGER wcp_test_session_audit_unavailable ON wcp_candidate_audit');
            $db->executeStatement('DROP FUNCTION wcp_test_session_audit_unavailable()');
        } else {
            $db->executeStatement('DROP TRIGGER wcp_test_session_audit_unavailable');
        }
    }
}

final class CandidateSessionClock implements ClockInterface
{
    public function __construct(public int $timestamp) {}

    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('@'.$this->timestamp);
    }
}

/** Test-only scheduler: execute a real competing SQL write after the service read and before its write. */
final class CandidateSessionInterleavingConnection extends Connection
{
    public ?\Closure $beforeSessionUpdate = null;
    public int $interleavings = 0;

    public function executeStatement(string $sql, array $params = [], array $types = []): int|string
    {
        if ($this->beforeSessionUpdate !== null && str_starts_with($sql, 'UPDATE wcp_candidate_user_sessions SET ')) {
            $callback = $this->beforeSessionUpdate;
            $this->beforeSessionUpdate = null;
            ++$this->interleavings;
            $callback($this);
        }

        return parent::executeStatement($sql, $params, $types);
    }
}
