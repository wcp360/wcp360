<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wcp360\Core\Module\Platform\Application\ServiceObservation;
use Wcp360\Core\Module\Platform\Domain\ServiceObservationSnapshot;
use Wcp360\Core\Module\Platform\Domain\ServiceUnitCatalogue;
use Wcp360\Core\Module\Platform\Infrastructure\SystemdServiceObservation;
use Wcp360\Core\Shared\Platform\TargetPlatform;

final class SystemdServiceObservationTest extends KernelTestCase
{
    public function testContainerResolvesTheObservationPortWithoutCallingRead(): void
    {
        self::bootKernel();
        $service = self::getContainer()->get(ServiceObservation::class);
        self::assertInstanceOf(SystemdServiceObservation::class, $service);
        $reflection = new \ReflectionClass(SystemdServiceObservation::class);
        self::assertTrue($reflection->isFinal());
        self::assertNull($reflection->getConstructor());
        self::assertSame(['read'], array_map(static fn (\ReflectionMethod $method): string => $method->getName(),
            $reflection->getMethods(\ReflectionMethod::IS_PUBLIC)));
        self::assertSame([], $reflection->getMethod('read')->getParameters());
    }

    public function testPrivateProcessDefinitionHasOneFixedSystemManagerQueryAndClosedStdin(): void
    {
        $method = new \ReflectionMethod(SystemdServiceObservation::class, 'processDefinition');
        foreach (['/usr/bin/systemctl', '/bin/systemctl'] as $binary) {
            $definition = $method->invoke(null, $binary);
            self::assertIsArray($definition);
            self::assertSame(['argv', 'cwd', 'environment', 'descriptors', 'options'], array_keys($definition));
            self::assertSame([$binary, 'show', '--no-pager', '--all',
                '--property=Id,LoadState,ActiveState,SubState,UnitFileState', '--', ...ServiceUnitCatalogue::unitNames()],
                $definition['argv']);
            self::assertCount(20, $definition['argv']);
            self::assertSame('/', $definition['cwd']);
            self::assertSame([0 => ['file', '/dev/null', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
                $definition['descriptors']);
            self::assertSame(['bypass_shell' => true, 'suppress_errors' => true], $definition['options']);
        }
        $reflection = new \ReflectionClass(SystemdServiceObservation::class);
        self::assertSame(3_000_000_000, $reflection->getConstant('TIMEOUT_NS'));
        self::assertSame(32768, $reflection->getConstant('STDOUT_LIMIT'));
        self::assertSame(8192, $reflection->getConstant('STDERR_LIMIT'));
    }

    public function testPrivateDefinitionNeverCopiesCallerOrProcessEnvironment(): void
    {
        $previousEnvironment = $_ENV;
        $previousServer = $_SERVER;
        $untrusted = [
            'DBUS_SYSTEM_BUS_ADDRESS' => 'unix:path=/nonexistent/untrusted-bus',
            'DBUS_SESSION_BUS_ADDRESS' => 'unix:path=/nonexistent/untrusted-session',
            'SYSTEMD_HOST' => 'untrusted.test', 'SYSTEMD_UNIT_PATH' => '/nonexistent/untrusted-units',
            'SYSTEMD_PAGER' => '/nonexistent/untrusted-pager', 'SYSTEMD_OFFLINE' => '1',
            'LD_PRELOAD' => '/nonexistent/untrusted.so', 'LD_LIBRARY_PATH' => '/nonexistent/untrusted-libs',
            'GCONV_PATH' => '/nonexistent/untrusted-gconv', 'HOME' => '/nonexistent/untrusted-home',
            'XDG_RUNTIME_DIR' => '/nonexistent/untrusted-runtime', 'PATH' => '/nonexistent/untrusted-bin',
            'LANG' => 'untrusted', 'LC_ALL' => 'untrusted', 'WCP_TEST_SYNTHETIC_SECRET' => 'synthetic-do-not-inherit',
        ];
        try {
            $_ENV = array_replace($_ENV, $untrusted);
            $_SERVER = array_replace($_SERVER, $untrusted);
            $method = new \ReflectionMethod(SystemdServiceObservation::class, 'processDefinition');
            $definition = $method->invoke(null, '/usr/bin/systemctl');
            self::assertSame([
                'LC_ALL' => 'C', 'LANG' => 'C', 'PATH' => '/usr/bin:/bin', 'HOME' => '/nonexistent',
                'XDG_CONFIG_HOME' => '/nonexistent', 'XDG_DATA_HOME' => '/nonexistent',
                'XDG_CACHE_HOME' => '/nonexistent', 'XDG_STATE_HOME' => '/nonexistent',
                'XDG_RUNTIME_DIR' => '/nonexistent',
            ], $definition['environment']);
        } finally {
            $_ENV = $previousEnvironment;
            $_SERVER = $previousServer;
        }
    }

    public function testPrivateDefinitionCannotBeReusedForAnotherExecutable(): void
    {
        // Inspection only: no process or injected command is executed by these contract checks.
        $method = new \ReflectionMethod(SystemdServiceObservation::class, 'processDefinition');
        foreach (['systemctl', '/usr/local/bin/systemctl', '/usr/bin/env', '/nonexistent/systemctl'] as $binary) {
            try {
                $method->invoke(null, $binary);
                self::fail('A different executable entered the closed observation definition.');
            } catch (\InvalidArgumentException $error) {
                self::assertSame('Invalid service observation executable.', $error->getMessage());
            }
        }
    }

    public function testRealAdapterReportsExplicitAvailabilityInTheCurrentNamespace(): void
    {
        $snapshot = (new SystemdServiceObservation())->read();
        self::assertInstanceOf(ServiceObservationSnapshot::class, $snapshot);
        self::assertContains($snapshot->status, ['observed', 'not_configured', 'failed']);
        self::assertSame(['status', 'units'], array_keys(get_object_vars($snapshot)));
        $unavailable = PHP_OS_FAMILY !== 'Linux' || !function_exists('posix_geteuid') || posix_geteuid() === 0;
        if (!$unavailable) {
            $manager = @file_get_contents('/proc/1/comm', false, null, 0, 17);
            $unavailable = TargetPlatform::detectHost() === null || !is_dir('/run/systemd/system')
                || ($manager !== "systemd\n" && $manager !== 'systemd');
        }
        if ($unavailable) {
            self::assertSame('not_configured', $snapshot->status,
                'An unsupported host or missing PID1 manager must never fabricate observed service states.');
        }
        if ($snapshot->status !== 'observed') {
            self::assertSame([], $snapshot->units);
        } else {
            self::assertNotEmpty($snapshot->units);
            self::assertLessThanOrEqual(14, count($snapshot->units));
            foreach ($snapshot->units as $unit) {
                self::assertContains($unit['id'], ServiceUnitCatalogue::unitNames());
            }
        }
    }
}
