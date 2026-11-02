<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Wcp360\Core\Shared\Platform\RuntimeReadiness;

final class RuntimeReadinessTest extends TestCase
{
    public function testQueriesActualPostgresqlVersionWithoutTrustingDoctrineHint(): void
    {
        $url = $this->databaseUrl();
        // A deliberately false Doctrine version hint must not override the live server.
        $url = preg_replace('/([?&])serverVersion=[^&]*/', '$1serverVersion=99', $url);
        self::assertIsString($url);
        if (!str_contains($url, 'serverVersion=')) {
            $url .= (str_contains($url, '?') ? '&' : '?').'serverVersion=99';
        }
        $report = RuntimeReadiness::check($url);
        self::assertTrue($report['checks']['database_url_supported']);
        self::assertTrue($report['checks']['postgresql_connected']);
        self::assertTrue($report['checks']['postgresql_17']);
        self::assertSame(17, $report['observed_postgresql_major']);
        self::assertTrue($report['ready']);
        self::assertFalse($report['qualified_release']);
        $encoded = json_encode($report, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString($url, $encoded);
        self::assertStringNotContainsString('password', $encoded);
    }

    public function testStandaloneCliUsesItsExplicitDatabaseEvenWhenTestOverrideIsInvalid(): void
    {
        $url = $this->databaseUrl();
        $process = new Process([PHP_BINARY, 'installer/preflight/runtime-readiness.php'], dirname(__DIR__, 2), [
            'DATABASE_URL' => $url,
            'WCP_TEST_DATABASE_URL' => 'sqlite:///:memory:',
            'APP_SECRET' => 'must-not-be-reported',
        ]);
        $process->setTimeout(15);
        $process->run();
        self::assertSame(0, $process->getExitCode(), 'The explicit PostgreSQL 17 runtime must pass readiness.');
        self::assertSame('', $process->getErrorOutput());
        $report = json_decode($process->getOutput(), true, 16, JSON_THROW_ON_ERROR);
        self::assertTrue($report['ready']);
        self::assertSame(17, $report['observed_postgresql_major']);
        self::assertFalse($report['qualified_release']);
        self::assertStringNotContainsString($url, $process->getOutput());
        self::assertStringNotContainsString('must-not-be-reported', $process->getOutput());
    }

    public function testFailedLoginStaysGenericAndDoesNotExposeConnectionData(): void
    {
        $url = preg_replace('~://([^:/@]+)(?::[^@]*)?@~', '://$1:deliberately-incorrect-readiness-password@', $this->databaseUrl());
        self::assertIsString($url);
        $report = RuntimeReadiness::check($url);
        self::assertTrue($report['checks']['database_url_supported']);
        self::assertFalse($report['checks']['postgresql_connected']);
        self::assertFalse($report['checks']['postgresql_17']);
        self::assertFalse($report['ready']);
        self::assertNull($report['observed_postgresql_major']);
        $encoded = json_encode($report, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString($url, $encoded);
        self::assertStringNotContainsString('deliberately-incorrect-readiness-password', $encoded);
        self::assertStringNotContainsString('SQLSTATE', $encoded);
    }

    private function databaseUrl(): string
    {
        $url = getenv('WCP_TEST_DATABASE_URL');
        if (!is_string($url) || (!str_starts_with($url, 'postgresql://') && !str_starts_with($url, 'postgres://'))) {
            self::markTestSkipped('Requires the isolated PostgreSQL 17 CI service with password authentication.');
        }

        return $url;
    }
}
