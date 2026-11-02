<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Wcp360\Core\Shared\Platform\TargetPlatform;

final class TargetPlatformTest extends TestCase
{
    private string $temporaryDirectory;

    protected function setUp(): void
    {
        $this->temporaryDirectory = sys_get_temp_dir().'/wcp360-platform-'.bin2hex(random_bytes(8));
        self::assertTrue(mkdir($this->temporaryDirectory, 0700));
    }

    protected function tearDown(): void
    {
        foreach (['etc-release', 'vendor-release', 'catalogue.json'] as $name) {
            $path = $this->temporaryDirectory.'/'.$name;
            if (is_file($path) || is_link($path)) {
                unlink($path);
            }
        }
        rmdir($this->temporaryDirectory);
    }

    public function testRecognizesAllFiveTargetsWithoutQualifyingTheirNativeRuntime(): void
    {
        $expected = [
            'debian-12' => ['debian', '12', '8.2'],
            'debian-13' => ['debian', '13', '8.4'],
            'ubuntu-22.04' => ['ubuntu', '22.04', '8.1'],
            'ubuntu-24.04' => ['ubuntu', '24.04', '8.3'],
            'ubuntu-26.04' => ['ubuntu', '26.04', '8.5'],
        ];
        self::assertSame(array_keys($expected), array_column(TargetPlatform::profiles(), 'id'));
        foreach ($expected as $id => [$os, $version, $nativePhp]) {
            foreach (["ID=$os\nVERSION_ID=$version\n", "# Distribution identity\nNAME=\"Example GNU/Linux\"\nID=\"$os\"\nVERSION_ID=\"$version\"\n", "ID='$os'\r\nVERSION_ID='$version'\r\n"] as $release) {
                $profile = TargetPlatform::detect($release);
                self::assertNotNull($profile, $id);
                self::assertSame($id, $profile['id']);
                self::assertSame($nativePhp, $profile['native_php_series']);
                self::assertSame('amd64', $profile['architecture']);
                self::assertSame('apt', $profile['package_manager']);
                self::assertArrayNotHasKey('qualified_release', $profile);
            }
        }
    }

    public function testRejectsAmbiguousMalformedDerivedAndUnsupportedIdentities(): void
    {
        $releases = [
            '',
            "ID=debian\n",
            "VERSION_ID=13\n",
            "ID_LIKE=debian\nVERSION_ID=13\n",
            "ID=linuxmint\nID_LIKE=ubuntu\nVERSION_ID=24.04\n",
            "ID=debian\nVERSION_ID=11\n",
            "ID=ubuntu\nVERSION_ID=25.10\n",
            "ID=ubuntu\nVERSION_ID=24.04.1\n",
            "ID=Debian\nVERSION_ID=13\n",
            "ID=debian\nVERSION_ID=13\nID=ubuntu\n",
            "ID=debian\nVERSION_ID=13\nVERSION_ID=12\n",
            "ID=debian\nVERSION_ID=13\nNAME=one\nNAME=two\n",
            "ID=debian\nVERSION_ID=13\nthis is not an assignment\n",
            "ID=\"debian\nVERSION_ID=13\n",
            "ID=debian\nVERSION_ID=\"13\" trailing\n",
            "ID=debian;echo unsafe\nVERSION_ID=13\n",
            'ID=$(printf debian)'."\nVERSION_ID=13\n",
            "ID=debian\0\nVERSION_ID=13\n",
            "ID=debian\nVERSION_ID=13\nNAME=has\ttab\n",
            "ID=debian\nVERSION_ID=13\nNAME=has\rreturn\n",
            "ID=debian\nVERSION_ID=13\nNAME=\xFF\n",
            "ID=debian\nVERSION_ID=13\n#".str_repeat('x', 65536),
        ];
        foreach ($releases as $release) {
            self::assertNull(TargetPlatform::detect($release), bin2hex(substr($release, 0, 80)));
        }
    }

    public function testEtcIdentityTakesPrecedenceAndVendorFallbackRequiresItsAbsence(): void
    {
        $etc = $this->temporaryDirectory.'/etc-release';
        $vendor = $this->temporaryDirectory.'/vendor-release';
        file_put_contents($vendor, "ID=ubuntu\nVERSION_ID=24.04\n");
        self::assertSame('ubuntu-24.04', TargetPlatform::detectHost($etc, $vendor)['id'] ?? null);
        file_put_contents($etc, "ID=debian\nVERSION_ID=12\n");
        self::assertSame('debian-12', TargetPlatform::detectHost($etc, $vendor)['id'] ?? null);
        file_put_contents($etc, "ID=unknown\nVERSION_ID=13\n");
        self::assertNull(TargetPlatform::detectHost($etc, $vendor));
        file_put_contents($etc, "ID=debian\nVERSION_ID=13\nID=ubuntu\n");
        self::assertNull(TargetPlatform::detectHost($etc, $vendor));
    }

