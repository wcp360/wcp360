<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class NativeInstallationPlanTest extends TestCase
{
    private const CHECKOUT_FILES = [
        'installer/preflight/native-plan.php',
        'installer/recipes/candidate-install.sh',
        'src/Shared/Platform/TargetPlatform.php',
        'src/Shared/Platform/RuntimePlan.php',
        'src/Shared/Platform/LinuxHostInventory.php',
        'src/Module/Setup/Application/NativeInstallationPlan.php',
        'config/platforms/targets.json',
        'config/platforms/runtime-providers.json',
    ];

    /** @var list<string> */
    private array $files = [];
    /** @var list<string> */
    private array $directories = [];

    protected function tearDown(): void
    {
        // Remove only the exact fixture files and directories created by this test.
        foreach (array_reverse($this->files) as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        foreach (array_reverse($this->directories) as $path) {
            if (is_dir($path)) {
                rmdir($path);
            }
        }
        parent::tearDown();
    }

    public function testStandaloneCheckoutReportsBlockedInspectionWithoutBootstrappingOrWriting(): void
    {
        $checkout = $this->checkout();
        $before = $this->snapshot($checkout);
        $process = $this->inspect($checkout);

        self::assertSame(78, $process->getExitCode());
        self::assertSame('', $process->getErrorOutput());
        $report = json_decode($process->getOutput(), true, 32, JSON_THROW_ON_ERROR);
        self::assertSame('wcp360.candidate-native-install-plan/1', $report['schema']);
        self::assertSame('fresh-host-inspection', $report['mode']);
        self::assertSame('operator_requested_checks_not_production_defaults', $report['requirements_source']);
        self::assertSame([
            'tcp_ports' => null,
            'udp_ports' => null,
            'memory_available_min_bytes' => null,
            'disk_free_min_bytes' => null,
        ], $report['requirements']);
        self::assertSame('current_process_namespace', $report['observations']['scope']);
        self::assertSame('not_checked', $report['checks']['requested_tcp_ports']);
        self::assertSame('not_checked', $report['checks']['requested_udp_ports']);
        self::assertSame('not_checked', $report['checks']['memory_available']);
        self::assertSame('not_checked', $report['checks']['disk_free_on_var']);
        foreach (['native_runtime_provider', 'artifact_identity_and_trust', 'registered_installation_effects'] as $check) {
            self::assertSame('blocked', $report['checks'][$check]);
            self::assertContains($check, $report['blockers']);
        }
        foreach (['canonical_execution_plan', 'native_apply_allowed', 'host_changes', 'qualified_release'] as $flag) {
            self::assertFalse($report[$flag]);
        }
        self::assertFalse($report['observations']['host_changes']);
        self::assertFalse($report['native_layout_reference']['applied']);
        self::assertSame($before, $this->snapshot($checkout));
        $this->assertNoSecretsOrBootstrap($checkout, $process);
    }

    public function testExplicitEmptyPortRequirementsAndResourceThresholdsRemainObservations(): void
    {
        $checkout = $this->checkout();
        $before = $this->snapshot($checkout);
        $process = $this->inspect($checkout, ['--tcp=none', '--udp=none', '--memory-mib=3', '--disk-mib=7']);

        self::assertSame(78, $process->getExitCode());
        self::assertSame('', $process->getErrorOutput());
        $report = json_decode($process->getOutput(), true, 32, JSON_THROW_ON_ERROR);
        self::assertSame([
            'tcp_ports' => [],
            'udp_ports' => [],
            'memory_available_min_bytes' => 3 * 1048576,
            'disk_free_min_bytes' => 7 * 1048576,
        ], $report['requirements']);
        self::assertSame(['tcp' => [], 'udp' => []], $report['port_conflicts']);
        self::assertSame('pass', $report['checks']['requested_tcp_ports']);
        self::assertSame('pass', $report['checks']['requested_udp_ports']);
        self::assertContains($report['checks']['memory_available'], ['pass', 'fail', 'unknown']);
        self::assertContains($report['checks']['disk_free_on_var'], ['pass', 'fail', 'unknown']);
        self::assertFalse($report['native_apply_allowed']);
        self::assertFalse($report['host_changes']);
        self::assertSame($before, $this->snapshot($checkout));
        $this->assertNoSecretsOrBootstrap($checkout, $process);
    }

    public function testInvalidArgumentsAreRejectedBeforeMissingConfigurationIsRead(): void
    {
        $checkout = $this->checkout();
        unlink($checkout.'/config/platforms/targets.json');
        unlink($checkout.'/config/platforms/runtime-providers.json');
        $before = $this->snapshot($checkout);
        $process = $this->inspect($checkout, ["--tcp=80\nargument-secret-do-not-display"]);

        self::assertSame(64, $process->getExitCode());
        self::assertSame('', $process->getOutput());
        self::assertSame("Usage: native-plan.php [--tcp=80,443|--tcp=none] [--udp=53|--udp=none] [--memory-mib=N] [--disk-mib=N]\n", $process->getErrorOutput());
        self::assertStringNotContainsString('argument-secret-do-not-display', $process->getErrorOutput());
        self::assertSame($before, $this->snapshot($checkout));
        $this->assertNoSecretsOrBootstrap($checkout, $process);
    }

    public function testUnavailableCatalogueProducesOnlyGenericFailureWithoutAnyInstallation(): void
    {
        $checkout = $this->checkout();
        file_put_contents($checkout.'/config/platforms/targets.json', '{"secret":"catalogue-secret-do-not-display"');
        $before = $this->snapshot($checkout);
        $process = $this->inspect($checkout);

        self::assertSame(1, $process->getExitCode());
        self::assertSame('', $process->getOutput());
        self::assertSame("Native installation inspection is unavailable. No installation was performed.\n", $process->getErrorOutput());
        self::assertStringNotContainsString('catalogue-secret-do-not-display', $process->getErrorOutput());
        self::assertStringNotContainsString($checkout, $process->getErrorOutput());
        self::assertSame($before, $this->snapshot($checkout));
        $this->assertNoSecretsOrBootstrap($checkout, $process);
    }

    public function testIncompleteCheckoutProducesOnlyGenericFailureWithoutLeakingImportPaths(): void
    {
        $checkout = $this->checkout();
        unlink($checkout.'/src/Shared/Platform/LinuxHostInventory.php');
        $before = $this->snapshot($checkout);
        $process = $this->inspect($checkout);

        self::assertSame(1, $process->getExitCode());
        self::assertSame('', $process->getOutput());
        self::assertSame("Native installation inspection is unavailable. No installation was performed.\n", $process->getErrorOutput());
        self::assertStringNotContainsString($checkout, $process->getErrorOutput());
        self::assertStringNotContainsString('LinuxHostInventory.php', $process->getErrorOutput());
        self::assertStringNotContainsString('Warning', $process->getErrorOutput());
        self::assertSame($before, $this->snapshot($checkout));
        $this->assertNoSecretsOrBootstrap($checkout, $process);
    }

    public function testLinuxShellHostCheckBypassesInstallerAndRejectsAdditionalArguments(): void
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            self::markTestSkipped('Requires Linux and Bash; Bash is unavailable on the Windows test host.');
        }
        $checkout = $this->checkout();
        $before = $this->snapshot($checkout);
        $environment = [
            'PATH' => dirname(PHP_BINARY).PATH_SEPARATOR.(getenv('PATH') ?: '/usr/bin:/bin'),
            'APP_ENV' => 'prod',
            'WCP_CANDIDATE' => '0',
        ];
        // An absent Bash on a Linux target is a failure, never a skipped check.
        $inspection = new Process(['bash', 'installer/recipes/candidate-install.sh', '--host-check'], $checkout, $environment);
        $inspection->setTimeout(15);
        $inspection->run();

        self::assertSame(78, $inspection->getExitCode(), $inspection->getErrorOutput());
        self::assertSame('', $inspection->getErrorOutput());
        $report = json_decode($inspection->getOutput(), true, 32, JSON_THROW_ON_ERROR);
        self::assertSame('wcp360.candidate-native-install-plan/1', $report['schema']);
        self::assertSame('blocked', $report['checks']['native_runtime_provider']);
        self::assertFalse($report['native_apply_allowed']);
        self::assertFalse($report['host_changes']);
        self::assertFalse($report['qualified_release']);
        self::assertSame($before, $this->snapshot($checkout));
        $this->assertNoSecretsOrBootstrap($checkout, $inspection);

        $extraArgument = new Process(['bash', 'installer/recipes/candidate-install.sh', '--host-check', '--tcp=80'], $checkout, $environment);
        $extraArgument->setTimeout(15);
        $extraArgument->run();

        self::assertSame(64, $extraArgument->getExitCode());
        self::assertSame('', $extraArgument->getOutput());
        self::assertSame("Usage: candidate-install.sh [--plan|--check-runtime|--host-check|--status|--resume]\n", $extraArgument->getErrorOutput());
        self::assertSame($before, $this->snapshot($checkout));
        $this->assertNoSecretsOrBootstrap($checkout, $extraArgument);
    }

    private function checkout(): string
    {
        $core = dirname(__DIR__, 2);
        $root = $core.'/var/native-preflight-test-'.bin2hex(random_bytes(8));
        $this->makeDirectory($root);
        foreach (self::CHECKOUT_FILES as $relative) {
            $this->makeDirectory(dirname($root.'/'.$relative));
            self::assertTrue(copy($core.'/'.$relative, $root.'/'.$relative));
            $this->files[] = $root.'/'.$relative;
        }
        // Deliberately invalid Dotenv syntax: this CLI must not bootstrap the app.
        self::assertNotFalse(file_put_contents($root.'/.env', 'APP_SECRET="dotenv-secret-do-not-display'));
        $this->files[] = $root.'/.env';

        return $root;
    }

    private function makeDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }
        $this->makeDirectory(dirname($path));
        self::assertTrue(mkdir($path, 0700));
        $this->directories[] = $path;
    }

    /** @param list<string> $arguments */
    private function inspect(string $checkout, array $arguments = []): Process
    {
        $process = new Process([PHP_BINARY, 'installer/preflight/native-plan.php', ...$arguments], $checkout, [
            'DATABASE_URL' => 'postgresql://unused:database-secret-do-not-display@127.0.0.1:1/unused',
            'WCP_TEST_DATABASE_URL' => 'invalid-test-url-do-not-display',
            'APP_SECRET' => 'environment-secret-do-not-display',
            'APP_ENV' => 'prod',
            'WCP_CANDIDATE' => '0',
        ]);
        $process->setTimeout(15);
        $process->run();

        return $process;
    }

    /** @return array<string, string> */
    private function snapshot(string $root): array
    {
        $snapshot = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $entry) {
            $relative = substr($entry->getPathname(), strlen($root) + 1);
            $snapshot[$relative] = $entry->isDir() ? 'directory' : (string) hash_file('sha256', $entry->getPathname());
        }
        ksort($snapshot);

        return $snapshot;
    }

    private function assertNoSecretsOrBootstrap(string $checkout, Process $process): void
    {
        self::assertDirectoryDoesNotExist($checkout.'/vendor');
        self::assertDirectoryDoesNotExist($checkout.'/var');
        self::assertDirectoryDoesNotExist($checkout.'/public');
        $output = $process->getOutput().$process->getErrorOutput();
        foreach (['dotenv-secret-do-not-display', 'environment-secret-do-not-display', 'database-secret-do-not-display', 'invalid-test-url-do-not-display', 'SQLSTATE', 'Stack trace:'] as $secret) {
            self::assertStringNotContainsString($secret, $output);
        }
    }
}
