<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\CandidateDatabaseMiddleware;
use Wcp360\Core\Shared\Security\CandidateDatabaseUrl;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

/** Destructive role fixtures are restricted to the explicitly configured disposable test database. */
final class CandidatePostgresGrantsTest extends KernelTestCase
{
    private Connection $db;
    private array $roles = [];
    private array $connections = [];
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->db = self::getContainer()->get(Connection::class);
        if (!$this->db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('Actual PostgreSQL role/grant enforcement is exercised by Linux CI.');
        }
        if (!getenv('WCP_TEST_DATABASE_URL') || $this->db->fetchOne('SELECT current_database()') !== 'wcp360_test') {
            throw new \RuntimeException('Role fixtures require the explicit disposable wcp360_test PostgreSQL database.');
        }
        self::assertTrue((bool) $this->db->fetchOne("SELECT rolsuper FROM pg_catalog.pg_roles WHERE rolname = current_user"), 'Disposable role tests require the dedicated CI PostgreSQL administrator.');
        $this->fixture = WorkbenchFixture::reset($this->db);
        foreach (['user', 'admin', 'worker'] as $context) {
            $name = 'wcp_test_'.bin2hex(random_bytes(8));
            $password = bin2hex(random_bytes(32));
            $this->db->executeStatement('CREATE ROLE '.$this->db->quoteIdentifier($name).' LOGIN NOINHERIT NOSUPERUSER NOCREATEDB NOCREATEROLE NOREPLICATION NOBYPASSRLS PASSWORD '.$this->db->quote($password));
            $this->roles[$context] = ['name' => $name, 'password' => $password];
            $role = $this->db->quoteIdentifier($name);
            $this->db->executeStatement('GRANT USAGE ON SCHEMA public TO '.$role);
            if ($context !== 'worker') {
                $this->db->executeStatement('GRANT SELECT, UPDATE ON public.wcp_candidate_users TO '.$role);
                $this->db->executeStatement('GRANT INSERT ON public.wcp_candidate_audit TO '.$role);
                $this->db->executeStatement('GRANT SELECT, INSERT, UPDATE, DELETE ON public.wcp_candidate_'.$context.'_sessions TO '.$role);
            }
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->connections as $connection) {
            $connection->close();
        }
        foreach (array_reverse($this->roles) as $role) {
            $quoted = $this->db->quoteIdentifier($role['name']);
            $this->db->executeStatement('DROP OWNED BY '.$quoted);
            $this->db->executeStatement('DROP ROLE '.$quoted);
        }
        parent::tearDown();
    }

    public function testSeparateLoginRolesSupportRealSessionLifecycleAndDenyOppositeCrud(): void
    {
        foreach (['user' => 'alice', 'admin' => 'admin'] as $context => $account) {
            $db = $this->connection($context);
            $sessions = new CandidateSessions(new WorkbenchStore($db));
            $actor = $this->fixture['users'][$account];
            $sid = str_repeat('s', 32);
            $selector = $sessions->issue($actor, $context, $sid);
            self::assertTrue($sessions->current($actor, $context, $selector, $sid));
            self::assertCount(1, $sessions->listing($actor, $context, $selector)['items']);
            self::assertTrue($sessions->revoke($actor, $context, $selector));
            self::assertFalse($sessions->current($actor, $context, $selector, $sid));
            self::assertSame(1, $db->delete('wcp_candidate_'.$context.'_sessions', ['id' => $selector]));
            $blocked = $context === 'user' ? 'admin' : 'user';
            $this->assertCrudDenied($db, $blocked);
            self::assertSame('pg_catalog, public, pg_temp', $db->fetchOne('SHOW search_path'));
        }
    }

    public function testTemporaryObjectsCannotShadowPublicIdentityOrSessionTables(): void
    {
        $db = $this->connection('user');
        $db->executeStatement('CREATE TEMP TABLE wcp_candidate_users (id UUID)');
        $db->executeStatement('CREATE TEMP TABLE wcp_candidate_admin_sessions (id UUID)');
        self::assertSame(0, (int) $db->fetchOne('SELECT count(*) FROM pg_temp.wcp_candidate_users'));
        self::assertSame(3, (int) $db->fetchOne('SELECT count(*) FROM wcp_candidate_users'));
        self::assertSame($this->fixture['users']['alice'], $db->fetchOne("SELECT id FROM wcp_candidate_users WHERE email = 'alice@example.test'"));
        $this->assertCrudDenied($db, 'admin', false);
    }

    public function testWorkerHasNoSessionAuthorityAndMaintenanceIsNotAWebCredential(): void
    {
        $worker = $this->connection('worker');
        self::assertSame(1, (int) $worker->fetchOne('SELECT 1'));
        $this->assertCrudDenied($worker, 'user');
        $this->assertCrudDenied($worker, 'admin');
        foreach (['user', 'admin', 'worker'] as $context) {
            $configuration = new Configuration();
            $configuration->setMiddlewares([new CandidateDatabaseMiddleware('session-isolated', $context)]);
            $db = DriverManager::getConnection($this->db->getParams(), $configuration);
            $this->connections[] = $db;
            $this->assertRejected($db);
        }
        $configuration = new Configuration();
        $configuration->setMiddlewares([new CandidateDatabaseMiddleware('session-isolated', 'setup')]);
        $setup = DriverManager::getConnection($this->db->getParams(), $configuration);
        $this->connections[] = $setup;
        self::assertSame(0, (int) $setup->fetchOne('SELECT count(*) FROM public.wcp_candidate_admin_sessions'));
        self::assertSame(0, (int) $setup->fetchOne('SELECT count(*) FROM public.wcp_candidate_user_sessions'));
    }

    public function testCrossSurfaceTableOrColumnGrantRejectsNewConnections(): void
    {
        $role = $this->db->quoteIdentifier($this->roles['user']['name']);
        foreach (['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'TRUNCATE', 'REFERENCES', 'TRIGGER', 'MAINTAIN', 'SELECT (id)', 'UPDATE (id)', 'INSERT (id)', 'REFERENCES (id)'] as $privilege) {
            $this->db->executeStatement('GRANT '.$privilege.' ON public.wcp_candidate_admin_sessions TO '.$role);
            $this->assertRejected($this->connection('user'));
            $this->db->executeStatement('REVOKE '.$privilege.' ON public.wcp_candidate_admin_sessions FROM '.$role);
        }
        self::assertSame(1, (int) $this->connection('user')->fetchOne('SELECT 1'));
    }

    public function testMissingOwnCrudSchemaCreationAndElevatedRoleAreRejected(): void
    {
        $role = $this->db->quoteIdentifier($this->roles['user']['name']);
        foreach (['SELECT', 'INSERT', 'UPDATE', 'DELETE'] as $privilege) {
            $this->db->executeStatement('REVOKE '.$privilege.' ON public.wcp_candidate_user_sessions FROM '.$role);
            $this->assertRejected($this->connection('user'));
            $this->db->executeStatement('GRANT '.$privilege.' ON public.wcp_candidate_user_sessions TO '.$role);
        }
        $this->db->executeStatement('GRANT CREATE ON SCHEMA public TO '.$role);
        $this->assertRejected($this->connection('user'));
        $this->db->executeStatement('REVOKE CREATE ON SCHEMA public FROM '.$role);
        foreach (['CREATEDB' => 'NOCREATEDB', 'CREATEROLE' => 'NOCREATEROLE', 'BYPASSRLS' => 'NOBYPASSRLS'] as $grant => $revoke) {
            $this->db->executeStatement('ALTER ROLE '.$role.' '.$grant);
            $this->assertRejected($this->connection('user'));
            $this->db->executeStatement('ALTER ROLE '.$role.' '.$revoke);
        }
        self::assertSame(1, (int) $this->connection('user')->fetchOne('SELECT 1'));
    }

    public function testNoInheritMembershipStillCannotProvideSetRoleEscape(): void
    {
        $worker = $this->db->quoteIdentifier($this->roles['worker']['name']);
        $user = $this->db->quoteIdentifier($this->roles['user']['name']);
        self::assertSame(1, (int) $this->connection('worker')->fetchOne('SELECT 1'));
        $this->db->executeStatement('GRANT '.$user.' TO '.$worker);
        $this->assertRejected($this->connection('worker'));
        $this->db->executeStatement('REVOKE '.$user.' FROM '.$worker);
        self::assertSame(1, (int) $this->connection('worker')->fetchOne('SELECT 1'));
    }

    public function testSymfonyWiresTheGateOnTheActualSelectedPostgresConnection(): void
    {
        $params = $this->db->getParams();
        $url = 'postgresql://'.rawurlencode($this->roles['user']['name']).':'.rawurlencode($this->roles['user']['password'])
            .'@'.$params['host'].':'.($params['port'] ?? 5432).'/'.rawurlencode($params['dbname']).'?serverVersion=17&charset=utf8';
        $saved = [];
        self::ensureKernelShutdown();
        try {
            foreach (['WCP_DATABASE_MODE' => 'session-isolated', 'WCP_DATABASE_USER_URL' => $url] as $name => $value) {
                $saved[$name] = [$_SERVER[$name] ?? null, $_ENV[$name] ?? null];
                $_SERVER[$name] = $_ENV[$name] = $value;
            }
            self::bootKernel();
            $selected = self::getContainer()->get(Connection::class);
            self::assertSame($this->roles['user']['name'], $selected->fetchOne('SELECT current_user'));
            self::assertSame(0, (int) $selected->fetchOne('SELECT count(*) FROM public.wcp_candidate_user_sessions'));
            $selected->close();
            $this->db->executeStatement('GRANT SELECT ON public.wcp_candidate_admin_sessions TO '.$this->db->quoteIdentifier($this->roles['user']['name']));
            $this->assertRejected($selected);
        } finally {
            self::ensureKernelShutdown();
            foreach ($saved as $name => [$server, $env]) {
                if ($server === null) {
                    unset($_SERVER[$name]);
                } else {
                    $_SERVER[$name] = $server;
                }
                if ($env === null) {
                    unset($_ENV[$name]);
                } else {
                    $_ENV[$name] = $env;
                }
            }
        }
    }

    public function testMissingSessionSchemaFailsClosedWithoutImplicitMigration(): void
    {
        $this->db->executeStatement('DROP TABLE public.wcp_candidate_admin_sessions');
        $this->assertRejected($this->connection('user'));
        self::assertNull($this->db->fetchOne("SELECT to_regclass('public.wcp_candidate_admin_sessions')"));
    }

    private function connection(string $context): Connection
    {
        $configuration = new Configuration();
        $configuration->setMiddlewares([new CandidateDatabaseMiddleware('session-isolated', $context)]);
        $params = $this->db->getParams();
        $params['user'] = $this->roles[$context]['name'];
        $params['password'] = $this->roles[$context]['password'];
        $connection = DriverManager::getConnection($params, $configuration);
        $this->connections[] = $connection;
        return $connection;
    }

    private function assertRejected(Connection $connection): void
    {
        try {
            $connection->fetchOne('SELECT 1');
            self::fail('A misconfigured runtime database credential was admitted.');
        } catch (\RuntimeException $error) {
            self::assertSame(CandidateDatabaseUrl::REJECTED, $error->getMessage());
            self::assertNull($error->getPrevious());
        }
    }

    private function assertCrudDenied(Connection $db, string $surface, bool $qualified = true): void
    {
        $table = ($qualified ? 'public.' : '').'wcp_candidate_'.$surface.'_sessions';
        foreach ([
            'SELECT id FROM '.$table,
            'INSERT INTO '.$table.' DEFAULT VALUES',
            'UPDATE '.$table.' SET auth_strength = auth_strength WHERE false',
            'DELETE FROM '.$table.' WHERE false',
        ] as $sql) {
            try {
                $db->executeStatement($sql);
                self::fail('PostgreSQL accepted cross-surface session access.');
            } catch (DriverException $error) {
                self::assertSame('42501', $error->getSQLState());
            }
        }
    }
}
