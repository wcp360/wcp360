<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\Middleware;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use OTPHP\TOTP;
use Psr\Log\AbstractLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Application\ChangeOwnPassword;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\PrincipalProvider;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ChangeOwnPasswordTest extends KernelTestCase
{
    private const NEXT = 'new-synthetic-credential-only-2026';
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
        $this->mfa = self::getContainer()->get(CandidateMfa::class);
    }

    private function runtime(string $surface = 'user'): RuntimeContext
    {
        return new RuntimeContext($surface, true, 'test', $surface.'.localhost');
    }

    private function principal(string $name = 'alice', string $surface = 'user'): Principal
    {
        return (new PrincipalProvider($this->store, $this->runtime($surface)))->loadUserByIdentifier($name);
    }

    private function service(string $surface = 'user', string $mode = 'shared'): ChangeOwnPassword
    {
        return new ChangeOwnPassword($this->store, $this->sessions, $this->mfa,
            self::getContainer()->get(UserPasswordHasherInterface::class), $this->runtime($surface), $mode);
    }

    private function hash(string $name = 'alice'): string
    {
        return $this->store->db->fetchOne('SELECT password_hash FROM wcp_candidate_users WHERE id=?', [$this->fixture['users'][$name]]);
    }

    public function testChangeRevokesOnlyOwnContextAndAuditsNoCredentialMaterial(): void
    {
        $actor = $this->principal();
        $current = $this->sessions->issue($actor->id, 'user', str_repeat('a', 32));
        $this->sessions->issue($actor->id, 'user', str_repeat('b', 32));
        $bob = $this->sessions->issue($this->fixture['users']['bob'], 'user', str_repeat('c', 32));
        $admin = $this->sessions->issue($this->fixture['users']['admin'], 'admin', str_repeat('d', 32));
        $this->service()->change($actor, $current, str_repeat('a', 32), self::NEXT);
        self::assertTrue(password_verify(self::NEXT, $this->hash()));
        self::assertFalse(password_verify(WorkbenchFixture::PASSWORD, $this->hash()));
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD, $this->hash('bob')));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_user_sessions WHERE user_id=? AND revoked_at IS NULL', [$actor->id]));
        self::assertTrue($this->sessions->current($this->fixture['users']['bob'], 'user', $bob, str_repeat('c', 32)));
        self::assertTrue($this->sessions->current($this->fixture['users']['admin'], 'admin', $admin, str_repeat('d', 32)));
        self::assertSame(1, (int) $this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.account.password_changed' AND actor_id=? AND resource_id=?", [$actor->id, $actor->id]));
        $audit = json_encode($this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit'), JSON_THROW_ON_ERROR);
        foreach ([self::NEXT, WorkbenchFixture::PASSWORD, $this->hash(), $actor->getPassword(), str_repeat('a', 32)] as $secret) {
            self::assertStringNotContainsString($secret, $audit);
        }
    }

    public function testChangedCredentialsCannotBeOverwrittenByPreviouslyConfirmedPrincipal(): void
    {
        $actor = $this->principal();
        $current = $this->sessions->issue($actor->id, 'user', str_repeat('a', 32));
        $this->store->db->update('wcp_candidate_users', ['password_hash' => 'externally-rotated-fixture'], ['id' => $actor->id]);
        try {
            $this->service()->change($actor, $current, str_repeat('a', 32), self::NEXT);
            self::fail('A stale password confirmation replaced new credentials.');
        } catch (AccessDeniedHttpException) {
        }
        self::assertSame('externally-rotated-fixture', $this->hash());
        self::assertTrue($this->sessions->current($actor->id, 'user', $current, str_repeat('a', 32)));
    }

    public function testRevokedOrForeignSessionRollsBackTentativePasswordWrite(): void
    {
        $actor = $this->principal();
        $before = $this->hash();
        $own = $this->sessions->issue($actor->id, 'user', str_repeat('a', 32));
        $foreign = $this->sessions->issue($this->fixture['users']['bob'], 'user', str_repeat('b', 32));
        $this->sessions->revoke($actor->id, 'user', $own);
        foreach ([[$own, str_repeat('a', 32)], [$foreign, str_repeat('b', 32)]] as [$selector, $sid]) {
            try {
                $this->service()->change($actor, $selector, $sid, self::NEXT);
                self::fail('Unavailable session changed the password.');
            } catch (AccessDeniedHttpException) {
            }
            self::assertSame($before, $this->hash());
        }
    }

    public function testAuditFailureRollsBackPasswordAndAllRevocations(): void
    {
        $actor = $this->principal();
        $own = $this->sessions->issue($actor->id, 'user', str_repeat('a', 32));
        $second = $this->sessions->issue($actor->id, 'user', str_repeat('b', 32));
        $before = $this->hash();
        $db = $this->store->db;
        if ($db->getDatabasePlatform() instanceof SQLitePlatform) {
            $db->executeStatement("CREATE TRIGGER reject_password_audit BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind='candidate.account.password_changed' BEGIN SELECT RAISE(ABORT,'audit unavailable'); END");
        } else {
            $db->executeStatement("CREATE OR REPLACE FUNCTION wcp_candidate_reject_password_audit() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN IF NEW.kind = ''candidate.account.password_changed'' THEN RAISE EXCEPTION ''audit unavailable''; END IF; RETURN NEW; END;'");
            $db->executeStatement('CREATE TRIGGER reject_password_audit BEFORE INSERT ON wcp_candidate_audit FOR EACH ROW EXECUTE FUNCTION wcp_candidate_reject_password_audit()');
        }
        try {
            $this->service()->change($actor, $own, str_repeat('a', 32), self::NEXT);
            self::fail('Missing mandatory audit was accepted.');
        } catch (\PDOException|\Doctrine\DBAL\Exception) {
        }
        self::assertSame($before, $this->hash());
        self::assertTrue($this->sessions->current($actor->id, 'user', $own, str_repeat('a', 32)));
        self::assertTrue($this->sessions->current($actor->id, 'user', $second, str_repeat('b', 32)));
        self::assertSame(0, (int) $db->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind IN ('candidate.session.revoked','candidate.account.password_changed')"));
    }

    public function testEnabledMfaAndBackupCodesSurvivePasswordChange(): void
    {
        $actor = $this->principal('admin', 'admin');
        $enrollment = $this->mfa->startEnrollment($actor->id);
        $result = $this->mfa->check($actor->id, TOTP::createFromSecret($enrollment['secret'])->now(), 1);
        self::assertNotNull($result);
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id=?', [$actor->id]);
        $codes = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery WHERE user_id=? ORDER BY code_hash', [$actor->id]);
        $selector = $this->sessions->issue($actor->id, 'admin', str_repeat('a', 32));
        $this->sessions->upgrade($actor->id, 'admin', $selector, CandidateSessions::binding($actor->id, 'admin', str_repeat('a', 32)), str_repeat('b', 32));
        $proof = ['actor' => $actor->id, 'revision' => $result['revision'], 'at' => time()];
        $this->service('admin')->change($actor, $selector, str_repeat('b', 32), self::NEXT, $proof);
        self::assertTrue(password_verify(self::NEXT, $this->hash('admin')));
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id=?', [$actor->id]));
        self::assertSame($codes, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery WHERE user_id=? ORDER BY code_hash', [$actor->id]));
        self::assertTrue($this->mfa->status($actor->id)['enabled']);
        self::assertFalse($this->sessions->current($actor->id, 'admin', $selector, str_repeat('b', 32), 2));
    }

    public function testAdministratorRequiresRecentBoundProofAndCorrectContext(): void
    {
        $actor = $this->principal('admin', 'admin');
        $before = $this->hash('admin');
        $selector = $this->sessions->issue($actor->id, 'admin', str_repeat('a', 32));
        foreach ([null, ['actor' => $actor->id, 'revision' => 0, 'at' => time()-300],
            ['actor' => $this->fixture['users']['alice'], 'revision' => 0, 'at' => time()],
            ['actor' => $actor->id, 'revision' => 1, 'at' => time()],
            ['actor' => $actor->id, 'revision' => 0, 'at' => time()+3600]] as $proof) {
            try {
                $this->service('admin')->change($actor, $selector, str_repeat('a', 32), self::NEXT, $proof);
                self::fail('Invalid administrator proof accepted.');
            } catch (AccessDeniedHttpException) {
            }
            self::assertSame($before, $this->hash('admin'));
        }
        $this->expectException(AccessDeniedHttpException::class);
        $this->service('user')->change($actor, $selector, str_repeat('a', 32), self::NEXT);
    }

    public function testIsolatedDatabaseModeRetainsExistingCredentialWriteRestrictions(): void
    {
        self::assertFalse($this->service(mode: 'isolated')->available());
        $actor = $this->principal();
        $before = $this->hash();
        try {
            $this->service(mode: 'isolated')->change($actor, 'unused', str_repeat('a', 32), self::NEXT);
            self::fail('Isolated database credentials were broadened.');
        } catch (ServiceUnavailableHttpException) {
        }
        self::assertSame($before, $this->hash());
    }

    public function testPasswordVerifiersNeverEnterDoctrineParameterProfiling(): void
    {
        $logger = new class extends AbstractLogger {
            public array $records = [];
            public function log($level, string|\Stringable $message, array $context = []): void
            {
                $this->records[] = [(string) $message, $context];
            }
        };
        $config = new Configuration();
        $config->setMiddlewares([new Middleware($logger)]);
        $db = DriverManager::getConnection($this->store->db->getParams(), $config);
        try {
            $this->store = new WorkbenchStore($db);
            $this->fixture = WorkbenchFixture::reset($db);
            $this->sessions = new CandidateSessions($this->store);
            $actor = $this->principal();
            $selector = $this->sessions->issue($actor->id, 'user', str_repeat('a', 32));
            $logger->records = [];
            $this->service()->change($actor, $selector, str_repeat('a', 32), self::NEXT);
            $newHash = $this->hash();
            self::assertTrue(password_verify(self::NEXT, $newHash));
            $log = serialize($logger->records);
            foreach ([$newHash, $actor->getPassword(), self::NEXT, str_repeat('a', 32)] as $secret) {
                self::assertStringNotContainsString($secret, $log);
            }
        } finally {
            $db->close();
        }
    }
}
