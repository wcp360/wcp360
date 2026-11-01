<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Wcp360\Core\Module\IdentityAccess\Domain\CandidateUsername;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\PrincipalProvider;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\UsernameSchema;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class UsernameIdentityTest extends KernelTestCase
{
    private Connection $db;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->db = self::getContainer()->get(Connection::class);
        $this->fixture = WorkbenchFixture::reset($this->db, usernames: false);
    }

    public function testReadOnlyMigrationPlanAndUpgradePreserveAccountRelationships(): void
    {
        $admin = $this->fixture['users']['admin'];
        $this->db->insert('wcp_candidate_mfa', ['user_id' => $admin, 'sealed_secret' => 'synthetic-encrypted-fixture']);
        $this->db->insert('wcp_candidate_mfa_recovery', ['user_id' => $admin, 'code_hash' => str_repeat('a', 64)]);
        $this->db->insert('wcp_candidate_admin_sessions', [
            'id' => WorkbenchStore::id(), 'user_id' => $admin, 'secret_verifier' => str_repeat('b', 64),
            'surface_claim' => 'WCP-ADMIN', 'auth_strength' => 2, 'created_at' => 100,
            'last_seen_at' => 100, 'expires_at' => 200,
        ]);
        $before = $this->snapshot();
        $plan = UsernameSchema::statements($this->db);
        self::assertSame($plan, UsernameSchema::statements($this->db));
        self::assertArrayNotHasKey('username', $this->db->createSchemaManager()->listTableColumns('wcp_candidate_users'));
        self::assertSame($before, $this->snapshot());
        UsernameSchema::upgrade($this->db);
        self::assertSame($before, $this->snapshot());
        foreach ($this->fixture['users'] as $name => $id) {
            self::assertSame($name, $this->db->fetchOne('SELECT username FROM wcp_candidate_users WHERE id=?', [$id]));
        }
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD, $this->db->fetchOne('SELECT password_hash FROM wcp_candidate_users WHERE id=?', [$admin])));
    }

    public function testCollisionsUseStableUniqueNamesWithoutCrossingContexts(): void
    {
        $this->oldAccount('ALICE@another.test', 'user');
        $invalid = $this->oldAccount('first.last@example.test', 'user');
        $sameNameAdmin = $this->oldAccount('alice@admin.test', 'admin');
        $first = UsernameSchema::statements($this->db);
        self::assertSame($first, UsernameSchema::statements($this->db));
        UsernameSchema::upgrade($this->db);
        $rows = $this->db->fetchAllAssociative('SELECT id,username,context FROM wcp_candidate_users');
        $keys = [];
        foreach ($rows as $row) {
            self::assertTrue(CandidateUsername::valid($row['username']));
            $key = $row['context'].':'.$row['username'];
            self::assertNotContains($key, $keys);
            $keys[] = $key;
        }
        self::assertStringStartsWith('u_', $this->db->fetchOne('SELECT username FROM wcp_candidate_users WHERE id=?', [$invalid]));
        self::assertStringStartsWith('u_', $this->db->fetchOne('SELECT username FROM wcp_candidate_users WHERE id=?', [$this->fixture['users']['alice']]));
        self::assertSame('alice', $this->db->fetchOne('SELECT username FROM wcp_candidate_users WHERE id=?', [$sameNameAdmin]));
    }

    public function testFreshInstallDryRunDoesNotCreateTables(): void
    {
        $fresh = \Doctrine\DBAL\DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        try {
            self::assertNotEmpty(UsernameSchema::statements($fresh));
            self::assertSame([], $fresh->createSchemaManager()->listTableNames());
        } finally {
            $fresh->close();
        }
    }

    public function testLateMigrationFailureRollsBackColumnAndBackfill(): void
    {
        $before = $this->snapshot();
        $this->db->executeStatement('CREATE INDEX wcp_candidate_users_username_context ON wcp_candidate_users(email)');
        try {
            UsernameSchema::upgrade($this->db);
            self::fail('Migration index collision was accepted.');
        } catch (Exception) {
        }
        self::assertArrayNotHasKey('username', $this->db->createSchemaManager()->listTableColumns('wcp_candidate_users'));
        self::assertSame($before, $this->snapshot());
    }

    public function testDatabaseRejectsNonCanonicalNamesAndDuplicateContext(): void
    {
        UsernameSchema::upgrade($this->db);
        foreach ([null, '', 'ab', 'Alice', 'alice@example.test', "alice\n", 'élise', str_repeat('a', 33), 'alice'] as $name) {
            try {
                $this->db->insert('wcp_candidate_users', [
                    'id' => WorkbenchStore::id(), 'username' => $name, 'email' => 'new@example.test',
                    'password_hash' => 'synthetic-unused', 'context' => 'user', 'active' => 1,
                ]);
                self::fail('Invalid or duplicate username accepted by database.');
            } catch (Exception) {
            }
            self::assertSame(3, (int) $this->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_users'));
        }
        try {
            $this->db->update('wcp_candidate_users', ['username' => null], ['id' => $this->fixture['users']['alice']]);
            self::fail('Null username update accepted.');
        } catch (Exception) {
        }
        self::assertSame('alice', $this->db->fetchOne('SELECT username FROM wcp_candidate_users WHERE id=?', [$this->fixture['users']['alice']]));
    }

    public function testSameUsernameIsIsolatedByContextAndContactEmailIsNotAnAlias(): void
    {
        UsernameSchema::upgrade($this->db);
        $this->db->update('wcp_candidate_users', ['username' => 'alice'], ['id' => $this->fixture['users']['admin']]);
        $user = $this->provider('user')->loadUserByIdentifier('alice');
        $admin = $this->provider('admin')->loadUserByIdentifier('alice');
        self::assertSame($this->fixture['users']['alice'], $user->id);
        self::assertSame($this->fixture['users']['admin'], $admin->id);
        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertSame(['ROLE_ADMIN'], $admin->getRoles());
        foreach (['alice@example.test', 'Alice', ' alice', "alice\n", "' OR 1=1 --", 'unknown'] as $invalid) {
            try {
                $this->provider('user')->loadUserByIdentifier($invalid);
                self::fail('Alias or invalid identifier accepted.');
            } catch (UserNotFoundException) {
            }
        }
    }

    public function testRenamedUsernameCannotTransferAnExistingSession(): void
    {
        UsernameSchema::upgrade($this->db);
        $provider = $this->provider('user');
        $principal = $provider->loadUserByIdentifier('alice');
        $this->db->update('wcp_candidate_users', ['username' => 'alice_new'], ['id' => $principal->id]);
        $this->db->update('wcp_candidate_users', ['username' => 'alice'], ['id' => $this->fixture['users']['bob']]);
        $this->expectException(UserNotFoundException::class);
        $provider->refreshUser($principal);
    }

    public function testLegacyEmailSessionIsReadButCannotRefresh(): void
    {
        UsernameSchema::upgrade($this->db);
        $legacy = (new \ReflectionClass(Principal::class))->newInstanceWithoutConstructor();
        $legacy->__unserialize([
            'id' => $this->fixture['users']['alice'], 'email' => 'alice@example.test',
            'passwordHash' => 'legacy-session-fingerprint', 'context' => 'user', 'active' => true,
        ]);
        self::assertSame('', $legacy->username);
        $this->expectException(UserNotFoundException::class);
        $this->provider('user')->refreshUser($legacy);
    }

    private function provider(string $context): PrincipalProvider
    {
        return new PrincipalProvider(new WorkbenchStore($this->db), new RuntimeContext($context, true, 'test', $context.'.localhost'));
    }

    private function oldAccount(string $email, string $context): string
    {
        $id = WorkbenchStore::id();
        $this->db->insert('wcp_candidate_users', ['id' => $id, 'email' => $email, 'password_hash' => 'synthetic-unused', 'context' => $context, 'active' => 1]);
        return $id;
    }

    private function snapshot(): array
    {
        $result = ['users' => $this->db->fetchAllAssociative('SELECT id,email,password_hash,context,active,lock_guard FROM wcp_candidate_users ORDER BY id')];
        foreach (['accounts', 'tenants', 'memberships', 'mfa', 'mfa_recovery', 'admin_sessions'] as $table) {
            $result[$table] = $this->db->fetchAllAssociative('SELECT * FROM wcp_candidate_'.$table);
        }
        return $result;
    }
}
