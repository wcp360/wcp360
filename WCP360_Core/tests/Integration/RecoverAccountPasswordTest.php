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
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Setup\Application\RecoverAccountPassword;
use Wcp360\Core\Module\Setup\UI\Console\RecoverAccountPasswordCommand;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class RecoverAccountPasswordTest extends KernelTestCase
{
    private const NEXT = 'operator-recovery-synthetic-password-2026';
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function runtime(string $context = 'setup'): RuntimeContext
    {
        return new RuntimeContext($context, true, 'test', 'localhost');
    }

    private function recovery(bool $enabled = true, string $context = 'setup', ?UserPasswordHasherInterface $hasher = null): RecoverAccountPassword
    {
        return new RecoverAccountPassword($this->store, new CandidateSessions($this->store),
            $hasher ?? self::getContainer()->get(UserPasswordHasherInterface::class), $this->runtime($context), $enabled);
    }

    private function hash(string $name = 'alice'): string
    {
        return (string) $this->store->db->fetchOne('SELECT password_hash FROM wcp_candidate_users WHERE id=?', [$this->fixture['users'][$name]]);
    }

    private function refused(callable $call): void
    {
        try { $call(); self::fail('Refused maintenance operation completed.'); }
        catch (\RuntimeException $error) {
            self::assertSame(RecoverAccountPassword::REFUSED, $error->getMessage());
            self::assertNull($error->getPrevious());
        }
    }

    public function testPlanIsReadOnlyAndNeverReturnsCredentialData(): void
    {
        $before = $this->hash();
        $plan = $this->recovery(false)->plan('alice', 'user');
        self::assertSame($this->fixture['users']['alice'], $plan['id']);
        self::assertFalse($plan['apply_enabled']);
        self::assertSame($before, $this->hash());
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
        $email = $this->store->db->fetchOne('SELECT email FROM wcp_candidate_users WHERE id=?', [$plan['id']]);
        foreach ([$before, WorkbenchFixture::PASSWORD, $email] as $secret) {
            self::assertStringNotContainsString($secret, json_encode($plan, JSON_THROW_ON_ERROR));
        }
    }

    public function testWrongContextDisabledApplyAndInvalidTargetsAreRefused(): void
    {
        foreach (['user', 'admin', 'worker'] as $context) {
            self::assertFalse($this->recovery(true, $context)->enabled());
            $this->refused(fn() => $this->recovery(true, $context)->plan('alice', 'user'));
            $this->refused(fn() => $this->recovery(true, $context)->recover($this->fixture['users']['alice'], 'alice', 'user', self::NEXT));
        }
        $this->refused(fn() => $this->recovery(false)->recover($this->fixture['users']['alice'], 'alice', 'user', self::NEXT));
        foreach ([['alice@example.test','user'], ['Alice','user'], ['alice','worker'], ['missing','user']] as [$name,$surface]) {
            $this->refused(fn() => $this->recovery()->plan($name, $surface));
        }
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD, $this->hash()));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
    }

    public function testRecoveryRevokesOnlyTheSelectedAccountAndAuditsMaintenance(): void
    {
        $sessions = new CandidateSessions($this->store);
        $alice = $this->fixture['users']['alice'];
        $own = $sessions->issue($alice, 'user', str_repeat('a', 32));
        $sessions->issue($alice, 'user', str_repeat('b', 32));
        $bob = $sessions->issue($this->fixture['users']['bob'], 'user', str_repeat('c', 32));
        $admin = $sessions->issue($this->fixture['users']['admin'], 'admin', str_repeat('d', 32));
        $before = $this->hash();
        $result = $this->recovery()->recover($alice, 'alice', 'user', self::NEXT);
        self::assertSame(2, $result['revoked_sessions']);
        self::assertTrue(password_verify(self::NEXT, $this->hash()));
        self::assertFalse(password_verify(WorkbenchFixture::PASSWORD, $this->hash()));
        self::assertFalse($sessions->current($alice, 'user', $own, str_repeat('a', 32)));
        self::assertTrue($sessions->current($this->fixture['users']['bob'], 'user', $bob, str_repeat('c', 32)));
        self::assertTrue($sessions->current($this->fixture['users']['admin'], 'admin', $admin, str_repeat('d', 32)));
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD, $this->hash('bob')));
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD, $this->hash('admin')));
        $audit = $this->store->db->fetchAssociative("SELECT actor_id,resource_id FROM wcp_candidate_audit WHERE kind='candidate.account.password_recovered'");
        self::assertSame(['actor_id' => null, 'resource_id' => $alice], $audit);
        $export = serialize($this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit')).serialize($result);
        foreach ([$before, $this->hash(), self::NEXT, str_repeat('a', 32)] as $secret) self::assertStringNotContainsString($secret, $export);
    }

    public function testRecoveryPreservesEnabledMfaAndEveryBackupCode(): void
    {
        $admin = $this->fixture['users']['admin'];
        $mfa = self::getContainer()->get(CandidateMfa::class);
        $enrollment = $mfa->startEnrollment($admin);
        self::assertNotNull($mfa->check($admin, TOTP::createFromSecret($enrollment['secret'])->now(), 1));
        $before = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id=?', [$admin]);
        $codes = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery WHERE user_id=? ORDER BY code_hash', [$admin]);
        $this->recovery()->recover($admin, 'admin', 'admin', self::NEXT);
        self::assertTrue(password_verify(self::NEXT, $this->hash('admin')));
        self::assertSame($before, $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id=?', [$admin]));
        self::assertSame($codes, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery WHERE user_id=? ORDER BY code_hash', [$admin]));
        self::assertTrue($mfa->status($admin)['enabled']);
    }

    public function testReuseInvalidPasswordAndDeactivatedAccountCannotChangeCredentials(): void
    {
        $id = $this->fixture['users']['alice'];
        $before = $this->hash();
        $this->refused(fn() => $this->recovery()->recover($id, 'alice', 'user', WorkbenchFixture::PASSWORD));
        try { $this->recovery()->recover($id, 'alice', 'user', 'short'); self::fail('Weak password accepted.'); }
        catch (\InvalidArgumentException) {}
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $id]);
        $this->refused(fn() => $this->recovery()->plan('alice', 'user'));
        $this->refused(fn() => $this->recovery()->recover($id, 'alice', 'user', self::NEXT));
        self::assertSame($before, $this->hash());
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT active FROM wcp_candidate_users WHERE id=?', [$id]));
    }

    public function testReusedUsernameCannotRedirectAnInspectedAccount(): void
    {
        $plan = $this->recovery()->plan('alice', 'user');
        $before = [$this->hash(), $this->hash('bob')];
        $this->store->db->update('wcp_candidate_users', ['username' => 'alice_old'], ['id' => $plan['id']]);
        $this->store->db->update('wcp_candidate_users', ['username' => 'alice'], ['id' => $this->fixture['users']['bob']]);
        $this->refused(fn() => $this->recovery()->recover($plan['id'], 'alice', 'user', self::NEXT));
        self::assertSame($before, [$this->hash(), $this->hash('bob')]);
    }

    public function testAuditFailureRollsBackPasswordAndSessionRevocation(): void
    {
        $sessions = new CandidateSessions($this->store);
        $id = $this->fixture['users']['alice'];
        $selector = $sessions->issue($id, 'user', str_repeat('a', 32));
        $before = $this->hash();
        if ($this->store->db->getDatabasePlatform() instanceof SQLitePlatform) {
            $this->store->db->executeStatement("CREATE TRIGGER reject_recovery BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind = 'candidate.account.password_recovered' BEGIN SELECT RAISE(ABORT,'Recovery audit fixture'); END");
        } else {
            $this->store->db->executeStatement("ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_recovery CHECK (kind <> 'candidate.account.password_recovered')");
        }
        $this->refused(fn() => $this->recovery()->recover($id, 'alice', 'user', self::NEXT));
        self::assertSame($before, $this->hash());
        self::assertTrue($sessions->current($id, 'user', $selector, str_repeat('a', 32)));
        self::assertSame(0, (int) $this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.session.revoked'"));
    }

    public function testCallerTransactionRemainsOwnedByItsCaller(): void
    {
        $before = $this->hash();
        $this->store->db->beginTransaction();
        try {
            $this->refused(fn() => $this->recovery()->recover($this->fixture['users']['alice'], 'alice', 'user', self::NEXT));
            self::assertTrue($this->store->db->isTransactionActive());
            self::assertSame($before, $this->hash());
        } finally { $this->store->db->rollBack(); }
    }

    public function testConcurrentCredentialChangeIsNotOverwritten(): void
    {
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->method('isPasswordValid')->willReturn(false);
        $hasher->method('hashPassword')->willReturnCallback(function (): string {
            $this->store->db->update('wcp_candidate_users', ['password_hash' => 'concurrent-verifier-fixture'], ['id' => $this->fixture['users']['alice']]);
            return 'replacement-verifier-fixture';
        });
        $this->refused(fn() => $this->recovery(hasher: $hasher)->recover($this->fixture['users']['alice'], 'alice', 'user', self::NEXT));
        self::assertSame('concurrent-verifier-fixture', $this->hash());
        self::assertSame(0, (int) $this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.account.password_recovered'"));
    }

    public function testNativeCredentialOperationsStayOutOfDoctrineProfiles(): void
    {
        $logger = new class extends AbstractLogger {
            public array $records = [];
            public function log($level, string|\Stringable $message, array $context = []): void { $this->records[] = [(string) $message, $context]; }
        };
        $configuration = new Configuration();
        $configuration->setMiddlewares([new Middleware($logger)]);
        $db = DriverManager::getConnection($this->store->db->getParams(), $configuration);
        try {
            $this->store = new WorkbenchStore($db);
            $this->fixture = WorkbenchFixture::reset($db);
            $before = $this->hash();
            $logger->records = [];
            $this->recovery()->recover($this->fixture['users']['alice'], 'alice', 'user', self::NEXT);
            $log = serialize($logger->records);
            foreach ([$before, $this->hash(), self::NEXT] as $secret) self::assertStringNotContainsString($secret, $log);
        } finally { $db->close(); }
    }

    private function command(bool $enabled = true, string $context = 'setup'): CommandTester
    {
        return new CommandTester(new RecoverAccountPasswordCommand($this->recovery($enabled, $context), $this->runtime($context)));
    }

    public function testCommandPlanWorksWithoutInteractiveInputAndApplyRequiresOptIn(): void
    {
        $command = $this->command(false);
        self::assertSame(0, $command->execute(['username' => 'alice', '--surface' => 'user'], ['interactive' => false]));
        self::assertStringContainsString('"status":"planned"', $command->getDisplay());
        self::assertSame(2, $command->execute(['username' => 'alice', '--surface' => 'user', '--apply' => true], ['interactive' => true]));
        self::assertSame(2, $this->command()->execute(['username' => 'alice', '--surface' => 'user', '--apply' => true], ['interactive' => false]));
        foreach (['user','admin','worker'] as $context) {
            self::assertSame(2, $this->command(true, $context)->execute(['username' => 'alice', '--surface' => 'user'], ['interactive' => false]));
        }
        self::assertSame(2, $this->command()->execute(['username' => 'alice'], ['interactive' => false]));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
    }

    public function testDecliningConfirmationHasNoEffect(): void
    {
        $command = $this->command();
        $command->setInputs(['no']);
        self::assertSame(0, $command->execute(['username' => 'alice', '--surface' => 'user', '--apply' => true], ['interactive' => true]));
        self::assertStringContainsString('"status":"cancelled"', $command->getDisplay());
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD, $this->hash()));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
    }

    public function testHiddenConfirmationMismatchNeverChangesTheAccount(): void
    {
        if (PHP_OS_FAMILY === 'Windows') self::markTestSkipped('Hidden console input requires the Linux CI runner; Windows uses a separate native helper.');
        $command = $this->command();
        $command->setInputs(['yes', self::NEXT, 'different-synthetic-password']);
        self::assertSame(1, $command->execute(['username' => 'alice', '--surface' => 'user', '--apply' => true], ['interactive' => true]));
        self::assertStringContainsString('"status":"refused"', $command->getDisplay());
        self::assertStringNotContainsString(self::NEXT, $command->getDisplay());
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD, $this->hash()));
    }

    public function testInteractiveRecoveryPreservesPassphraseSpacesWithoutPrintingSecrets(): void
    {
        if (PHP_OS_FAMILY === 'Windows') self::markTestSkipped('Hidden console input requires the Linux CI runner; Windows uses a separate native helper.');
        $password = '  synthetic passphrase with spaces  ';
        $command = $this->command();
        $command->setInputs(['yes', $password, $password]);
        self::assertSame(0, $command->execute(['username' => 'alice', '--surface' => 'user', '--apply' => true], ['interactive' => true]));
        self::assertStringContainsString('"status":"recovered"', $command->getDisplay());
        self::assertStringNotContainsString($password, $command->getDisplay());
        self::assertTrue(password_verify($password, $this->hash()));
        self::assertFalse(password_verify(trim($password), $this->hash()));
    }

    public function testOrdinaryContainerKeepsRecoveryDisabled(): void
    {
        self::assertFalse(self::getContainer()->get(RecoverAccountPassword::class)->enabled());
    }
}
