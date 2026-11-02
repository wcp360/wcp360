<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Wcp360\Core\Module\Hosting\Infrastructure\SyntaxProcessRunner;

final class SyntaxProcessRunnerTest extends TestCase
{
    private const STDOUT_LIMIT = 262144;
    private const STDERR_LIMIT = 16384;

    public function testSuccessfulChildReceivesUnchangedStdinAndReturnsExactOutputBytes(): void
    {
        $input = "synthetic configuration\nUTF-8: é\nNUL: \0\n";
        $process = self::child('stdin', $input);

        $result = (new SyntaxProcessRunner())->run($process);

        self::assertSame('passed', $result->status);
        self::assertSame($input, $result->stdout);
        self::assertTrue($process->isStarted());
        self::assertSame(0, $process->getExitCode());
        self::assertFalse($process->isRunning());
    }

    public function testNonzeroExitDoesNotReturnCapturedConfigurationOrDiagnostics(): void
    {
        $process = self::child('exit-seven');

        $result = (new SyntaxProcessRunner())->run($process);

        self::assertSame('failed', $result->status);
        self::assertSame('', $result->stdout);
        self::assertSame(7, $process->getExitCode());
        self::assertFalse($process->isRunning());
    }

    public function testExactOutputAndDiagnosticLimitsAreAccepted(): void
    {
        $process = self::child('exact-limits');

        $result = (new SyntaxProcessRunner())->run($process);

        self::assertSame('passed', $result->status);
        self::assertSame(str_repeat('O', self::STDOUT_LIMIT), $result->stdout);
        self::assertSame(0, $process->getExitCode());
        self::assertFalse($process->isRunning());
    }

    public static function overflowingStreams(): iterable
    {
        yield 'stdout exceeds 256 KiB' => ['stdout-flood'];
        yield 'stderr exceeds 16 KiB' => ['stderr-flood'];
        yield 'stdout remains active during cleanup' => ['continuous-stdout'];
    }

    #[DataProvider('overflowingStreams')]
    public function testAnOverLimitChildIsStoppedAndItsOutputIsDiscarded(string $mode): void
    {
        $process = self::child($mode);
        $started = hrtime(true);

        $result = (new SyntaxProcessRunner())->run($process);

        $elapsed = (hrtime(true) - $started) / 1_000_000_000;
        self::assertSame('failed', $result->status);
        self::assertSame('', $result->stdout);
        self::assertTrue($process->isStarted());
        self::assertFalse($process->isRunning());
        self::assertLessThan(8.0, $elapsed, 'The overflowing child must not finish its ten-second sleep.');
    }

    public function testFiveSecondTimeoutStopsTheChildAndDiscardsEarlierOutput(): void
    {
        $process = self::child('timeout');
        $started = hrtime(true);

        $result = (new SyntaxProcessRunner())->run($process);

        $elapsed = (hrtime(true) - $started) / 1_000_000_000;
        self::assertSame('failed', $result->status);
        self::assertSame('', $result->stdout);
        self::assertTrue($process->isStarted());
        self::assertFalse($process->isRunning());
        self::assertGreaterThanOrEqual(4.5, $elapsed, 'The child must start and reach the configured timeout.');
        self::assertLessThan(8.0, $elapsed, 'The timed-out child must not complete its ten-second sleep.');
    }

    public function testParentAndExplicitProcessSentinelsAreNotForwardedToTheChild(): void
    {
        $processKey = 'WCP360_SYNTAX_TEST_PUTENV';
        $environmentKey = 'WCP360_SYNTAX_TEST_ENV';
        $serverKey = 'WCP360_SYNTAX_TEST_SERVER';
        $explicitKey = 'WCP360_SYNTAX_TEST_EXPLICIT';
        $previousProcess = getenv($processKey);
        $environmentExisted = array_key_exists($environmentKey, $_ENV);
        $previousEnvironment = $_ENV[$environmentKey] ?? null;
        $serverExisted = array_key_exists($serverKey, $_SERVER);
        $previousServer = $_SERVER[$serverKey] ?? null;

        try {
            putenv($processKey.'=synthetic-parent-value');
            $_ENV[$environmentKey] = 'synthetic-environment-value';
            $_SERVER[$serverKey] = 'synthetic-server-value';
            $process = self::child('environment');
            $process->setEnv([$explicitKey => 'synthetic-process-override']);

            $result = (new SyntaxProcessRunner())->run($process);

            self::assertSame('passed', $result->status);
            self::assertSame(json_encode([
                $processKey => false,
                $environmentKey => false,
                $serverKey => false,
                $explicitKey => false,
                'HOME' => '/nonexistent',
                'XDG_CONFIG_HOME' => '/nonexistent',
                'XDG_DATA_HOME' => '/nonexistent',
                'XDG_CACHE_HOME' => '/nonexistent',
                'XDG_STATE_HOME' => '/nonexistent',
                'XDG_RUNTIME_DIR' => '/nonexistent',
                'PHP_INI_SCAN_DIR' => '',
            ], JSON_THROW_ON_ERROR), $result->stdout);
            self::assertSame(0, $process->getExitCode());
            self::assertFalse($process->isRunning());
        } finally {
            putenv($previousProcess === false ? $processKey : $processKey.'='.$previousProcess);
            if ($environmentExisted) {
                $_ENV[$environmentKey] = $previousEnvironment;
            } else {
                unset($_ENV[$environmentKey]);
            }
            if ($serverExisted) {
                $_SERVER[$serverKey] = $previousServer;
            } else {
                unset($_SERVER[$serverKey]);
            }
        }
    }

    public function testStartFailureReturnsOnlyAFailedResult(): void
    {
        // A regular file cannot be used as a working directory.
        $process = new Process([PHP_BINARY, '-n', self::fixture(), 'stdin'], self::fixture());

        $result = (new SyntaxProcessRunner())->run($process);

        self::assertSame('failed', $result->status);
        self::assertSame('', $result->stdout);
        self::assertFalse($process->isRunning());
    }

    private static function child(string $mode, string $input = ''): Process
    {
        return new Process([PHP_BINARY, '-n', self::fixture(), $mode], dirname(__DIR__, 2), null, $input, 30);
    }

    private static function fixture(): string
    {
        return dirname(__DIR__).'/Fixtures/syntax-process.php';
    }
}
