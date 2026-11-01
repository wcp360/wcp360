<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateInstaller;

final class CandidateInstallerTest extends TestCase
{
    public function testStandaloneStatusWorksWithoutVendorOrConfigurationAndCreatesNoState(): void
    {
        $root = sys_get_temp_dir().'/wcp-installer-status-'.bin2hex(random_bytes(8));
        $source = dirname(__DIR__, 2);
        $files = ['installer/recipes/candidate-install.php', 'src/Module/Setup/Infrastructure/InstallationJournal.php', 'src/Module/Setup/Infrastructure/InstallationInputs.php', 'src/Module/Setup/Infrastructure/CandidateInstaller.php', 'src/Shared/Platform/TargetPlatform.php'];
        try {
            foreach ($files as $file) {
                $directory = dirname($root.'/'.$file);
                if (!is_dir($directory)) {
                    mkdir($directory, 0700, true);
                }
                copy($source.'/'.$file, $root.'/'.$file);
            }
            $process = new Process([PHP_BINARY, $root.'/installer/recipes/candidate-install.php', '--status'], $root, timeout: 10);
            $process->run();
            self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
            $report = json_decode($process->getOutput(), true, 16, JSON_THROW_ON_ERROR);
            self::assertSame('absent', $report['status']);
            self::assertFalse($report['qualified_release']);
            self::assertFileDoesNotExist($root.'/.env');
            self::assertDirectoryDoesNotExist($root.'/var');
            self::assertDirectoryDoesNotExist($root.'/vendor');
            $invalid = new Process([PHP_BINARY, $root.'/installer/recipes/candidate-install.php', '--reset'], $root, timeout: 10);
            $invalid->run();
            self::assertSame(64, $invalid->getExitCode());
            self::assertDirectoryDoesNotExist($root.'/var');
        } finally {
            $this->remove($root);
        }
    }

    public function testUnknownStepCannotBecomeAnExecutableCommand(): void
    {
        $installer = new CandidateInstaller(dirname(__DIR__, 2), PHP_BINARY, '/unused/composer', '/unused/go');
        $this->expectException(\InvalidArgumentException::class);
        $installer->execute('database_migrations; touch /tmp/injected');
    }

    public function testFixedChildProcessHandlesMetacharactersAsLiteralPaths(): void
    {
        $directory = sys_get_temp_dir().'/wcp-installer-argv-'.bin2hex(random_bytes(8)).' space;quote';
        mkdir($directory, 0700);
        $composer = $directory.'/composer script.php';
        // The real PHP child records only its argv, providing evidence that no
        // shell expansion or command string was used for the installer path.
        file_put_contents($composer, '<?php file_put_contents(__DIR__."/argv.json", json_encode(array_slice($argv, 1)));');
        try {
            $installer = new CandidateInstaller($directory, PHP_BINARY, $composer, '/unused/go');
            self::assertSame(0, $installer->execute('composer_install'));
            self::assertSame(['install', '--no-interaction', '--prefer-dist', '--no-progress'], json_decode((string) file_get_contents($directory.'/argv.json'), true));
        } finally {
            $this->remove($directory);
        }
    }

    public function testRawChildDiagnosticsCannotLeakConfigurationToInstallerOutput(): void
    {
        $directory = sys_get_temp_dir().'/wcp-installer-redaction-'.bin2hex(random_bytes(8));
        mkdir($directory, 0700);
        $composer = $directory.'/composer.php';
        file_put_contents($composer, '<?php echo "APP_SECRET=must-stay-private"; fwrite(STDERR, "postgresql://user:password@host/db"); exit(23);');
        $source = dirname(__DIR__, 2).'/src/Module/Setup/Infrastructure/CandidateInstaller.php';
        $wrapper = $directory.'/invoke.php';
        file_put_contents($wrapper, '<?php require '.var_export($source, true).'; $i=new \\Wcp360\\Core\\Module\\Setup\\Infrastructure\\CandidateInstaller('.var_export($directory, true).', PHP_BINARY, '.var_export($composer, true).', "/unused/go"); exit($i->execute("composer_install"));');
        try {
            $process = new Process([PHP_BINARY, $wrapper], $directory, timeout: 10);
            $process->run();
            self::assertSame(23, $process->getExitCode());
            self::assertSame('', $process->getOutput());
            self::assertSame('', $process->getErrorOutput());
        } finally {
            $this->remove($directory);
        }
    }

    public function testCompletedMigrationVerificationReadsMetadataAndRefusesMissingOrUnknownVersions(): void
    {
        $directory = sys_get_temp_dir().'/wcp-installer-migrations-'.bin2hex(random_bytes(8));
        mkdir($directory, 0700);
        $root = dirname(__DIR__, 2);
        $database = str_replace('\\', '/', $directory.'/isolated.db');
        $wrapper = $directory.'/invoke.php';
        file_put_contents($wrapper, '<?php require '.var_export($root.'/src/Module/Setup/Infrastructure/CandidateInstaller.php', true).'; $i=new \\Wcp360\\Core\\Module\\Setup\\Infrastructure\\CandidateInstaller('.var_export($root, true).', PHP_BINARY, "/unused/composer", "/unused/go"); exit($i->execute($argv[1]));');
        $environment = ['DATABASE_URL' => 'sqlite:///'.$database, 'APP_ENV' => 'test', 'APP_DEBUG' => '0', 'APP_SECRET' => 'isolated-installer-test-only', 'WCP_CANDIDATE' => '1', 'WCP_CONTEXT' => 'user', 'WCP_PUBLIC_HOST' => 'user.localhost'];
        $run = static function (string $step) use ($wrapper, $root, $environment): int {
            $process = new Process([PHP_BINARY, $wrapper, $step], $root, $environment, timeout: 60);
            $process->run();
            return $process->getExitCode();
        };
        try {
            self::assertSame(0, $run('database_migrations'), 'Only the isolated temporary SQLite database may be migrated.');
            $pdo = new \PDO('sqlite:'.$database);
            $versions = $pdo->query('SELECT version FROM doctrine_migration_versions ORDER BY version')->fetchAll(\PDO::FETCH_COLUMN);
            self::assertNotEmpty($versions);
            $before = hash_file('sha256', $database);
            self::assertSame(0, $run('database_verify'));
            self::assertSame($before, hash_file('sha256', $database), 'Verification must not write migration metadata.');
            $pdo->prepare('INSERT INTO doctrine_migration_versions (version) VALUES (?)')->execute(['Unregistered\\Version20261101000000']);
            self::assertSame(2, $run('database_verify'), 'Unknown executed versions must fail verification.');
            $pdo->exec('DELETE FROM doctrine_migration_versions');
            self::assertSame(1, $run('database_verify'), 'Missing migrations must fail verification instead of being applied.');
            self::assertSame(0, (int) $pdo->query('SELECT COUNT(*) FROM doctrine_migration_versions')->fetchColumn());
            $pdo = null;
        } finally {
            unset($pdo);
            $this->remove($directory);
        }
    }

    private function remove(string $path): void
    {
        if (!file_exists($path) && !is_link($path)) {
            return;
        }
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
