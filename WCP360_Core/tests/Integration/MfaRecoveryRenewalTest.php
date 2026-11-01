<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Application\RegenerateMfaRecoveryCodes;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\RecoveryAccountSnapshot;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class MfaRecoveryRenewalTest extends KernelTestCase
{
    private const KEY = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
    private const SID = 'synthetic-bound-session-id-2026';
    private WorkbenchStore $store;
    private CandidateMfa $mfa;
    private CandidateSessions $sessions;
    private RegenerateMfaRecoveryCodes $renewal;
    private Principal $actor;
    private string $selector;
    private array $proof;
    private array $oldCodes;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $id = $this->fixture['users']['admin'];
        $this->mfa = new CandidateMfa($this->store, self::KEY);
        $enrollment = $this->mfa->startEnrollment($id);
        $result = $this->mfa->check($id, TOTP::createFromSecret($enrollment['secret'])->now(), 1);
        self::assertNotNull($result);
        $this->oldCodes = $result['recovery_codes'];
        $snapshot = RecoveryAccountSnapshot::find($this->store->db, $id, 'admin');
        self::assertNotNull($snapshot);
        $this->actor = $snapshot['actor'];
        $this->sessions = new CandidateSessions($this->store);
        $oldId = str_repeat('o', 32);
        $this->selector = $this->sessions->issue($id, 'admin', $oldId);
        $this->sessions->upgrade($id, 'admin', $this->selector, CandidateSessions::binding($id, 'admin', $oldId), self::SID);
        $this->proof = ['actor' => $id, 'revision' => $result['revision'], 'at' => time()];
        $this->renewal = new RegenerateMfaRecoveryCodes($this->store, $this->mfa, $this->sessions,
            new RuntimeContext('admin', true, 'test', 'admin.localhost'));
    }

    public function testReplacementPreservesAuthenticatorAndReplayBudgetAndRevokesOnlyOwnSessions(): void
    {
        $id = $this->actor->id;
        $before = $this->factor();
        $alice = $this->fixture['users']['alice'];
        $other = $this->sessions->issue($alice, 'user', str_repeat('u', 32));
        $codes = $this->renew();
        self::assertCount(10, array_unique($codes));
        self::assertSame([], array_intersect($this->oldCodes, $codes));
        $after = $this->factor();
        foreach (['sealed_secret', 'last_counter', 'failed_window_started', 'failed_attempts', 'enrollment_attempts'] as $field) {
            self::assertSame($before[$field], $after[$field]);
        }
        self::assertSame((int) $before['revision'] + 1, (int) $after['revision']);
        self::assertTrue($this->mfa->status($id)['enabled']);
        self::assertFalse($this->sessions->current($id, 'admin', $this->selector, self::SID, 2, false));
        self::assertTrue($this->sessions->current($alice, 'user', $other, str_repeat('u', 32), 1, false));
        $persisted = serialize([$after, $this->storedCodes(), $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit')]);
        foreach ($codes as $code) {
            self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/D', $code);
            self::assertStringNotContainsString($code, $persisted);
        }
        self::assertNull($this->mfa->check($id, $this->oldCodes[0], (int) $after['revision']));
        self::assertSame('recovery', $this->mfa->check($id, $codes[0], (int) $after['revision'])['method']);
        self::assertNull($this->mfa->check($id, $codes[0]));
    }

    public function testStaleFutureForeignAndWrongRevisionProofOrBindingCannotRotateCodes(): void
    {
        $before = $this->storedCodes();
        foreach ([null, array_replace($this->proof, ['at' => time() - 301]),
            array_replace($this->proof, ['at' => time() + 60]),
            array_replace($this->proof, ['actor' => $this->fixture['users']['alice']]),
            array_replace($this->proof, ['revision' => 999])] as $proof) {
            $this->refused(fn () => $this->renewal->regenerate($this->actor, $this->selector, self::SID, $proof), 403);
        }
        $this->refused(fn () => $this->renewal->regenerate($this->actor, $this->selector, str_repeat('x', 32), $this->proof), 403);
        self::assertSame($before, $this->storedCodes());
        self::assertTrue($this->sessions->current($this->actor->id, 'admin', $this->selector, self::SID, 2, false));
    }

    public function testPasswordChangeAfterConfirmationCannotRenewUsingStalePrincipal(): void
    {
        $before = $this->storedCodes();
        $this->store->db->update('wcp_candidate_users', ['password_hash' => 'concurrent-synthetic-verifier'], ['id' => $this->actor->id]);
        $this->refused(fn () => $this->renew(), 403);
        self::assertSame($before, $this->storedCodes());
        self::assertSame('concurrent-synthetic-verifier', $this->store->db->fetchOne('SELECT password_hash FROM wcp_candidate_users WHERE id=?', [$this->actor->id]));
    }

    public function testCallerTransactionIsRefusedWithoutAdoptingItsWrites(): void
    {
        $before = $this->storedCodes();
        $this->store->db->beginTransaction();
        try {
            $this->refused(fn () => $this->renew(), 503);
            self::assertTrue($this->store->db->isTransactionActive());
            self::assertSame($before, $this->storedCodes());
        } finally {
            $this->store->db->rollBack();
        }
    }

    public function testMandatoryAuditFailureRollsBackCodesRevisionAndSessionRevocation(): void
    {
        $before = [$this->factor(), $this->storedCodes()];
        $sqlite = $this->store->db->getDatabasePlatform() instanceof SQLitePlatform;
        if ($sqlite) {
            $this->store->db->executeStatement("CREATE TRIGGER reject_renewal_audit BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind = 'candidate.mfa.recovery.regenerated' BEGIN SELECT RAISE(ABORT,'synthetic private failure'); END");
        } else {
            $this->store->db->executeStatement("ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_renewal_audit CHECK (kind <> 'candidate.mfa.recovery.regenerated')");
        }
        try {
            $this->refused(fn () => $this->renew(), 503);
            self::assertSame($before, [$this->factor(), $this->storedCodes()]);
            self::assertTrue($this->sessions->current($this->actor->id, 'admin', $this->selector, self::SID, 2, false));
        } finally {
            $this->store->db->executeStatement($sqlite ? 'DROP TRIGGER reject_renewal_audit' : 'ALTER TABLE wcp_candidate_audit DROP CONSTRAINT reject_renewal_audit');
        }
        self::assertCount(10, $this->renew());
    }

    public function testChangedEncryptionKeyCannotReplaceCodesOrInvalidateExistingSessions(): void
    {
        $before = [$this->factor(), $this->storedCodes()];
        $wrong = new RegenerateMfaRecoveryCodes($this->store, new CandidateMfa($this->store, str_repeat('b', 64)),
            $this->sessions, new RuntimeContext('admin', true, 'test', 'admin.localhost'));
        $this->refused(fn () => $wrong->regenerate($this->actor, $this->selector, self::SID, $this->proof), 503);
        self::assertSame($before, [$this->factor(), $this->storedCodes()]);
    }

    private function renew(): array { return $this->renewal->regenerate($this->actor, $this->selector, self::SID, $this->proof); }
    private function factor(): array { return $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id=?', [$this->actor->id]); }
    private function storedCodes(): array { return $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery WHERE user_id=? ORDER BY code_hash', [$this->actor->id]); }
    private function refused(callable $action, int $status): void
    {
        try {
            $action();
            self::fail('Recovery codes were renewed without current authority and durable audit.');
        } catch (HttpExceptionInterface $error) {
            self::assertSame($status, $error->getStatusCode());
            self::assertNull($error->getPrevious());
            foreach ($this->oldCodes as $code) self::assertStringNotContainsString($code, (string) $error);
            self::assertStringNotContainsString('synthetic private failure', (string) $error);
        }
    }
}
