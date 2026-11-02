<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class CandidateArtifactVerifierTest extends TestCase
{
    private const CHECKOUT_FILES = [
        'installer/preflight/artifact.php',
        'src/Shared/Platform/TargetPlatform.php',
        'src/Module/Setup/Infrastructure/ArtifactInput.php',
        'src/Module/Setup/Application/CandidateArtifactVerifier.php',
        'config/platforms/targets.json',
    ];

    /** @var list<string> */
    private array $files = [];
    /** @var list<string> */
    private array $directories = [];
    private string $secretKey;
    private string $publicKey;

    protected function setUp(): void
    {
        $pair = sodium_crypto_sign_keypair();
        $this->secretKey = sodium_crypto_sign_secretkey($pair);
        $this->publicKey = sodium_crypto_sign_publickey($pair);
        sodium_memzero($pair);
    }

    protected function tearDown(): void
    {
        foreach (array_reverse($this->files) as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        foreach (array_reverse($this->directories) as $directory) {
            if (is_dir($directory)) {
                rmdir($directory);
            }
        }
        if (isset($this->secretKey)) {
            sodium_memzero($this->secretKey);
        }
        parent::tearDown();
    }

    public function testStandaloneCliAuthenticatesWithoutComposerEnvironmentOrHostChanges(): void
    {
        $fixture = $this->checkout();
        $before = $this->snapshot($fixture['checkout']);
        $process = $this->verify($fixture);

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertSame('', $process->getErrorOutput());
        $report = json_decode($process->getOutput(), true, 32, JSON_THROW_ON_ERROR);
        self::assertTrue($report['candidate_verified']);
        self::assertSame($fixture['manifest'], $report['authenticated_metadata']);
        self::assertSame(hash('sha256', $this->publicKey), $report['signing_key_sha256']);
        self::assertSame(hash('sha256', $fixture['manifest_bytes']), $report['manifest_sha256']);
        self::assertSame($fixture['manifest']['sha256'], $report['payload_sha256']);
        $this->assertNoBootstrapOrLeak($fixture['checkout'], $process, $report);
        self::assertSame($before, $this->snapshot($fixture['checkout']));
    }

    public function testCorruptAndMissingInputsProduceGenericJsonRefusals(): void
    {
        foreach (['artifact', 'manifest', 'signature', 'missing_artifact', 'missing_manifest', 'missing_signature'] as $case) {
            $fixture = $this->checkout();
            $key = str_replace('missing_', '', $case);
            if (str_starts_with($case, 'missing_')) {
                self::assertTrue(unlink($fixture['paths'][$key]));
            } else {
                $this->write($fixture['paths'][$key], 'private-input-marker-do-not-display');
            }
            $before = $this->snapshot($fixture['checkout']);
            $process = $this->verify($fixture);

            self::assertSame(1, $process->getExitCode());
            self::assertSame('', $process->getErrorOutput());
            $report = json_decode($process->getOutput(), true, 32, JSON_THROW_ON_ERROR);
            self::assertFalse($report['candidate_verified']);
            self::assertArrayNotHasKey('authenticated_metadata', $report);
            $this->assertNoBootstrapOrLeak($fixture['checkout'], $process, $report);
            self::assertSame($before, $this->snapshot($fixture['checkout']));
        }
    }

    public function testInvalidArgumentsAreRejectedBeforePlatformConfigurationIsRead(): void
    {
        $fixture = $this->checkout();
        self::assertTrue(unlink($fixture['checkout'].'/config/platforms/targets.json'));
        $arguments = $this->arguments($fixture);
        $arguments[0] = '--artifact=relative/private-input-marker-do-not-display';
        $before = $this->snapshot($fixture['checkout']);
        $process = $this->verify($fixture, $arguments);

        self::assertSame(64, $process->getExitCode());
        self::assertSame('', $process->getOutput());
        self::assertStringStartsWith('Usage: artifact.php ', $process->getErrorOutput());
        self::assertStringNotContainsString('Warning', $process->getErrorOutput());
        self::assertStringNotContainsString('private-input-marker', $process->getErrorOutput());
        self::assertStringNotContainsString($fixture['checkout'], $process->getErrorOutput());
        self::assertStringNotContainsString(bin2hex($this->publicKey), $process->getErrorOutput());
        self::assertSame($before, $this->snapshot($fixture['checkout']));
    }

    public function testUnavailableCatalogueRefusesWithoutLeakingPrivateContents(): void
    {
        $fixture = $this->checkout();
        $this->write($fixture['checkout'].'/config/platforms/targets.json', '{"private-input-marker-do-not-display":');
        $before = $this->snapshot($fixture['checkout']);
        $process = $this->verify($fixture);

        self::assertSame(1, $process->getExitCode());
        self::assertSame('', $process->getErrorOutput());
        $report = json_decode($process->getOutput(), true, 32, JSON_THROW_ON_ERROR);
        self::assertFalse($report['candidate_verified']);
        $this->assertNoBootstrapOrLeak($fixture['checkout'], $process, $report);
        self::assertSame($before, $this->snapshot($fixture['checkout']));
    }

    public function testIncompleteCheckoutFailsGenericallyWithoutImportWarnings(): void
    {
        $fixture = $this->checkout();
        self::assertTrue(unlink($fixture['checkout'].'/src/Module/Setup/Infrastructure/ArtifactInput.php'));
        $before = $this->snapshot($fixture['checkout']);
        $process = $this->verify($fixture);

        self::assertSame(1, $process->getExitCode());
        self::assertSame('', $process->getErrorOutput());
        $report = json_decode($process->getOutput(), true, 32, JSON_THROW_ON_ERROR);
        self::assertFalse($report['candidate_verified']);
        $this->assertNoBootstrapOrLeak($fixture['checkout'], $process, $report);
        self::assertSame($before, $this->snapshot($fixture['checkout']));
    }

    private function checkout(): array
    {
        $core = dirname(__DIR__, 2);
        $root = $core.'/var/artifact-verifier-cli-'.bin2hex(random_bytes(8));
        $this->makeDirectory($root);
        foreach (self::CHECKOUT_FILES as $relative) {
            $this->makeDirectory(dirname($root.'/'.$relative));
            self::assertTrue(copy($core.'/'.$relative, $root.'/'.$relative));
            self::assertTrue(chmod($root.'/'.$relative, 0600));
            $this->files[] = $root.'/'.$relative;
        }
        $this->write($root.'/.env', 'APP_SECRET="dotenv-secret-do-not-display');
        $payload = "standalone-artifact\0payload\n";
        $manifest = [
            'schema' => 'wcp360.candidate-artifact/1', 'component' => 'core',
            'version' => '1.0.0-rc.1', 'target' => 'debian-13', 'architecture' => 'amd64',
            'size' => strlen($payload), 'sha256' => hash('sha256', $payload),
        ];
        $bytes = json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
        $paths = ['artifact' => $root.'/candidate.payload', 'manifest' => $root.'/candidate.json', 'signature' => $root.'/candidate.sig'];
        $this->write($paths['artifact'], $payload);
        $this->write($paths['manifest'], $bytes);
        $this->write($paths['signature'], sodium_crypto_sign_detached($bytes, $this->secretKey));

        return ['checkout' => $root, 'paths' => $paths, 'manifest' => $manifest, 'manifest_bytes' => $bytes];
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

    private function write(string $path, string $bytes): void
    {
        self::assertSame(strlen($bytes), file_put_contents($path, $bytes));
        self::assertTrue(chmod($path, 0600));
        if (!in_array($path, $this->files, true)) {
            $this->files[] = $path;
        }
    }

    private function arguments(array $fixture): array
    {
        return [
            '--artifact='.$fixture['paths']['artifact'], '--manifest='.$fixture['paths']['manifest'],
            '--signature='.$fixture['paths']['signature'], '--public-key='.bin2hex($this->publicKey),
            '--component=core', '--version=1.0.0-rc.1', '--target=debian-13',
        ];
    }

    private function verify(array $fixture, ?array $arguments = null): Process
    {
        $process = new Process([PHP_BINARY, 'installer/preflight/artifact.php', ...($arguments ?? $this->arguments($fixture))], $fixture['checkout'], [
            'APP_ENV' => 'prod', 'WCP_CANDIDATE' => '0',
            'APP_SECRET' => 'environment-secret-do-not-display',
            'DATABASE_URL' => 'postgresql://unused:database-secret-do-not-display@127.0.0.1:1/unused',
            'WCP_TEST_DATABASE_URL' => 'invalid-test-url-do-not-display',
        ]);
        $process->setTimeout(15);
        $process->run();

        return $process;
    }

    private function assertNoBootstrapOrLeak(string $checkout, Process $process, array $report): void
    {
        foreach (['native_apply_allowed', 'qualified_release', 'host_changes', 'canonical_execution_plan'] as $flag) {
            self::assertFalse($report[$flag]);
        }
        foreach (['vendor', 'var', 'public'] as $directory) {
            self::assertDirectoryDoesNotExist($checkout.'/'.$directory);
        }
        $output = $process->getOutput().$process->getErrorOutput();
        foreach ([$checkout, bin2hex($this->publicKey), 'private-input-marker', 'dotenv-secret-do-not-display', 'environment-secret-do-not-display', 'database-secret-do-not-display', 'invalid-test-url-do-not-display', 'Warning', 'Stack trace:', 'SQLSTATE'] as $secret) {
            self::assertStringNotContainsString($secret, $output);
        }
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
}
