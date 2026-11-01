<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use OTPHP\TOTP;
use Psr\Log\AbstractLogger;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Platform\Application\ApplicationServerSettings;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateDatabasePolicy;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateDatabaseRoles;
use Wcp360\Core\Shared\Security\CandidateDatabaseMiddleware;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class CandidateDatabaseRolesTest extends WebTestCase
{
    private Connection $maintenance;
    private CandidateDatabaseRoles $provider;
    private array $roles = [];
    private array $passwords = [];
    private array $connections = [];
    private array $saved = [];
    private array $fixture;
    private bool $cleanup = false;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->maintenance = self::getContainer()->get(Connection::class);
        if (!$this->maintenance->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('Actual PostgreSQL candidate role provisioning runs in Linux CI.');
        }
        if (!getenv('WCP_TEST_DATABASE_URL') || $this->maintenance->fetchOne('SELECT current_database()') !== 'wcp360_test') {
            throw new \RuntimeException('Role fixtures require the explicit disposable PostgreSQL test database.');
        }
        self::assertTrue((bool) $this->maintenance->fetchOne('SELECT rolsuper FROM pg_catalog.pg_roles WHERE rolname=current_user'));
        $this->roles = CandidateDatabasePolicy::roles('wcp360_test');
        self::assertSame(0, $this->roleCount(), 'Existing roles must never be removed by this fixture.');
        $this->cleanup = true;
        $this->fixture = WorkbenchFixture::reset($this->maintenance);
        $this->provider = $this->provider($this->maintenance);
        foreach (CandidateDatabasePolicy::CONTEXTS as $context) {
            $this->passwords[$context] = bin2hex(random_bytes(32));
        }
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        foreach ($this->connections as $connection) {
            $connection->close();
        }
        if ($this->cleanup) {
            foreach ($this->roles as $role) {
                if ($this->maintenance->fetchOne('SELECT 1 FROM pg_catalog.pg_roles WHERE rolname=?', [$role])) {
                    $quoted = $this->maintenance->quoteIdentifier($role);
                    $this->maintenance->executeStatement('DROP OWNED BY '.$quoted);
                    $this->maintenance->executeStatement('DROP ROLE '.$quoted);
                }
            }
        }
        foreach ($this->saved as $name => [$server, $env]) {
            if ($server === null) { unset($_SERVER[$name]); } else { $_SERVER[$name] = $server; }
            if ($env === null) { unset($_ENV[$name]); } else { $_ENV[$name] = $env; }
        }
        parent::tearDown();
    }

    public function testPlanReadsOnlyAndNeverNeedsOrReturnsPasswords(): void
    {
        $plan = $this->provider->plan();
        self::assertSame($this->roles, $plan['roles']);
        self::assertFalse($plan['applied']);
        self::assertFalse($plan['production_qualified']);
        self::assertSame(0, $this->roleCount());
        self::assertSame(0, (int) $this->maintenance->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
        $this->assertNoSecrets($plan);
    }

    public function testProvisioningCreatesScramLoginsWithoutApplicationSqlProfilingSecrets(): void
    {
        $logger = new class extends AbstractLogger {
            public array $records = [];
            public function log($level, string|\Stringable $message, array $context = []): void
            {
                $this->records[] = [(string) $message, $context];
            }
        };
        $configuration = new Configuration();
        $configuration->setMiddlewares([new \Doctrine\DBAL\Logging\Middleware($logger)]);
        $db = DriverManager::getConnection($this->maintenance->getParams(), $configuration);
        $this->connections[] = $db;
        $result = $this->provider($db)->provision($this->passwords);
        self::assertTrue($result['applied']);
        self::assertSame(3, $this->roleCount());
        $this->assertNoSecrets($result);
        $recorded = json_encode($logger->records, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString('SCRAM-SHA-256$', $recorded);
        foreach ($this->passwords as $password) {
            self::assertStringNotContainsString($password, $recorded);
        }
        foreach ($this->roles as $context => $role) {
            $connection = $this->login($context);
            self::assertSame($role, $connection->fetchOne('SELECT current_user'));
            self::assertSame(1, (int) $this->maintenance->fetchOne("SELECT count(*) FROM pg_catalog.pg_authid WHERE rolname=? AND rolpassword LIKE 'SCRAM-SHA-256$4096:%' AND rolcanlogin AND NOT (rolsuper OR rolcreatedb OR rolcreaterole OR rolreplication OR rolbypassrls)", [$role]));
        }
        self::assertSame('candidate.database.roles.created', $this->maintenance->fetchOne('SELECT kind FROM wcp_candidate_audit'));
        $this->assertRefused(fn () => $this->login('user', str_repeat('0',64))->fetchOne('SELECT 1'));
    }

    public function testExistingRoleIsNeverAdoptedAlteredOrRepaired(): void
    {
        $role = $this->maintenance->quoteIdentifier($this->roles['admin']);
        $this->maintenance->executeStatement('CREATE ROLE '.$role.' NOLOGIN');
        $oid = $this->maintenance->fetchOne('SELECT oid FROM pg_catalog.pg_roles WHERE rolname=?', [$this->roles['admin']]);
        $this->assertRefused(fn () => $this->provider->provision($this->passwords));
        self::assertSame(1, $this->roleCount());
        self::assertSame($oid, $this->maintenance->fetchOne('SELECT oid FROM pg_catalog.pg_roles WHERE rolname=?', [$this->roles['admin']]));
        self::assertFalse((bool) $this->maintenance->fetchOne('SELECT rolcanlogin FROM pg_catalog.pg_roles WHERE rolname=?', [$this->roles['admin']]));
    }

    public function testAuditFailureRollsBackEveryRoleAndGrant(): void
    {
        $this->maintenance->executeStatement("ALTER TABLE public.wcp_candidate_audit ADD CONSTRAINT reject_role_creation CHECK (kind <> 'candidate.database.roles.created')");
        $this->assertRefused(fn () => $this->provider->provision($this->passwords));
        self::assertSame(0, $this->roleCount());
        self::assertSame(0, (int) $this->maintenance->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
    }

    public function testPublicColumnGrantCannotSilentlyBroadenThePolicy(): void
    {
        $this->maintenance->executeStatement('GRANT SELECT (sealed_secret) ON public.wcp_candidate_mfa TO PUBLIC');
        try {
            $this->assertRefused(fn () => $this->provider->provision($this->passwords));
            self::assertSame(0, $this->roleCount());
        } finally {
            $this->maintenance->executeStatement('REVOKE SELECT (sealed_secret) ON public.wcp_candidate_mfa FROM PUBLIC');
        }
    }

    public function testUnknownTableAndMissingTableAreRefusedBeforeRoleCreation(): void
    {
        $this->maintenance->executeStatement('CREATE TABLE public.wcp_candidate_unreviewed (id INTEGER)');
        try {
            $this->assertRefused(fn () => $this->provider->provision($this->passwords));
            self::assertSame(0, $this->roleCount());
        } finally {
            $this->maintenance->executeStatement('DROP TABLE public.wcp_candidate_unreviewed');
        }
        $this->maintenance->executeStatement('DROP TABLE public.wcp_candidate_admin_sessions');
        $this->assertRefused(fn () => $this->provider->provision($this->passwords));
        self::assertSame(0, $this->roleCount());
    }

    public function testCanonicalSchemaAndOtherDatabaseAreOutsideTheApplyScope(): void
    {
        self::assertNull($this->maintenance->fetchOne("SELECT to_regnamespace('wcp_iam')"));
        $this->maintenance->executeStatement('CREATE SCHEMA wcp_iam');
        try {
            $this->assertRefused(fn () => $this->provider->provision($this->passwords));
        } finally {
            $this->maintenance->executeStatement('DROP SCHEMA wcp_iam');
        }
        $params = $this->maintenance->getParams();
        $params['dbname'] = 'postgres';
        $other = DriverManager::getConnection($params);
        $this->connections[] = $other;
        $this->assertRefused(fn () => $this->provider($other)->provision($this->passwords));
        self::assertSame(0, $this->roleCount());
    }

    public function testCallerTransactionAndConcurrentProvisionerRemainUntouched(): void
    {
        $this->maintenance->beginTransaction();
        try {
            $this->assertRefused(fn () => $this->provider->provision($this->passwords));
            self::assertTrue($this->maintenance->isTransactionActive());
            self::assertSame(0, $this->roleCount());
        } finally {
            $this->maintenance->rollBack();
        }
        $lock = DriverManager::getConnection($this->maintenance->getParams());
        $this->connections[] = $lock;
        $lock->beginTransaction();
        try {
            $lock->fetchOne('SELECT pg_catalog.pg_advisory_xact_lock(pg_catalog.hashtext(current_database()), 3601701)');
            $this->assertRefused(fn () => $this->provider->provision($this->passwords));
            self::assertTrue($lock->isTransactionActive());
            self::assertSame(0, $this->roleCount());
        } finally {
            $lock->rollBack();
        }
    }

    public function testMissingConstantZeroConstraintIsRefusedAndGrantedMarkerCannotChange(): void
    {
        $this->maintenance->executeStatement('ALTER TABLE public.wcp_candidate_users DROP CONSTRAINT wcp_candidate_users_lock_guard_zero');
        $this->assertRefused(fn () => $this->provider->provision($this->passwords));
        self::assertSame(0, $this->roleCount());
        $this->maintenance->executeStatement('ALTER TABLE public.wcp_candidate_users ADD CONSTRAINT wcp_candidate_users_lock_guard_zero CHECK (lock_guard = 0)');
        $this->provider->provision($this->passwords);
        foreach (['user', 'admin'] as $context) {
            $db = $this->login($context);
            self::assertSame(0, (int) $db->fetchOne('SELECT lock_guard FROM public.wcp_candidate_users LIMIT 1'));
            try {
                $db->executeStatement('UPDATE public.wcp_candidate_users SET lock_guard = 1');
                self::fail('The row-lock marker was mutable.');
            } catch (DriverException $error) {
                self::assertSame('23514', $error->getSQLState());
            }
            $db->beginTransaction();
            try {
                self::assertIsString($db->fetchOne('SELECT id FROM public.wcp_candidate_users LIMIT 1 FOR UPDATE'));
            } finally {
                $db->rollBack();
            }
        }
    }

    public function testDatabaseGrantsEnforceSessionMfaIdentityPolicyAndAppendOnlyAuditBoundaries(): void
    {
        $this->provider->provision($this->passwords);
        foreach (CandidateDatabasePolicy::CONTEXTS as $context) {
            $db = $this->login($context);
            $denied = [
                'UPDATE public.wcp_candidate_users SET id=id WHERE false',
                'UPDATE public.wcp_candidate_users SET password_hash=password_hash WHERE false',
                'UPDATE public.wcp_candidate_users SET active=active WHERE false',
                'INSERT INTO public.wcp_candidate_users DEFAULT VALUES',
                'SELECT id FROM public.wcp_candidate_audit',
                'UPDATE public.wcp_candidate_audit SET kind=kind WHERE false',
                'DELETE FROM public.wcp_candidate_audit WHERE false',
            ];
            foreach (['user', 'admin'] as $surface) {
                if ($context !== $surface) {
                    $denied[] = 'SELECT id FROM public.wcp_candidate_'.$surface.'_sessions';
                    $denied[] = 'INSERT INTO public.wcp_candidate_'.$surface.'_sessions DEFAULT VALUES';
                    $denied[] = 'UPDATE public.wcp_candidate_'.$surface.'_sessions SET auth_strength=auth_strength WHERE false';
                    $denied[] = 'DELETE FROM public.wcp_candidate_'.$surface.'_sessions WHERE false';
                }
            }
            if ($context !== 'admin') {
                $denied[] = 'SELECT sealed_secret FROM public.wcp_candidate_mfa';
                $denied[] = 'INSERT INTO public.wcp_candidate_permission_denials DEFAULT VALUES';
                $denied[] = 'UPDATE public.wcp_candidate_runtime_settings SET revision=revision WHERE false';
            }
            if ($context !== 'worker') {
                $denied[] = 'UPDATE public.wcp_candidate_jobs SET state=state WHERE false';
            }
            foreach ($denied as $sql) {
                try {
                    $db->executeStatement($sql);
                    self::fail('Forbidden database capability was accepted.');
                } catch (DriverException $error) {
                    self::assertSame('42501', $error->getSQLState());
                }
            }
        }
    }

    public function testCommandAndHttpUserMfaAdminAndWorkerJourneysUseProvisionedCredentials(): void
    {
        $this->context('setup');
        $this->set('WCP_ALLOW_CANDIDATE_DATABASE_ROLES', '1');
        foreach ($this->passwords as $context => $password) {
            $this->set('WCP_CANDIDATE_DATABASE_'.strtoupper($context).'_PASSWORD', $password);
        }
        $kernel = self::bootKernel();
        $command = new CommandTester((new Application($kernel))->find('wcp:candidate:database:roles'));
        self::assertSame(0, $command->execute(['--apply' => true], ['interactive' => false]));
        $result = json_decode(trim($command->getDisplay()), true, 16, JSON_THROW_ON_ERROR);
        self::assertSame('created', $result['status']);
        $this->assertNoSecrets($result);
        foreach (CandidateDatabasePolicy::CONTEXTS as $context) {
            $this->set('WCP_CANDIDATE_DATABASE_'.strtoupper($context).'_PASSWORD', null);
        }

        $this->set('WCP_ALLOW_CANDIDATE_DATABASE_ROLES', '0');
        $browser = $this->browser('user');
        $crawler = $browser->request('GET', 'http://user.localhost/login');
        $browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'alice@example.test', '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
        $crawler = $browser->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSame($this->roles['user'], self::getContainer()->get(Connection::class)->fetchOne('SELECT current_user'));
        $browser->submit($crawler->filter('form[action="/workbench/plans"]')->form(['hostname' => 'isolated-profile.test']));
        self::assertResponseRedirects();
        $browser->followRedirect();
        self::assertResponseIsSuccessful();
        $job = $this->maintenance->fetchOne('SELECT id FROM public.wcp_candidate_jobs');
        $browser->request('GET', 'http://user.localhost/account/sessions');
        self::assertResponseIsSuccessful();

        $this->context('worker');
        self::bootKernel();
        self::assertSame($this->roles['worker'], self::getContainer()->get(Connection::class)->fetchOne('SELECT current_user'));
        self::assertSame($job, self::getContainer()->get(RunHostingPlan::class)->runOne());
        self::assertSame('SUCCEEDED', $this->maintenance->fetchOne('SELECT state FROM public.wcp_candidate_jobs WHERE id=?', [$job]));

        $this->context('user');
        self::bootKernel();
        $second = self::getContainer()->get(SubmitHostingPlan::class)->submit($this->fixture['users']['alice'], [
            'tenant_id' => $this->fixture['tenants']['alice'], 'hostname' => 'revoked-profile.test', 'idempotency_key' => 'profile-revocation-0001',
        ]);
        $browser = $this->browser('admin');
        $crawler = $browser->request('GET', 'http://admin.localhost/login');
        $browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'admin@example.test', '_password' => WorkbenchFixture::PASSWORD]));
        for ($i=0; $i<3 && $browser->getResponse()->isRedirect(); ++$i) { $crawler=$browser->followRedirect(); }
        self::assertResponseIsSuccessful();
        $crawler=$browser->submit($crawler->selectButton('Configurer la MFA')->form());
        $secret=trim($crawler->filter('#mfa-secret')->text());
        $crawler=$browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code'=>TOTP::createFromSecret($secret)->now()]));
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, '#recovery-codes code');
        $backup=trim($crawler->filter('#recovery-codes code')->first()->text());
        $browser->request('GET', 'http://admin.localhost/');
        self::assertResponseIsSuccessful();
        self::assertSame($this->roles['admin'], self::getContainer()->get(Connection::class)->fetchOne('SELECT current_user'));
        $session=$browser->getRequest()->getSession();
        $proof=$session->get('_wcp_mfa_proof');
        self::assertIsArray($proof);
        $proof['at']=time()-301;
        $session->set('_wcp_mfa_proof', $proof);
        $session->save();
        $crawler=$browser->request('GET', 'http://admin.localhost/account/reauthenticate');
        self::assertResponseIsSuccessful();
        $browser->submit($crawler->selectButton('Confirmer mon mot de passe')->form(['_password'=>WorkbenchFixture::PASSWORD]));
        for ($i=0; $i<3 && $browser->getResponse()->isRedirect(); ++$i) { $crawler=$browser->followRedirect(); }
        self::assertSame('/mfa', $browser->getRequest()->getPathInfo());
        self::assertResponseIsSuccessful();
        $browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code'=>$backup]));
        self::assertResponseRedirects('/');
        $browser->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSame($this->roles['admin'], self::getContainer()->get(Connection::class)->fetchOne('SELECT current_user'));
        self::assertSame(1, (int) $this->maintenance->fetchOne('SELECT count(*) FROM wcp_candidate_admin_sessions WHERE revoked_at IS NULL'));
        $browser->request('GET', 'http://admin.localhost/account/sessions');
        self::assertResponseIsSuccessful();
        self::getContainer()->get(ApplicationServerSettings::class)->change($this->fixture['users']['admin'], 'php_fpm', 1);
        self::getContainer()->get(CandidatePermissionAdministration::class)->setDenied($this->fixture['users']['admin'], $this->fixture['users']['alice'], $this->fixture['tenants']['alice'], true);
        $browser->request('GET', 'http://admin.localhost/workbench/access');
        self::assertResponseIsSuccessful();

        $this->context('worker');
        self::bootKernel();
        self::assertSame($second['job_id'], self::getContainer()->get(RunHostingPlan::class)->runOne());
        self::assertSame('FAILED', $this->maintenance->fetchOne('SELECT state FROM public.wcp_candidate_jobs WHERE id=?', [$second['job_id']]));
        self::assertSame(1, (int) $this->maintenance->fetchOne('SELECT count(*) FROM wcp_candidate_user_sessions'));
        self::assertSame(2, (int) $this->maintenance->fetchOne('SELECT count(*) FROM wcp_candidate_admin_sessions'));
    }

    private function provider(Connection $db): CandidateDatabaseRoles
    {
        return new CandidateDatabaseRoles($db, new RuntimeContext('setup', true, 'test', 'localhost'), true);
    }

    private function roleCount(): int
    {
        return (int) $this->maintenance->fetchOne('SELECT count(*) FROM pg_catalog.pg_roles WHERE rolname IN (?,?,?)', array_values($this->roles));
    }

    private function login(string $context, ?string $password = null): Connection
    {
        $configuration = new Configuration();
        $configuration->setMiddlewares([new CandidateDatabaseMiddleware('session-isolated', $context)]);
        $params = $this->maintenance->getParams();
        $params['user'] = $this->roles[$context];
        $params['password'] = $password ?? $this->passwords[$context];
        $connection = DriverManager::getConnection($params, $configuration);
        $this->connections[] = $connection;
        return $connection;
    }

    private function assertRefused(\Closure $operation): void
    {
        try {
            $operation();
            self::fail('Unsafe or conflicting provisioning was accepted.');
        } catch (\RuntimeException $error) {
            // Driver authentication refusal uses the existing connection gate's fixed message.
            self::assertContains($error->getMessage(), [CandidateDatabaseRoles::REJECTED, \Wcp360\Core\Shared\Security\CandidateDatabaseUrl::REJECTED]);
            self::assertNull($error->getPrevious());
            foreach ($this->passwords as $password) { self::assertStringNotContainsString($password, (string) $error); }
        }
    }

    private function assertNoSecrets(array $value): void
    {
        $output = json_encode($value, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString('SCRAM-SHA-256$', $output);
        foreach ($this->passwords as $password) { self::assertStringNotContainsString($password, $output); }
    }

    private function browser(string $context): KernelBrowser
    {
        $this->context($context);
        $browser = self::createClient([], ['HTTP_HOST'=>$context.'.localhost', 'HTTP_ORIGIN'=>'http://'.$context.'.localhost']);
        $browser->disableReboot();
        return $browser;
    }

    private function context(string $context): void
    {
        self::ensureKernelShutdown();
        $this->set('WCP_CONTEXT', $context);
        $this->set('WCP_PUBLIC_HOST', in_array($context, ['user','admin'], true) ? $context.'.localhost' : 'localhost');
        $this->set('WCP_DATABASE_MODE', $context === 'setup' ? 'shared' : 'session-isolated');
        foreach (CandidateDatabasePolicy::CONTEXTS as $surface) {
            $this->set('WCP_DATABASE_'.strtoupper($surface).'_URL', null);
        }
        if ($context !== 'setup') {
            $params = $this->maintenance->getParams();
            $url = 'postgresql://'.$this->roles[$context].':'.$this->passwords[$context].'@'.$params['host'].':'.($params['port']??5432).'/wcp360_test?serverVersion=17&charset=utf8';
            $this->set('WCP_DATABASE_'.strtoupper($context).'_URL', $url);
        }
    }

    private function set(string $name, ?string $value): void
    {
        if (!array_key_exists($name, $this->saved)) { $this->saved[$name]=[$_SERVER[$name]??null,$_ENV[$name]??null]; }
        if ($value === null) { unset($_SERVER[$name],$_ENV[$name]); } else { $_SERVER[$name]=$_ENV[$name]=$value; }
    }
}
