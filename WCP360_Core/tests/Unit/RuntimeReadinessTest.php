<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Wcp360\Core\Shared\Platform\RuntimeReadiness;

final class RuntimeReadinessTest extends TestCase
{
    public function testUsesVersionBoundariesRatherThanConfigurationHints(): void
    {
        foreach ([80399 => false, 80400 => true, 80427 => true, 80499 => true, 80500 => false] as $version => $supported) {
            self::assertSame($supported, RuntimeReadiness::phpVersionSupported($version));
        }
        foreach (['160999' => 16, '170000' => 17, '179999' => 17, '180000' => 18, '90624' => 9] as $version => $major) {
            self::assertSame($major, RuntimeReadiness::postgresqlMajor((string) $version));
        }
        foreach ([null, false, 170000, '17', '17.0', '170000junk', '1700000', "170000\n", '000000'] as $invalid) {
            self::assertNull(RuntimeReadiness::postgresqlMajor($invalid));
        }
    }

    public function testOnlyAcceptsBoundedPostgresqlConnectionDataWithReadOnlySession(): void
    {
        $connection = RuntimeReadiness::connectionParameters('postgresql://candidate:p%40ss%3Bword@db.local:5433/wcp360_test?serverVersion=18&charset=utf8&sslmode=require');
        self::assertNotNull($connection);
        self::assertSame('candidate', $connection['user']);
        self::assertSame('p@ss;word', $connection['password']);
        self::assertStringContainsString('host=db.local;port=5433;dbname=wcp360_test;', $connection['dsn']);
        self::assertStringContainsString('connect_timeout=3;', $connection['dsn']);
        self::assertStringContainsString('sslmode=require;', $connection['dsn']);
        self::assertStringContainsString('statement_timeout=3000', $connection['dsn']);
        self::assertStringContainsString('default_transaction_read_only=on', $connection['dsn']);
        self::assertStringNotContainsString('serverVersion', $connection['dsn']);
        self::assertStringNotContainsString('p@ss', $connection['dsn']);
        self::assertNotNull(RuntimeReadiness::connectionParameters('postgres://candidate:password@[::1]/wcp360_test'));
    }

    public function testRejectsUnsupportedUrlsAndDsnInjectionBeforeConnecting(): void
    {
        foreach ([
            '',
            'sqlite:///:memory:',
            'mysql://user:password@db/database',
            'postgresql://db/database',
            'postgresql://user:password@db/',
            'postgresql://user:password@db:0/database',
            'postgresql://user:password@db:99999/database',
            'postgresql://user:password@db/database/extra',
            'postgresql://user:password@db/database%3Bhost=elsewhere',
            'postgresql://user:password@db;host=elsewhere/database',
            'postgresql://user:p%00ass@db/database',
            'postgresql://user:password@db/database?sslmode=disable;host=elsewhere',
            'postgresql://user:password@db/database?options=arbitrary',
            'postgresql://user:password@db/database?sslmode=require&sslmode=disable',
            'postgresql://user:password@db/database?charset=latin1',
            'postgresql://user:password@db/database?serverVersion=17%0A',
            'postgresql://user:password@db/database#fragment',
            "postgresql://user:password@db/database\n",
            str_repeat('x', 8193),
        ] as $url) {
            self::assertNull(RuntimeReadiness::connectionParameters($url));
        }
    }

    public function testFailureReportsContainNoCredentialsAndCannotQualifyMissingDatabase(): void
    {
        $report = RuntimeReadiness::check('postgresql://private-user:private-secret@127.0.0.1/wcp360_test?options=not-allowed');
        self::assertFalse($report['ready']);
        self::assertFalse($report['checks']['database_url_supported']);
        self::assertFalse($report['checks']['postgresql_connected']);
        self::assertFalse($report['checks']['postgresql_17']);
        self::assertNull($report['observed_postgresql_major']);
        self::assertFalse($report['qualified_release']);
        $encoded = json_encode($report, JSON_THROW_ON_ERROR);
        foreach (['private-user', 'private-secret', '127.0.0.1', 'wcp360_test', 'postgresql://', 'dsn'] as $secret) {
            self::assertStringNotContainsString($secret, $encoded);
        }
    }

    public function testStandaloneCliDoesNotUseTheTestDatabaseOrPrintEnvironmentSecrets(): void
    {
        $process = new Process([PHP_BINARY, 'installer/preflight/runtime-readiness.php'], dirname(__DIR__, 2), [
            'DATABASE_URL' => 'sqlite:///:memory:',
            'WCP_TEST_DATABASE_URL' => 'postgresql://private-user:private-secret@private-host/private-db',
            'APP_SECRET' => 'private-app-secret',
        ]);
        $process->run();
        self::assertSame(1, $process->getExitCode());
        self::assertSame('', $process->getErrorOutput());
        $report = json_decode($process->getOutput(), true, 16, JSON_THROW_ON_ERROR);
        self::assertFalse($report['checks']['database_url_supported']);
        self::assertFalse($report['ready']);
        foreach (['private-user', 'private-secret', 'private-host', 'private-db', 'private-app-secret'] as $secret) {
            self::assertStringNotContainsString($secret, $process->getOutput().$process->getErrorOutput());
        }
    }
}
