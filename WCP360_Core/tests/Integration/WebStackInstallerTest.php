<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Wcp360\Core\Shared\Platform\WebStackCatalogue;

final class WebStackInstallerTest extends TestCase
{
    private string $checkout;
    /** @var list<string> */
    private array $files = [];
    /** @var list<string> */
    private array $directories = [];

    protected function setUp(): void
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            self::markTestSkipped('The installer Bash wrapper is exercised on Linux CI targets.');
        }
        $source = dirname(__DIR__, 2);
        $this->checkout = sys_get_temp_dir().'/wcp-web-stack-'.bin2hex(random_bytes(8)).' space;literal';
        $this->makeDirectory($this->checkout);
        foreach ([
            'installer/recipes/candidate-install.sh',
            'installer/preflight/runtime-plan.php',
            'src/Shared/Platform/RuntimePlan.php',
            'src/Shared/Platform/TargetPlatform.php',
            'src/Shared/Platform/WebStackCatalogue.php',
            'config/platforms/targets.json',
            'config/platforms/runtime-providers.json',
        ] as $relative) {
            $this->makeDirectory(dirname($this->checkout.'/'.$relative));
            self::assertTrue(copy($source.'/'.$relative, $this->checkout.'/'.$relative));
            $this->files[] = $this->checkout.'/'.$relative;
        }
        // These entry points must work without loading even a malformed Dotenv file.
        self::assertNotFalse(file_put_contents($this->checkout.'/.env', 'APP_SECRET="dotenv-secret-must-stay-private'));
        $this->files[] = $this->checkout.'/.env';
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->files) as $file) {
            if (is_file($file)) unlink($file);
        }
        foreach (array_reverse($this->directories) as $directory) {
            if (is_dir($directory)) rmdir($directory);
        }
    }

    /** @return iterable<string,array{bool}> */
    public static function planningFlags(): iterable
    {
        yield 'default planning' => [false];
        yield 'explicit worker planning' => [true];
    }

    #[DataProvider('planningFlags')]
    public function testCatalogueListingIsReadOnlyAndKeepsExplicitWorkerPlanning(bool $workerPlans): void
    {
        $process = $this->inspect($workerPlans ? ['--web-stacks', '--worker-plans'] : ['--web-stacks']);
        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertSame('', $process->getErrorOutput());
        $report = $this->report($process);
        self::assertSame(WebStackCatalogue::report($workerPlans), $report);
        self::assertFalse($report['native_installation_available']);
        self::assertFalse($report['migration_available']);
        self::assertFalse($report['qualified_release']);
    }

    #[DataProvider('planningFlags')]
    public function testModernPlanIsRefusedWithoutFallingBackToAnExistingEngine(bool $workerPlans): void
    {
        $arguments = ['--plan', '--web-stack=modern'];
        if ($workerPlans) $arguments[] = '--worker-plans';
        $process = $this->inspect($arguments);
        self::assertSame(78, $process->getExitCode(), $process->getErrorOutput());
        self::assertSame('', $process->getErrorOutput());
        $report = $this->report($process);
        self::assertSame('modern', $report['hosting_web_stack']['id']);
        self::assertSame('frankenphp_direct', $report['hosting_web_stack']['driver']);
        self::assertNull($report['hosting_web_stack']['selection_value']);
        self::assertFalse($report['hosting_web_stack']['plan_implemented']);
        self::assertFalse($report['hosting_web_stack']['selectable_for_plan']);
        self::assertFalse($report['native_installation_available']);
        self::assertFalse($report['host_changes']);
        self::assertFalse($report['qualified_release']);
    }

    /** @return iterable<string,array{list<string>}> */
    public static function invalidArguments(): iterable
    {
        yield 'unknown list flag' => [['--web-stacks', '--install']];
        yield 'duplicate listing' => [['--web-stacks', '--web-stacks']];
        yield 'duplicate worker flag' => [['--web-stacks', '--worker-plans', '--worker-plans']];
        yield 'conflicting selection' => [['--web-stacks', '--web-stack=classic']];
        yield 'unknown selected profile' => [['--plan', '--web-stack=unknown']];
        yield 'unsupported apply flag' => [['--plan', '--web-stack=classic', '--apply']];
        yield 'literal shell metacharacters' => [['--plan', '--web-stack=modern; touch unexpected-install']];
    }

    /** @param list<string> $arguments */
    #[DataProvider('invalidArguments')]
    public function testInvalidFlagsAreRefusedBeforeAnyBootstrapOrMutation(array $arguments): void
    {
        $process = $this->inspect($arguments);
        self::assertSame(64, $process->getExitCode());
        self::assertSame('', $process->getOutput());
        self::assertSame("Usage: php installer/preflight/runtime-plan.php [--web-stacks | --web-stack=classic|modern|legacy|advanced] [--worker-plans]\n", $process->getErrorOutput());
        self::assertFileDoesNotExist($this->checkout.'/unexpected-install');
    }

    public function testLegacySingleArgumentPlanKeepsItsOriginalReportShape(): void
    {
        $process = $this->inspect(['--plan']);
        self::assertContains($process->getExitCode(), [0, 78], $process->getErrorOutput());
        self::assertSame('', $process->getErrorOutput());
        $report = $this->report($process);
        self::assertSame(['supported_target', 'plan', 'host_changes', 'qualified_release'], array_keys($report));
        self::assertSame($report['supported_target'] ? 0 : 78, $process->getExitCode());
        self::assertFalse($report['host_changes']);
        self::assertFalse($report['qualified_release']);
    }

    public function testExtraHostCheckArgumentsKeepTheExactLegacyRefusal(): void
    {
        $process = $this->inspect(['--host-check', '--tcp=80']);
        self::assertSame(64, $process->getExitCode());
        self::assertSame('', $process->getOutput());
        self::assertSame("Usage: candidate-install.sh [--plan|--check-runtime|--host-check|--status|--resume]\n", $process->getErrorOutput());
    }

    private function makeDirectory(string $path): void
    {
        if (is_dir($path)) return;
        $this->makeDirectory(dirname($path));
        self::assertTrue(mkdir($path, 0700));
        $this->directories[] = $path;
    }

    /** @param list<string> $arguments */
    private function inspect(array $arguments): Process
    {
        $before = $this->snapshot();
        $process = new Process(['bash', $this->checkout.'/installer/recipes/candidate-install.sh', ...$arguments], $this->checkout, [
            'PATH' => dirname(PHP_BINARY).PATH_SEPARATOR.(getenv('PATH') ?: '/usr/bin:/bin'),
            'APP_ENV' => 'prod',
            'WCP_CANDIDATE' => '0',
            'DATABASE_URL' => 'postgresql://unused:database-secret-must-stay-private@127.0.0.1:1/unused',
            'APP_SECRET' => 'environment-secret-must-stay-private',
        ], timeout: 15);
        $process->run();
        self::assertSame($before, $this->snapshot());
        self::assertDirectoryDoesNotExist($this->checkout.'/vendor');
        self::assertDirectoryDoesNotExist($this->checkout.'/var');
        self::assertDirectoryDoesNotExist($this->checkout.'/public');
        foreach (['dotenv-secret-must-stay-private', 'database-secret-must-stay-private', 'environment-secret-must-stay-private'] as $secret) {
            self::assertStringNotContainsString($secret, $process->getOutput().$process->getErrorOutput());
        }

        return $process;
    }

    /** @return array<string,mixed> */
    private function report(Process $process): array
    {
        /** @var array<string,mixed> $report */
        $report = json_decode($process->getOutput(), true, 16, JSON_THROW_ON_ERROR);
        self::assertIsArray($report);

        return $report;
    }

    /** @return array<string,string> */
    private function snapshot(): array
    {
        $snapshot = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->checkout, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $entry) {
            $relative = substr($entry->getPathname(), strlen($this->checkout) + 1);
            $snapshot[$relative] = $entry->isDir() ? 'directory' : (string) hash_file('sha256', $entry->getPathname());
        }
        ksort($snapshot);

        return $snapshot;
    }
}
