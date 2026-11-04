<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Application\CompletePasswordRecovery;
use Wcp360\Core\Module\IdentityAccess\Domain\PasswordRecoveryCode;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\RecoveryAccountSnapshot;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Setup\Application\IssuePasswordRecoveryCode;
use Wcp360\Core\Module\Setup\Application\RecoverAccountPassword;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateAccountMaintenance;
use Wcp360\Core\Module\Setup\UI\Console\IssuePasswordRecoveryCodeCommand;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class PasswordRecoveryCodeTest extends KernelTestCase
{
    private const KEY = 'cccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccc';
    private const NEXT = 'synthetic-bearer-recovery-password-2026';
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function runtime(string $surface): RuntimeContext { return new RuntimeContext($surface,true,'test','localhost'); }
    private function issuer(string $context='setup',bool $enabled=true,string $mode='shared',string $key=self::KEY): IssuePasswordRecoveryCode
    {
        $runtime=$this->runtime($context);
        return new IssuePasswordRecoveryCode($this->store,new CandidateAccountMaintenance($this->store,$runtime),$runtime,$mode,new \Wcp360\Core\Tests\Fixtures\RecoveryKeysFixture($key),$enabled);
    }
    private function consumer(string $surface='user',bool $enabled=true,string $mode='shared',string $key=self::KEY,?UserPasswordHasherInterface $hasher=null): CompletePasswordRecovery
    {
        return new CompletePasswordRecovery($this->store,new CandidateSessions($this->store),
            $hasher??self::getContainer()->get(UserPasswordHasherInterface::class),$this->runtime($surface),$mode,new \Wcp360\Core\Tests\Fixtures\RecoveryKeysFixture($key),$enabled);
    }
    private function code(string $name='alice',string $surface='user'): string
    {
        return $this->issuer()->issue($this->fixture['users'][$name],$name,$surface);
    }
    private function hash(string $name='alice'): string
    {
        return (string)$this->store->db->fetchOne('SELECT password_hash FROM wcp_candidate_users WHERE id=?',[$this->fixture['users'][$name]]);
    }
    private function refused(callable $call): void
    {
        try { $call(); self::fail('Recovery was unexpectedly accepted.'); }
        catch (\RuntimeException $error) { self::assertNull($error->getPrevious()); }
    }

    public function testRedemptionConsumesAllOldCodesAndRevokesOnlyOwnSessions(): void
    {
        $sessions=new CandidateSessions($this->store);
        $id=$this->fixture['users']['alice'];
        $selector=$sessions->issue($id,'user',str_repeat('a',32));
        $bob=$sessions->issue($this->fixture['users']['bob'],'user',str_repeat('b',32));
        $before=$this->hash();
        $one=$this->code();$two=$this->code();
        self::assertSame($before,$this->hash(),'Issuance must not change credentials.');
        $this->consumer()->complete($one,self::NEXT);
        self::assertTrue(password_verify(self::NEXT,$this->hash()));
        self::assertFalse($sessions->current($id,'user',$selector,str_repeat('a',32)));
        self::assertTrue($sessions->current($this->fixture['users']['bob'],'user',$bob,str_repeat('b',32)));
        $this->refused(fn()=>$this->consumer()->complete($one,'another-synthetic-password'));
        $this->refused(fn()=>$this->consumer()->complete($two,'another-synthetic-password'));
        self::assertSame(1,(int)$this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.account.password_reset'"));
        $audit=serialize($this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit'));
        foreach([$one,$two,self::NEXT,$before,$this->hash()] as $secret)self::assertStringNotContainsString($secret,$audit);
    }

    public function testExpiryWrongSurfaceAndChangedIdentityOrFactorStateRefuseWithoutReset(): void
    {
        $id=$this->fixture['users']['alice'];
        $snapshot=RecoveryAccountSnapshot::find($this->store->db,$id,'user');
        self::assertNotNull($snapshot);
        $expired=PasswordRecoveryCode::issue($snapshot['actor'],0,self::KEY,time()-900);
        $before=$this->hash();$code=$this->code();
        $this->refused(fn()=>$this->consumer()->complete($expired,self::NEXT));
        $this->refused(fn()=>$this->consumer('admin')->complete($code,self::NEXT));
        $this->store->db->update('wcp_candidate_users',['email'=>'changed@example.test'],['id'=>$id]);
        $this->refused(fn()=>$this->consumer()->complete($code,self::NEXT));
        $this->store->db->update('wcp_candidate_users',['active'=>0],['id'=>$id]);
        $this->refused(fn()=>$this->consumer()->complete($code,self::NEXT));
        self::assertSame($before,$this->hash());
        self::assertSame(0,(int)$this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.account.password_reset'"));
    }

    public function testAdministratorMfaAndBackupCodesRemainRequiredAndUnchanged(): void
    {
        $id=$this->fixture['users']['admin'];
        $old=$this->code('admin','admin');
        $mfa=self::getContainer()->get(CandidateMfa::class);
        $enrollment=$mfa->startEnrollment($id);
        self::assertNotNull($mfa->check($id,TOTP::createFromSecret($enrollment['secret'])->now(),1));
        $this->refused(fn()=>$this->consumer('admin')->complete($old,self::NEXT));
        $code=$this->code('admin','admin');
        $factor=$this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id=?',[$id]);
        $codes=$this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery WHERE user_id=? ORDER BY code_hash',[$id]);
        $this->consumer('admin')->complete($code,self::NEXT);
        self::assertTrue(password_verify(self::NEXT,$this->hash('admin')));
        self::assertTrue($mfa->status($id)['enabled']);
        self::assertSame($factor,$this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id=?',[$id]));
        self::assertSame($codes,$this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery WHERE user_id=? ORDER BY code_hash',[$id]));
    }

    public function testAuditFailureRollsBackAndDoesNotConsumeTheCode(): void
    {
        $code=$this->code();$before=$this->hash();
        $id=$this->fixture['users']['alice'];
        $sessions=new CandidateSessions($this->store);
        $selector=$sessions->issue($id,'user',str_repeat('a',32));
        $sqlite=$this->store->db->getDatabasePlatform() instanceof SQLitePlatform;
        $this->store->db->executeStatement($sqlite
            ? "CREATE TRIGGER reject_code_reset BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind='candidate.account.password_reset' BEGIN SELECT RAISE(ABORT,'Recovery audit fixture'); END"
            : "ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_code_reset CHECK (kind <> 'candidate.account.password_reset')");
        $this->refused(fn()=>$this->consumer()->complete($code,self::NEXT));
        self::assertSame($before,$this->hash());
        self::assertTrue($sessions->current($id,'user',$selector,str_repeat('a',32)));
        $this->store->db->executeStatement($sqlite?'DROP TRIGGER reject_code_reset':'ALTER TABLE wcp_candidate_audit DROP CONSTRAINT reject_code_reset');
        $this->consumer()->complete($code,self::NEXT);
        self::assertTrue(password_verify(self::NEXT,$this->hash()));
    }

    public function testConcurrentCredentialChangeSurvivesRejectedRedemption(): void
    {
        $code=$this->code();
        $hasher=$this->createStub(UserPasswordHasherInterface::class);
        $hasher->method('isPasswordValid')->willReturn(false);
        $hasher->method('hashPassword')->willReturnCallback(function():string{
            $this->store->db->update('wcp_candidate_users',['password_hash'=>'concurrent-code-verifier'],['id'=>$this->fixture['users']['alice']]);
            return 'replacement-code-verifier';
        });
        $this->refused(fn()=>$this->consumer(hasher:$hasher)->complete($code,self::NEXT));
        self::assertSame('concurrent-code-verifier',$this->hash());
    }

    public function testDefaultOffMissingKeyWrongContextAndIsolatedProfileStayUnavailable(): void
    {
        foreach([$this->consumer(enabled:false),$this->consumer(key:''),$this->consumer('worker'),$this->consumer(mode:'session-isolated')] as $consumer) {
            self::assertFalse($consumer->available());
            $this->refused(fn()=>$consumer->complete('invalid',self::NEXT));
        }
        foreach([$this->issuer('user'),$this->issuer(enabled:false),$this->issuer(key:''),$this->issuer(mode:'session-isolated')] as $issuer) {
            self::assertFalse($issuer->enabled('user'));
            $this->refused(fn()=>$issuer->issue($this->fixture['users']['alice'],'alice','user'));
        }
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD,$this->hash()));
    }

    public function testCallerTransactionsAreNotAdopted(): void
    {
        $code=$this->code();
        $this->store->db->beginTransaction();
        try {
            $this->refused(fn()=>$this->consumer()->complete($code,self::NEXT));
            $this->refused(fn()=>$this->code());
            self::assertTrue($this->store->db->isTransactionActive());
        } finally {$this->store->db->rollBack();}
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD,$this->hash()));
    }

    public function testCodesAndVerifiersStayOutOfDoctrineProfiles(): void
    {
        $logger=new class extends \Psr\Log\AbstractLogger {
            public array $records=[];
            public function log($level,string|\Stringable $message,array $context=[]):void{$this->records[]=[(string)$message,$context];}
        };
        $configuration=new \Doctrine\DBAL\Configuration();
        $configuration->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware($logger)]);
        $db=\Doctrine\DBAL\DriverManager::getConnection($this->store->db->getParams(),$configuration);
        try {
            $this->store=new WorkbenchStore($db);
            $this->fixture=WorkbenchFixture::reset($db);
            $before=$this->hash();
            $logger->records=[];
            $code=$this->code();
            $this->consumer()->complete($code,self::NEXT);
            $log=serialize($logger->records);
            foreach([$code,self::NEXT,$before,$this->hash()] as $secret)self::assertStringNotContainsString($secret,$log);
        } finally {$db->close();}
    }

    public function testIssuanceCommandPlansCancelsAndRequiresInteractiveOptIn(): void
    {
        $runtime=$this->runtime('setup');
        $accounts=new RecoverAccountPassword($this->store,new CandidateSessions($this->store),
            self::getContainer()->get(UserPasswordHasherInterface::class),$runtime);
        $command=new CommandTester(new IssuePasswordRecoveryCodeCommand($accounts,$this->issuer(),$runtime));
        self::assertSame(0,$command->execute(['username'=>'alice','--surface'=>'user'],['interactive'=>false]));
        self::assertStringNotContainsString('wcp-cr1.',$command->getDisplay());
        self::assertSame(2,$command->execute(['username'=>'alice','--surface'=>'user','--issue'=>true],['interactive'=>false]));
        $command->setInputs(['no']);
        self::assertSame(0,$command->execute(['username'=>'alice','--surface'=>'user','--issue'=>true],['interactive'=>true]));
        self::assertSame(0,(int)$this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.account.recovery_code_issued'"));
        $command->setInputs(['yes']);
        self::assertSame(0,$command->execute(['username'=>'alice','--surface'=>'user','--issue'=>true],['interactive'=>true]));
        self::assertSame(1,preg_match_all('/wcp-cr1\.[a-z0-9.-]+/',$command->getDisplay(),$matches));
        $code=$matches[0][0];
        self::assertSame($this->fixture['users']['alice'],PasswordRecoveryCode::subject($code,'user',time()));
        $this->consumer()->complete($code,self::NEXT);
        self::assertTrue(password_verify(self::NEXT,$this->hash()));
    }
}
