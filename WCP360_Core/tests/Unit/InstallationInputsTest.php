<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Setup\Infrastructure\InstallationInputs;

final class InstallationInputsTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir().'/wcp-install-inputs-'.bin2hex(random_bytes(8));
        mkdir($this->root, 0700);
        foreach (['src', 'config', 'migrations', 'installer', 'templates', 'bin', 'agents/wcpd/candidate/planner', 'assets', 'public'] as $directory) {
            mkdir($this->root.'/'.$directory, 0700, true);
        }
        foreach (['composer.json', 'composer.lock', 'importmap.php', 'public/index.php', 'src/fixture.php', 'migrations/fixture.php'] as $file) {
            file_put_contents($this->root.'/'.$file, 'trusted source');
        }
        file_put_contents($this->root.'/.env.example', "APP_SECRET=replace-with-a-random-local-secret\nDATABASE_URL=\"sqlite:///:memory:\"\n");
    }

    protected function tearDown(): void
    {
        $this->remove($this->root);
    }

    public function testBootstrapCreatesPrivateUniqueSecretOnceAndPreservesOperatorConfiguration(): void
    {
        $inputs = new InstallationInputs($this->root);
        $inputs->ensureEnvironment(true);
        $first = file_get_contents($this->root.'/.env');
        self::assertIsString($first);
        self::assertMatchesRegularExpression('/APP_SECRET=[a-f0-9]{64}\n/', $first);
        self::assertStringNotContainsString('replace-with', $first);
        $inputs->ensureEnvironment(true);
        self::assertSame($first, file_get_contents($this->root.'/.env'));
        self::assertCount(0, glob($this->root.'/.env.bootstrap-*'));
        if (PHP_OS_FAMILY !== 'Windows') {
            self::assertSame(0600, fileperms($this->root.'/.env') & 0777);
        }
    }

    public function testResumeCannotRecreateMissingConfiguration(): void
    {
        try {
            (new InstallationInputs($this->root))->ensureEnvironment(false);
            self::fail('Resume recreated missing configuration.');
        } catch (\RuntimeException $error) {
            self::assertStringContainsString('resume cannot recreate', $error->getMessage());
            self::assertFileDoesNotExist($this->root.'/.env');
        }
    }

    public function testBindingChangesForSourceMigrationConfigurationRuntimeAndArbitraryEnvironmentInputs(): void
    {
        $inputs = new InstallationInputs($this->root);
        $inputs->ensureEnvironment(true);
        $environment = ['DATABASE_URL' => 'postgresql://operator:secret@example.test/workbench', 'CUSTOM_DATABASE_PASSWORD' => 'first'];
        $runtime = ['php' => '8.4.27', 'platform' => 'debian-13'];
        $before = $inputs->fingerprint($environment, $runtime);
        self::assertMatchesRegularExpression('/^[a-f0-9]{64}$/D', $before);
        self::assertSame($before, $inputs->fingerprint(array_reverse($environment, true), array_reverse($runtime, true)));
        self::assertNotSame($before, $inputs->fingerprint($environment + ['PWD' => '/other/caller', 'TERM' => 'xterm'], $runtime));
        self::assertNotSame($before, $inputs->fingerprint(array_replace($environment, ['CUSTOM_DATABASE_PASSWORD' => 'second']), $runtime));
        self::assertNotSame($before, $inputs->fingerprint($environment, array_replace($runtime, ['platform' => 'debian-12'])));
        foreach (['composer.lock', 'migrations/fixture.php', 'src/fixture.php', '.env'] as $file) {
            $original = file_get_contents($this->root.'/'.$file);
            file_put_contents($this->root.'/'.$file, $original."\nchanged");
            self::assertNotSame($before, $inputs->fingerprint($environment, $runtime), $file);
            file_put_contents($this->root.'/'.$file, $original);
        }
        file_put_contents($this->root.'/.env.local', 'DATABASE_URL=other');
        chmod($this->root.'/.env.local', 0600);
        self::assertNotSame($before, $inputs->fingerprint($environment, $runtime));
    }

    public function testGeneratedOutputsDoNotInvalidateTheirOwnInstallationBinding(): void
    {
        $inputs = new InstallationInputs($this->root);
        $inputs->ensureEnvironment(true);
        $before = $inputs->fingerprint([], []);
        foreach (['var/bin', 'vendor', 'assets/vendor', 'public/assets'] as $directory) {
            mkdir($this->root.'/'.$directory, 0700, true);
            file_put_contents($this->root.'/'.$directory.'/generated', 'generated dependency');
        }
        file_put_contents($this->root.'/config/reference.php', '<?php // Symfony-generated IDE reference');
        self::assertSame($before, $inputs->fingerprint([], []));
    }

    public function testSymlinkedConfigurationIsRefusedWithoutChangingItsTarget(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            self::markTestSkipped('Creating symlinks requires a Windows privilege; exercised on every Linux target.');
        }
        $target = $this->root.'/external-secret';
        file_put_contents($target, 'preserve-me');
        symlink($target, $this->root.'/.env');
        try {
            (new InstallationInputs($this->root))->ensureEnvironment(true);
            self::fail('Symlinked configuration accepted.');
        } catch (\RuntimeException) {
            self::assertSame('preserve-me', file_get_contents($target));
        }
    }

    public function testWorldReadableConfigurationIsRefusedOnLinux(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            self::markTestSkipped('POSIX permission enforcement is exercised on Linux.');
        }
        file_put_contents($this->root.'/.env', 'APP_SECRET=private');
        chmod($this->root.'/.env', 0644);
        $this->expectException(\RuntimeException::class);
        (new InstallationInputs($this->root))->ensureEnvironment(false);
    }

    private function remove(string $path): void
    {
        if (is_link($path) || !is_dir($path)) {
            unlink($path);
            return;
        }
        foreach (scandir($path) ?: [] as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                $this->remove($path.'/'.$entry);
            }
        }
        rmdir($path);
    }
}
