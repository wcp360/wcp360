<?php

declare(strict_types=1);
namespace Wcp360\Core\Tests\Integration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Wcp360\Core\Module\Setup\UI\Console\SetupCommand;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;
final class EnrollmentTest extends KernelTestCase
{
    private WorkbenchStore $store;
    protected function setUp():void
    {
        self::bootKernel();
        $this->store=self::getContainer()->get(WorkbenchStore::class);
        WorkbenchFixture::reset($this->store->db);
    }
    private function command():CommandTester
    {
        return new CommandTester(new SetupCommand($this->store,self::getContainer()->get(UserPasswordHasherInterface::class),new RuntimeContext('setup',true,'test','localhost')));
    }
    public function testUnattendedEnrollmentIsRefused():void
    {
        $tester=$this->command();$exit=$tester->execute([],['interactive'=>false]);
        self::assertSame(1,$exit);
        self::assertSame(3,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_users'));
    }
    public function testDuplicateUsernameCannotPartiallyEnrollAnotherAccount(): void
    {
        if (PHP_OS_FAMILY === 'Windows') self::markTestSkipped('Hidden terminal input is exercised in Linux CI.');
        $tester = $this->command();
        $tester->setInputs(['alice', 'new-contact@example.test', '0', 'wizard-test-only-strong-password']);
        try {
            $tester->execute([], ['interactive' => true]);
            self::fail('Duplicate username enrolled.');
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException) {
        }
        self::assertSame(3, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_users'));
        self::assertSame(2, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_tenants'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
    }

    public function testInteractiveWizardHashesPasswordAndCreatesSeparateAccountTenant():void
    {
        if(PHP_OS_FAMILY==='Windows')$this->markTestSkipped('The Windows hidden-input executable reads the console directly; this interaction is exercised in Linux CI.');
        $tester=$this->command();$password='wizard-test-only-strong-password';
        $tester->setInputs(['wizard','wizard@example.test','0',$password]);
        self::assertSame(0,$tester->execute([],['interactive'=>true]));
        $row=$this->store->db->fetchAssociative("SELECT * FROM wcp_candidate_users WHERE email='wizard@example.test'");
        self::assertSame('wizard',$row['username']);self::assertSame('user',$row['context']);self::assertTrue(password_verify($password,$row['password_hash']));
        self::assertStringNotContainsString($password,$tester->getDisplay());
        $tenant=$this->store->db->fetchAssociative('SELECT t.id,t.account_id FROM wcp_candidate_tenants t JOIN wcp_candidate_memberships m ON m.tenant_id=t.id WHERE m.user_id=?',[$row['id']]);
        self::assertNotSame($tenant['id'],$tenant['account_id']);self::assertNotSame($row['id'],$tenant['id']);
        self::assertSame(1,(int)$this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind='candidate.account.enrolled'"));
    }
}
