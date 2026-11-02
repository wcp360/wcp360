<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Setup\Infrastructure\PostgresScram;

final class PostgresScramTest extends TestCase
{
    public function testVerifierMatchesRfc7677ClientProofAndServerSignature(): void
    {
        $salt = base64_decode('W22ZaJ0SNY7soEsUEjb6gQ==', true);
        $verifier = PostgresScram::verifier('pencil', $salt);
        $parts = explode('$', $verifier);
        self::assertSame(['SCRAM-SHA-256', '4096:W22ZaJ0SNY7soEsUEjb6gQ=='], array_slice($parts, 0, 2));
        [$storedText, $serverText] = explode(':', $parts[2]);
        $nonce = 'rOprNGfwEbeRWgbNEkqO%hvYDpWUa2RaTCAfuxFIlj)hNlF$k0';
        $message = 'n=user,r=rOprNGfwEbeRWgbNEkqO,r='.$nonce.',s=W22ZaJ0SNY7soEsUEjb6gQ==,i=4096,c=biws,r='.$nonce;
        $stored = base64_decode($storedText, true);
        $server = base64_decode($serverText, true);
        self::assertSame('6rriTRBi23WpRR/wtup+mMhUZUn/dB5nLTJRsjl95G4=', base64_encode(hash_hmac('sha256', $message, $server, true)));
        $signature = hash_hmac('sha256', $message, $stored, true);
        $proof = base64_decode('dHzbZapWIk4jUhN+Ute9ytag9zjfMHgsqmmiz7AndVQ=', true);
        self::assertSame($stored, hash('sha256', $proof ^ $signature, true));
    }

    public function testDefaultSaltIsFreshAndPasswordNeverAppearsInVerifier(): void
    {
        $password = str_repeat('e4', 32);
        $first = PostgresScram::verifier($password);
        self::assertNotSame($first, PostgresScram::verifier($password));
        self::assertStringNotContainsString($password, $first);
        self::assertMatchesRegularExpression('/\ASCRAM-SHA-256\$4096:[A-Za-z0-9+\/]{22}==\$[A-Za-z0-9+\/]{43}=:[A-Za-z0-9+\/]{43}=\z/D', $first);
    }

    #[DataProvider('invalidPasswords')]
    public function testUnsafeEncodingAndUnboundedInputAreRejected(string $password): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PostgresScram::verifier($password);
    }

    public static function invalidPasswords(): iterable
    {
        foreach (['', "private\0secret", "private\nsecret", 'contains space', 'unicode-é', str_repeat('a', 257)] as $password) {
            yield [$password];
        }
    }

    public function testInvalidSaltLengthIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PostgresScram::verifier('pencil', 'short');
    }
}
