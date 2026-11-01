<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Setup\Application\CandidateArtifactVerifier;
use Wcp360\Core\Shared\Platform\TargetPlatform;

final class CandidateArtifactVerifierTest extends TestCase
{
    private string $directory;
    private string $secretKey;
    private string $publicKey;
    /** @var list<string> */
    private array $files = [];

    protected function setUp(): void
    {
        $this->directory = dirname(__DIR__, 2).'/var/artifact-verifier-unit-'.bin2hex(random_bytes(8));
        self::assertTrue(mkdir($this->directory, 0700));
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
        if (isset($this->directory) && is_dir($this->directory)) {
            rmdir($this->directory);
        }
        if (isset($this->secretKey)) {
            sodium_memzero($this->secretKey);
        }
        parent::tearDown();
    }

    public function testPinnedSignatureAuthenticatesExactMetadataForAllDeclaredTargetsAndComponents(): void
    {
        foreach (TargetPlatform::profiles() as $profile) {
            foreach (['core', 'wcpd', 'wcp-privd'] as $component) {
                $manifest = $this->manifest(['target' => $profile['id'], 'component' => $component]);
                $fixture = $this->fixture($manifest);
                $report = CandidateArtifactVerifier::verify($fixture['paths'], $this->expected($manifest));

                self::assertTrue($report['candidate_verified'], $profile['id'].' '.$component);
                self::assertSame($manifest, $report['authenticated_metadata']);
                self::assertSame(hash('sha256', $this->publicKey), $report['signing_key_sha256']);
                self::assertSame(hash('sha256', $fixture['manifest_bytes']), $report['manifest_sha256']);
                self::assertSame($manifest['sha256'], $report['payload_sha256']);
                $this->assertNoInstallationAuthority($report);
            }
        }
    }

    public function testArgumentParsingPreservesOperatorPinsAndExactAbsolutePaths(): void
    {
        $fixture = $this->fixture();
        $expected = $this->expected();
        $arguments = [
            '--target=debian-13', '--signature='.$fixture['paths']['signature'],
            '--public-key='.$expected['public_key'], '--component=core',
            '--artifact='.$fixture['paths']['artifact'], '--version=1.0.0-rc.1',
            '--manifest='.$fixture['paths']['manifest'],
        ];
        self::assertSame(['paths' => $fixture['paths'], 'expected' => $expected], CandidateArtifactVerifier::parseArguments($arguments));
    }

    public function testInvalidArgumentsNeverEchoValuesOrBecomeImplicitDefaults(): void
    {
        $fixture = $this->fixture();
        $valid = $this->arguments($fixture['paths']);
        $invalid = [[], array_slice($valid, 0, 6), [...$valid, '--apply'], [...$valid, $valid[0]]];
        foreach ([
            [0, '--artifact=relative/private-marker'],
            [0, '--artifact=php://filter/private-marker'],
            [0, '--artifact='.$this->directory."/private-marker\n"],
            [0, '--artifact='.$this->directory."/private-marker\0"],
            [1, '--manifest='],
            [3, '--public-key='.str_repeat('A', 64)],
            [3, '--public-key='.str_repeat('a', 63)],
            [3, '--public-key='.str_repeat('a', 65)],
            [3, '--public-key='.str_repeat('g', 64)],
            [4, '--component=plugin-private-marker'],
            [5, '--version=01.0.0'],
            [5, '--version=1.0'],
            [5, '--version=1.0.0/private-marker'],
            [6, '--target=ubuntu'],
            [6, '--unknown=private-marker'],
        ] as [$index, $argument]) {
            $arguments = $valid;
            $arguments[$index] = $argument;
            $invalid[] = $arguments;
        }
        foreach ($invalid as $arguments) {
            try {
                CandidateArtifactVerifier::parseArguments($arguments);
                self::fail('Invalid artifact arguments were accepted.');
            } catch (\InvalidArgumentException $error) {
                self::assertNotSame('', $error->getMessage());
                self::assertStringNotContainsString('private-marker', $error->getMessage());
                self::assertStringNotContainsString($this->directory, $error->getMessage());
                self::assertStringNotContainsString(bin2hex($this->publicKey), $error->getMessage());
            }
        }
    }

