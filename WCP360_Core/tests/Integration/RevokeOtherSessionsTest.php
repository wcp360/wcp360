<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Application\RevokeOtherSessions;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\RecoveryAccountSnapshot;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class RevokeOtherSessionsTest extends KernelTestCase
{
    private const SID = 'synthetic-current-session-2026';
    private const KEY = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    private WorkbenchStore $store;
    private CandidateSessions $sessions;
    private CandidateMfa $mfa;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->sessions = new CandidateSessions($this->store);
        $this->mfa = new CandidateMfa($this->store, self::KEY);
    }

    public function testAllOtherRegistrationsAreRevokedBeyondTheFirstInventoryPage(): void
    {
        $actor = $this->actor('alice', 'user');
        $current = $this->sessions->issue($actor->id, 'user', self::SID);
        for ($i = 0; $i < 120; ++$i) $this->sessions->issue($actor->id, 'user', hash('sha256', 'other-session-'.$i));
        $old = $this->sessions->issue($actor->id, 'user', str_repeat('o', 32));
        $this->sessions->revoke($actor->id, 'user', $old);
        $oldRevocation = $this->store->db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id=?', [$old]);
        $bob = $this->sessions->issue($this->fixture['users']['bob'], 'user', str_repeat('b', 32));
        $admin = $this->sessions->issue($this->fixture['users']['admin'], 'admin', str_repeat('a', 32));
        $before = $this->row('user', $current);

        self::assertSame(120, $this->service('user')->revoke($actor, $current, self::SID));
        self::assertSame($before, $this->row('user', $current));
        self::assertTrue($this->sessions->current($actor->id, 'user', $current, self::SID, 1, false));
        self::assertSame($oldRevocation, $this->row('user', $old)['revoked_at']);
        self::assertTrue($this->sessions->current($this->fixture['users']['bob'], 'user', $bob, str_repeat('b', 32), 1, false));
        self::assertTrue($this->sessions->current($this->fixture['users']['admin'], 'admin', $admin, str_repeat('a', 32), 1, false));
        self::assertSame(1, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind='candidate.session.others_revoked' AND resource_id=?", [$current]));
        self::assertSame(0, $this->service('user')->revoke($actor, $current, self::SID));
        self::assertSame(1, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind='candidate.session.others_revoked'"));
        // This is a snapshot revocation, not a ban on subsequent logins.
        $new = $this->sessions->issue($actor->id, 'user', str_repeat('n', 32));
        self::assertTrue($this->sessions->current($actor->id, 'user', $new, str_repeat('n', 32), 1, false));
    }

    public function testPasswordOnlyAdministratorNeedsNoEncryptionKeyOrFactor(): void
    {
        $actor = $this->actor('admin', 'admin');
        $current = $this->sessions->issue($actor->id, 'admin', self::SID);
        $other = $this->sessions->issue($actor->id, 'admin', str_repeat('o', 32));
        $service = new RevokeOtherSessions($this->store, $this->sessions, new CandidateMfa($this->store, ''), $this->runtime('admin'));
        self::assertSame(1, $service->revoke($actor, $current, self::SID, $this->proof($actor->id)));
        self::assertNull($this->row('admin', $current)['revoked_at']);
        self::assertNotNull($this->row('admin', $other)['revoked_at']);
        self::assertFalse($this->mfa->status($actor->id)['enabled']);
    }

    public function testEnabledMfaIsPreservedAndOtherPendingSessionsAreRevoked(): void
    {
        $actor = $this->actor('admin', 'admin');
        $enrollment = $this->mfa->startEnrollment($actor->id);
        $result = $this->mfa->check($actor->id, TOTP::createFromSecret($enrollment['secret'])->now(), 1);
        self::assertNotNull($result);
        $current = $this->sessions->issue($actor->id, 'admin', str_repeat('p', 32));
        $this->sessions->upgrade($actor->id, 'admin', $current, CandidateSessions::binding($actor->id, 'admin', str_repeat('p', 32)), self::SID);
        $pending = $this->sessions->issue($actor->id, 'admin', str_repeat('o', 32));
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa');
        $codes = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery ORDER BY code_hash');
        self::assertSame(1, $this->service('admin')->revoke($actor, $current, self::SID, $this->proof($actor->id, $result['revision'])));
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa'));
        self::assertSame($codes, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery ORDER BY code_hash'));
        self::assertTrue($this->sessions->current($actor->id, 'admin', $current, self::SID, 2, false));
        self::assertNotNull($this->row('admin', $pending)['revoked_at']);
        $audit = serialize($this->store->db->fetchAllAssociative("SELECT * FROM wcp_candidate_audit WHERE kind='candidate.session.others_revoked'"));
        foreach ([self::SID, $enrollment['secret'], $actor->getPassword(), ...$result['recovery_codes']] as $secret) self::assertStringNotContainsString($secret, $audit);
    }

    public function testAdministratorProofAndRequiredFactorAreRechecked(): void
    {
        $actor = $this->actor('admin', 'admin');
        $current = $this->sessions->issue($actor->id, 'admin', self::SID);
        $other = $this->sessions->issue($actor->id, 'admin', str_repeat('o', 32));
        foreach ([null, ['actor' => $actor->id, 'revision' => 0, 'at' => time() - 300],
            ['actor' => $actor->id, 'revision' => 0, 'at' => time() + 60],
            ['actor' => $actor->id, 'revision' => 1, 'at' => time()],
            ['actor' => $this->fixture['users']['alice'], 'revision' => 0, 'at' => time()]] as $proof) {
            $this->refused(fn () => $this->service('admin')->revoke($actor, $current, self::SID, $proof), 403);
        }
        $setup = $this->mfa->startEnrollment($actor->id);
        $result = $this->mfa->check($actor->id, TOTP::createFromSecret($setup['secret'])->now(), 1);
        self::assertNotNull($result);
        // Even a fresh matching revision cannot make a strength-one registration sufficient.
        $this->refused(fn () => $this->service('admin')->revoke($actor, $current, self::SID, $this->proof($actor->id, $result['revision'])), 403);
        self::assertNull($this->row('admin', $other)['revoked_at']);
    }

    public function testForeignMissingMalformedAndIncorrectBindingsCannotChooseTheRetainedSession(): void
    {
        $actor = $this->actor('alice', 'user');
        $current = $this->sessions->issue($actor->id, 'user', self::SID);
        $other = $this->sessions->issue($actor->id, 'user', str_repeat('o', 32));
        $foreign = $this->sessions->issue($this->fixture['users']['bob'], 'user', str_repeat('b', 32));
        foreach ([[$other, self::SID], [$foreign, self::SID], [WorkbenchStore::id(), self::SID],
            ['invalid', self::SID], [$current, 'invalid'], [$current, str_repeat('x', 32)]] as [$selector, $sid]) {
            $this->refused(fn () => $this->service('user')->revoke($actor, $selector, $sid), 403);
        }
        self::assertNull($this->row('user', $current)['revoked_at']);
        self::assertNull($this->row('user', $other)['revoked_at']);
    }

    public function testChangedCredentialsInactiveAccountsAndWrongSurfaceAreRefused(): void
    {
        $actor = $this->actor('alice', 'user');
        $current = $this->sessions->issue($actor->id, 'user', self::SID);
        $other = $this->sessions->issue($actor->id, 'user', str_repeat('o', 32));
        $this->refused(fn () => $this->service('admin')->revoke($actor, $current, self::SID), 403);
        $this->store->db->update('wcp_candidate_users', ['password_hash' => 'changed-synthetic-verifier'], ['id' => $actor->id]);
        $this->refused(fn () => $this->service('user')->revoke($actor, $current, self::SID), 403);
        $this->store->db->update('wcp_candidate_users', ['password_hash' => $actor->getPassword(), 'active' => 0], ['id' => $actor->id]);
        $this->refused(fn () => $this->service('user')->revoke($actor, $current, self::SID), 403);
        self::assertNull($this->row('user', $other)['revoked_at']);
    }

    public function testRevokedOrExpiredCurrentRegistrationCannotRevokeOthers(): void
    {
        $actor = $this->actor('alice', 'user');
        $current = $this->sessions->issue($actor->id, 'user', self::SID);
        $other = $this->sessions->issue($actor->id, 'user', str_repeat('o', 32));
        $this->sessions->revoke($actor->id, 'user', $current);
        $this->refused(fn () => $this->service('user')->revoke($actor, $current, self::SID), 403);
        $this->store->db->update('wcp_candidate_user_sessions', [
            'revoked_at' => null, 'created_at' => time() - 50000, 'last_seen_at' => time() - 50000,
            'expires_at' => time() - 50000 + 43200,
        ], ['id' => $current]);
        $this->refused(fn () => $this->service('user')->revoke($actor, $current, self::SID), 403);
        self::assertNull($this->row('user', $other)['revoked_at']);
    }

    public function testCallerTransactionIsNotAdopted(): void
    {
        $actor = $this->actor('alice', 'user');
        $current = $this->sessions->issue($actor->id, 'user', self::SID);
        $this->store->db->beginTransaction();
        try {
            $this->refused(fn () => $this->service('user')->revoke($actor, $current, self::SID), 503);
            self::assertTrue($this->store->db->isTransactionActive());
        } finally { $this->store->db->rollBack(); }
    }

    public function testAuditFailureRollsBackEveryOtherRevocation(): void
    {
        $actor = $this->actor('alice', 'user');
        $current = $this->sessions->issue($actor->id, 'user', self::SID);
        $other = $this->sessions->issue($actor->id, 'user', str_repeat('o', 32));
        $sqlite = $this->store->db->getDatabasePlatform() instanceof SQLitePlatform;
        $this->store->db->executeStatement($sqlite
            ? "CREATE TRIGGER reject_other_revocations BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind='candidate.session.others_revoked' BEGIN SELECT RAISE(ABORT,'synthetic private failure'); END"
            : "ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_other_revocations CHECK (kind <> 'candidate.session.others_revoked')");
        try {
            $this->refused(fn () => $this->service('user')->revoke($actor, $current, self::SID), 503);
            self::assertNull($this->row('user', $other)['revoked_at']);
            self::assertNull($this->row('user', $current)['revoked_at']);
        } finally {
            $this->store->db->executeStatement($sqlite ? 'DROP TRIGGER reject_other_revocations'
                : 'ALTER TABLE wcp_candidate_audit DROP CONSTRAINT reject_other_revocations');
        }
    }

    public function testSqliteAndRevocationBetweenValidationAndWriteFailClosed(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true, 'wrapperClass' => OtherSessionsInterleavingConnection::class]);
        self::assertInstanceOf(OtherSessionsInterleavingConnection::class, $db);
        try {
            $store = new WorkbenchStore($db);
            $fixture = WorkbenchFixture::reset($db);
            $sessions = new CandidateSessions($store);
            $snapshot = RecoveryAccountSnapshot::find($db, $fixture['users']['alice'], 'user');
            self::assertNotNull($snapshot);
            $actor = $snapshot['actor'];
            $current = $sessions->issue($actor->id, 'user', self::SID);
            $other = $sessions->issue($actor->id, 'user', str_repeat('o', 32));
            $service = new RevokeOtherSessions($store, $sessions, new CandidateMfa($store, ''), $this->runtime('user'));
            $db->beforeBulk = static function (Connection $db) use ($current): void {
                $db->executeStatement('UPDATE wcp_candidate_user_sessions SET revoked_at=last_seen_at WHERE id=?', [$current]);
            };
            $this->refused(fn () => $service->revoke($actor, $current, self::SID), 403);
            self::assertNull($db->fetchOne('SELECT revoked_at FROM wcp_candidate_user_sessions WHERE id=?', [$other]));
            self::assertSame(1, $service->revoke($actor, $current, self::SID));
            self::assertTrue($sessions->current($actor->id, 'user', $current, self::SID, 1, false));
        } finally { $db->close(); }
    }

    private function service(string $surface): RevokeOtherSessions { return new RevokeOtherSessions($this->store, $this->sessions, $this->mfa, $this->runtime($surface)); }
    private function runtime(string $surface): RuntimeContext { return new RuntimeContext($surface, true, 'test', $surface.'.localhost'); }
    private function actor(string $name, string $surface): Principal
    {
        $snapshot = RecoveryAccountSnapshot::find($this->store->db, $this->fixture['users'][$name], $surface);
        self::assertNotNull($snapshot);
        return $snapshot['actor'];
    }
    private function proof(string $actor, int $revision = 0): array { return ['actor' => $actor, 'revision' => $revision, 'at' => time()]; }
    private function row(string $surface, string $selector): array
    {
        $row = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_'.$surface.'_sessions WHERE id=?', [$selector]);
        self::assertIsArray($row);
        return $row;
    }
    private function refused(callable $action, int $status): void
    {
        try { $action(); self::fail('Revocation accepted without current account authority and audit.'); }
        catch (HttpExceptionInterface $error) {
            self::assertSame($status, $error->getStatusCode());
            self::assertNull($error->getPrevious());
            self::assertStringNotContainsString('synthetic private failure', (string) $error);
        }
    }
}

/** Real SQL schedule at the bulk mutation boundary, using disposable SQLite storage only. */
final class OtherSessionsInterleavingConnection extends Connection
{
    public ?\Closure $beforeBulk = null;

    public function executeStatement(string $sql, array $params = [], array $types = []): int|string
    {
        if ($this->beforeBulk !== null && str_starts_with($sql, 'UPDATE wcp_candidate_user_sessions SET revoked_at = CASE')
            && str_contains($sql, 'AND id <> ?')) {
            $callback = $this->beforeBulk;
            $this->beforeBulk = null;
            $callback($this);
        }
        return parent::executeStatement($sql, $params, $types);
    }
}
