<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateDatabasePolicy;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateDatabaseRoles;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Doctrine\DBAL\DriverManager;

final class CandidateDatabasePolicyTest extends TestCase
{
    public function testPolicyKeepsSessionsMfaAuthorityAuditAndIdentityWritesSeparate(): void
    {
        self::assertCount(16, CandidateDatabasePolicy::TABLES);
        foreach (CandidateDatabasePolicy::CONTEXTS as $context) {
            $grants = CandidateDatabasePolicy::grants($context);
            self::assertSame(['INSERT'], $grants['audit']);
            self::assertArrayNotHasKey('accounts', $grants);
            foreach ($grants as $table => $privileges) {
                self::assertContains($table, CandidateDatabasePolicy::TABLES);
                self::assertNotEmpty($privileges);
                foreach ($privileges as $privilege) {
                    self::assertMatchesRegularExpression('/\A(?:SELECT|INSERT|UPDATE|DELETE|UPDATE\(lock_guard\))\z/D', $privilege);
                }
            }
            foreach (['user', 'admin'] as $surface) {
                self::assertSame($context === $surface, isset($grants[$surface.'_sessions']));
            }
        }
        self::assertSame(['SELECT', 'UPDATE(lock_guard)'], CandidateDatabasePolicy::grants('user')['users']);
        self::assertSame(['SELECT'], CandidateDatabasePolicy::grants('worker')['users']);
        self::assertArrayNotHasKey('mfa', CandidateDatabasePolicy::grants('user'));
        self::assertArrayNotHasKey('mfa_recovery', CandidateDatabasePolicy::grants('worker'));
        self::assertNotContains('INSERT', CandidateDatabasePolicy::grants('worker')['jobs']);
        self::assertNotContains('UPDATE', CandidateDatabasePolicy::grants('user')['sites']);
        self::assertNotContains('UPDATE', CandidateDatabasePolicy::grants('user')['jobs']);
    }

    public function testOnlyTwoDevelopmentDatabaseNamesCanDefineRoleNames(): void
    {
        self::assertSame('wcp360_dev_user', CandidateDatabasePolicy::roles('wcp360_workbench')['user']);
        self::assertSame('wcp360_test_admin', CandidateDatabasePolicy::roles('wcp360_test')['admin']);
        foreach (['postgres', 'production', 'wcp360', 'wcp360_test; DROP ROLE x', 'WCP360_TEST'] as $database) {
            try {
                CandidateDatabasePolicy::roles($database);
                self::fail('Unsupported database accepted.');
            } catch (\RuntimeException $error) {
                self::assertSame(CandidateDatabaseRoles::REJECTED, $error->getMessage());
            }
        }
    }

    public function testUnsupportedRuntimeAndDisabledApplyCannotOpenOrMutateTheDatabase(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        foreach (['user', 'admin', 'worker'] as $context) {
            $provider = new CandidateDatabaseRoles($db, new RuntimeContext($context, true, 'test', 'localhost'), true);
            try {
                $provider->plan();
                self::fail('Ordinary runtime admitted to maintenance.');
            } catch (\RuntimeException $error) {
                self::assertSame(CandidateDatabaseRoles::REJECTED, $error->getMessage());
                self::assertFalse($db->isConnected());
            }
        }
        $provider = new CandidateDatabaseRoles($db, new RuntimeContext('setup', true, 'test', 'localhost'));
        try {
            $provider->provision([]);
            self::fail('Disabled apply accepted.');
        } catch (\RuntimeException $error) {
            self::assertSame(CandidateDatabaseRoles::REJECTED, $error->getMessage());
            self::assertFalse($db->isConnected());
        }
    }

    public function testInvalidOrReusedPasswordsFailBeforeDatabaseConnectionWithoutLeakingInputs(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $provider = new CandidateDatabaseRoles($db, new RuntimeContext('setup', true, 'test', 'localhost'), true);
        $good = ['user' => str_repeat('a',64), 'admin' => str_repeat('b',64), 'worker' => str_repeat('c',64)];
        foreach ([
            [], array_diff_key($good, ['worker' => true]), $good + ['setup' => 'private-unwanted-input'],
            array_replace($good, ['worker' => $good['user']]),
            array_replace($good, ['user' => 'private-password-marker']),
            array_replace($good, ['user' => str_repeat('a',63)]),
            array_replace($good, ['user' => str_repeat('z',64)]),
            array_replace($good, ['user' => str_repeat('a',65)]),
        ] as $passwords) {
            try {
                $provider->provision($passwords);
                self::fail('Invalid credentials accepted.');
            } catch (\RuntimeException $error) {
                self::assertSame(CandidateDatabaseRoles::REJECTED, $error->getMessage());
                self::assertNull($error->getPrevious());
                self::assertStringNotContainsString('private-password-marker', (string) $error);
                self::assertFalse($db->isConnected());
            }
        }
    }

    public function testSqliteCannotBecomeAProvisioningTarget(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $provider = new CandidateDatabaseRoles($db, new RuntimeContext('setup', true, 'test', 'localhost'), true);
        $this->expectExceptionMessage(CandidateDatabaseRoles::REJECTED);
        $provider->plan();
    }
}