    public function testWrongPinnedKeyCannotAuthenticateASelfConsistentArtifact(): void
    {
        $fixture = $this->fixture();
        $expected = $this->expected();
        $otherPair = sodium_crypto_sign_keypair();
        $expected['public_key'] = bin2hex(sodium_crypto_sign_publickey($otherPair));
        sodium_memzero($otherPair);
        $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $expected));
    }

    public function testUnrecognizedTargetAndNoncanonicalPathsCannotPassVerification(): void
    {
        $fixture = $this->fixture();
        $expected = $this->expected();
        $expected['target'] = 'debian-14';
        $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $expected));
        foreach ([$this->directory.'/../private-marker', $this->directory.'//private-marker'] as $path) {
            $paths = $fixture['paths'];
            $paths['artifact'] = $path;
            $this->assertRefusal(CandidateArtifactVerifier::verify($paths, $this->expected()));
        }
    }

    public function testManifestAndSignatureTamperingAreRejectedBeforeMetadataCanBeAuthenticated(): void
    {
        $fixture = $this->fixture();
        $bytes = str_replace('1.0.0-rc.1', '1.0.0-rc.2', $fixture['manifest_bytes']);
        $this->write($fixture['paths']['manifest'], $bytes);
        $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $this->expected()));

        $fixture = $this->fixture();
        $signature = sodium_crypto_sign_detached($fixture['manifest_bytes'], $this->secretKey);
        $signature[0] = chr(ord($signature[0]) ^ 1);
        $this->write($fixture['paths']['signature'], $signature);
        $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $this->expected()));
    }

    public function testMissingOrAdditionalNewlineCannotReuseAValidSignature(): void
    {
        foreach (['', "\n\n", "\r\n"] as $ending) {
            $fixture = $this->fixture();
            $this->write($fixture['paths']['manifest'], rtrim($fixture['manifest_bytes'], "\n").$ending);
            $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $this->expected()));
        }
    }

    public function testPayloadDigestAndExactSizeRejectTamperedTruncatedAndExtendedFiles(): void
    {
        foreach (["PAYLOAD-CANDIDATE\n", 'p', "payload-candidate\nextra"] as $payload) {
            $fixture = $this->fixture();
            $this->write($fixture['paths']['artifact'], $payload);
            $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $this->expected()));
        }
    }

    public function testSignatureRequiresExactlySixtyFourRawBytes(): void
    {
        $fixture = $this->fixture();
        $raw = sodium_crypto_sign_detached($fixture['manifest_bytes'], $this->secretKey);
        foreach (['', substr($raw, 0, 63), $raw."\n", bin2hex($raw), base64_encode($raw)] as $signature) {
            $this->write($fixture['paths']['signature'], $signature);
            $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $this->expected()));
        }
    }

    public function testEvenAuthenticallySignedMalformedOrAmbiguousJsonIsRejected(): void
    {
        $compact = rtrim($this->encode($this->manifest()), "\n");
        foreach ([
            '{"schema":',
            "[]\n",
            $compact,
            $compact."\n\n",
            $compact."\r\n",
            ' '.$compact."\n",
            json_encode($this->manifest(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n",
            str_replace('"component":"core"', '"component":"wcpd","component":"core"', $compact)."\n",
            str_replace('"schema":', '"sch\\u0065ma":', $compact)."\n",
            str_replace('candidate-artifact/1', 'candidate-artifact\\/1', $compact)."\n",
        ] as $bytes) {
            $fixture = $this->fixture();
            $this->signManifest($fixture['paths'], $bytes);
            $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $this->expected()));
        }
    }

    public function testSignedManifestHasExactlyTheSevenTypedFields(): void
    {
        $invalid = [];
        $manifest = $this->manifest();
        $manifest['unexpected'] = 'private-marker';
        $invalid[] = $manifest;
        $manifest = $this->manifest();
        unset($manifest['sha256']);
        $invalid[] = $manifest;
        foreach ([
            ['schema', 'wcp360.candidate-artifact/2'],
            ['component', 'plugin'], ['component', 1],
            ['version', '01.0.0'], ['version', 1],
            ['target', 'debian-14'], ['target', null],
            ['architecture', 'aarch64'],
            ['size', 0], ['size', -1], ['size', 1.0],
            ['size', '18'], ['size', 2147483649],
            ['sha256', strtoupper(hash('sha256', "payload-candidate\n"))],
            ['sha256', str_repeat('g', 64)], ['sha256', str_repeat('a', 63)],
        ] as [$key, $value]) {
            $manifest = $this->manifest();
            $manifest[$key] = $value;
            $invalid[] = $manifest;
        }
        foreach ($invalid as $manifest) {
            $fixture = $this->fixture();
            // Preserve a floating-point token so the JSON integer constraint is exercised.
            $bytes = json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR)."\n";
            $this->signManifest($fixture['paths'], $bytes);
            $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $this->expected()));
        }
    }

    public function testAuthenticatedArtifactCannotSubstituteAnotherExpectedIdentity(): void
    {
        $fixture = $this->fixture();
        foreach (['component' => 'wcpd', 'version' => '1.0.0-rc.2', 'target' => 'ubuntu-24.04'] as $key => $value) {
            $expected = $this->expected();
            $expected[$key] = $value;
            $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $expected));
        }
    }

    public function testUnreadableMissingOrOversizedMetadataRemainsARefusal(): void
    {
        $fixture = $this->fixture();
        $paths = $fixture['paths'];
        $paths['artifact'] = $this->directory.'/missing-private-marker';
        $this->assertRefusal(CandidateArtifactVerifier::verify($paths, $this->expected()));
        $paths = $fixture['paths'];
        $paths['manifest'] = $this->directory.'/missing-private-marker';
        $this->assertRefusal(CandidateArtifactVerifier::verify($paths, $this->expected()));
        $paths = $fixture['paths'];
        $paths['signature'] = $this->directory.'/missing-private-marker';
        $this->assertRefusal(CandidateArtifactVerifier::verify($paths, $this->expected()));
        $this->write($fixture['paths']['manifest'], str_repeat('private-marker', 2048));
        $this->assertRefusal(CandidateArtifactVerifier::verify($fixture['paths'], $this->expected()));
    }

    /** @return array<string, mixed> */
    private function manifest(array $override = []): array
    {
        return array_replace([
            'schema' => 'wcp360.candidate-artifact/1', 'component' => 'core',
            'version' => '1.0.0-rc.1', 'target' => 'debian-13', 'architecture' => 'amd64',
            'size' => strlen("payload-candidate\n"), 'sha256' => hash('sha256', "payload-candidate\n"),
        ], $override);
    }

    private function encode(array $manifest): string
    {
        return json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
    }

    private function expected(?array $manifest = null): array
    {
        $manifest ??= $this->manifest();

        return ['public_key' => bin2hex($this->publicKey), 'component' => $manifest['component'], 'version' => $manifest['version'], 'target' => $manifest['target']];
    }

    private function fixture(?array $manifest = null): array
    {
        $prefix = $this->directory.'/'.bin2hex(random_bytes(5));
        $paths = ['artifact' => $prefix.'.payload', 'manifest' => $prefix.'.json', 'signature' => $prefix.'.sig'];
        $bytes = $this->encode($manifest ?? $this->manifest());
        $this->write($paths['artifact'], "payload-candidate\n");
        $this->signManifest($paths, $bytes);

        return ['paths' => $paths, 'manifest_bytes' => $bytes];
    }

    private function signManifest(array $paths, string $bytes): void
    {
        $this->write($paths['manifest'], $bytes);
        $this->write($paths['signature'], sodium_crypto_sign_detached($bytes, $this->secretKey));
    }

    private function write(string $path, string $bytes): void
    {
        self::assertSame(strlen($bytes), file_put_contents($path, $bytes));
        self::assertTrue(chmod($path, 0600));
        if (!in_array($path, $this->files, true)) {
            $this->files[] = $path;
        }
    }

    private function arguments(array $paths): array
    {
        return [
            '--artifact='.$paths['artifact'], '--manifest='.$paths['manifest'], '--signature='.$paths['signature'],
            '--public-key='.bin2hex($this->publicKey), '--component=core', '--version=1.0.0-rc.1', '--target=debian-13',
        ];
    }

    private function assertRefusal(array $report): void
    {
        self::assertFalse($report['candidate_verified']);
        self::assertArrayNotHasKey('authenticated_metadata', $report);
        $this->assertNoInstallationAuthority($report);
        $encoded = json_encode($report, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString($this->directory, $encoded);
        self::assertStringNotContainsString('private-marker', $encoded);
        self::assertStringNotContainsString(bin2hex($this->publicKey), $encoded);
    }

    private function assertNoInstallationAuthority(array $report): void
    {
        foreach (['native_apply_allowed', 'qualified_release', 'host_changes', 'canonical_execution_plan'] as $flag) {
            self::assertFalse($report[$flag]);
        }
    }
}