    public function testOversizedReleaseAndCatalogueInputsAreBoundedWithoutUsingAFallback(): void
    {
        $etc = $this->temporaryDirectory.'/etc-release';
        $vendor = $this->temporaryDirectory.'/vendor-release';
        file_put_contents($vendor, "ID=ubuntu\nVERSION_ID=24.04\n");
        file_put_contents($etc, "ID=debian\nVERSION_ID=13\n".str_repeat('#x\n', 22000));
        self::assertNull(TargetPlatform::detectHost($etc, $vendor));
        $path = $this->temporaryDirectory.'/catalogue.json';
        file_put_contents($path, str_repeat(' ', 16385).file_get_contents(dirname(__DIR__, 2).'/config/platforms/targets.json'));
        $this->expectException(\RuntimeException::class);
        TargetPlatform::profiles($path);
    }

    public function testCatalogueRejectsExtraExecutableFieldsDuplicateTargetsAndWrongRequirements(): void
    {
        $catalogue = json_decode((string) file_get_contents(dirname(__DIR__, 2).'/config/platforms/targets.json'), true, 16, JSON_THROW_ON_ERROR);
        $variants = [];
        $extraField = $catalogue;
        $extraField['targets'][0]['executable'] = '/tmp/unsafe';
        $variants[] = $extraField;
        $duplicate = $catalogue;
        $duplicate['targets'][1] = $duplicate['targets'][0];
        $variants[] = $duplicate;
        $wrongPhp = $catalogue;
        $wrongPhp['core_php_series'] = '8.5';
        $variants[] = $wrongPhp;
        $wrongPackageManager = $catalogue;
        $wrongPackageManager['targets'][0]['package_manager'] = 'apt;echo unsafe';
        $variants[] = $wrongPackageManager;
        $wrongImage = $catalogue;
        $wrongImage['targets'][0]['image'] = 'untrusted:latest';
        $variants[] = $wrongImage;
        foreach ($variants as $variant) {
            $path = $this->temporaryDirectory.'/catalogue.json';
            file_put_contents($path, json_encode($variant, JSON_THROW_ON_ERROR));
            try {
                TargetPlatform::profiles($path);
                self::fail('Invalid platform catalogue was accepted.');
            } catch (\RuntimeException $error) {
                self::assertStringContainsString('platform', $error->getMessage());
            }
        }
    }

    public function testStandaloneIdentityCliReportsNoRuntimeOrReleaseQualification(): void
    {
        $process = new Process([PHP_BINARY, dirname(__DIR__, 2).'/installer/preflight/platform.php']);
        $process->run();
        $result = json_decode($process->getOutput(), true, 16, JSON_THROW_ON_ERROR);
        $expected = PHP_OS_FAMILY === 'Linux' ? TargetPlatform::detectHost() : null;
        self::assertSame($expected === null ? 78 : 0, $process->getExitCode(), $process->getErrorOutput());
        self::assertSame($expected !== null, $result['supported_target']);
        self::assertSame($expected, $result['platform']);
        self::assertSame(php_uname('m'), $result['host_architecture']);
        self::assertSame(php_uname('m') === 'x86_64', $result['architecture_supported']);
        self::assertSame('not_checked', $result['runtime_validation']);
        self::assertFalse($result['qualified_release']);
    }

    public function testPortableCandidatePreflightKeepsDependenciesAndRuntimeChecks(): void
    {
        $script = dirname(__DIR__, 2).'/installer/preflight/candidate.php';
        $process = new Process([PHP_BINARY, $script, '--dependencies']);
        $process->run();
        $result = json_decode($process->getOutput(), true, 16, JSON_THROW_ON_ERROR);
        self::assertSame('portable-development-preflight', $result['mode']);
        self::assertSame(PHP_VERSION_ID >= 80400 && PHP_VERSION_ID < 80500, $result['checks']['php_8_4']);
        self::assertTrue($result['checks']['composer_dependencies']);
        self::assertArrayNotHasKey('supported_os', $result['checks']);
        self::assertArrayNotHasKey('debian_13', $result['checks']);
        self::assertFalse($result['qualified_release']);
        self::assertSame(in_array(false, array_values($result['checks']), true) ? 1 : 0, $process->getExitCode());

        $target = new Process([PHP_BINARY, $script, '--target', '--dependencies']);
        $target->run();
        $targetResult = json_decode($target->getOutput(), true, 16, JSON_THROW_ON_ERROR);
        self::assertSame('target-preflight', $targetResult['mode']);
        self::assertSame(PHP_OS_FAMILY === 'Linux' && TargetPlatform::detectHost() !== null, $targetResult['checks']['supported_os']);
        self::assertSame(php_uname('m') === 'x86_64', $targetResult['checks']['amd64']);
        self::assertSame(extension_loaded('pdo_pgsql'), $targetResult['checks']['pdo_pgsql']);
        self::assertSame(in_array(false, array_values($targetResult['checks']), true) ? 1 : 0, $target->getExitCode());
    }

    public function testPreflightClisRejectUnknownAndRepeatedFlags(): void
    {
        foreach ([['platform.php', '--target'], ['candidate.php', '--unknown'], ['candidate.php', '--target', '--target']] as $arguments) {
            $script = array_shift($arguments);
            $process = new Process([PHP_BINARY, dirname(__DIR__, 2).'/installer/preflight/'.$script, ...$arguments]);
            $process->run();
            self::assertSame(64, $process->getExitCode());
            self::assertSame('', $process->getOutput());
            self::assertStringContainsString('Usage:', $process->getErrorOutput());
        }
    }
}
