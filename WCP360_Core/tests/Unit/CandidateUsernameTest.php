<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\IdentityAccess\Domain\CandidateUsername;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

final class CandidateUsernameTest extends TestCase
{
    public function testCanonicalUsernamesAndSerializedIdentity(): void
    {
        foreach (['abc', 'alice', 'admin_01', 'a'.str_repeat('9', 31)] as $name) {
            self::assertSame($name, CandidateUsername::require($name));
        }
        $principal = new Principal('fixture-id', 'alice', 'contact@example.test', 'fixture-password-hash', 'user');
        $restored = unserialize(serialize($principal), ['allowed_classes' => [Principal::class]]);
        self::assertInstanceOf(Principal::class, $restored);
        self::assertSame('alice', $restored->getUserIdentifier());
        self::assertSame('contact@example.test', $restored->email);
        self::assertStringNotContainsString('fixture-password-hash', serialize($principal));
    }

    #[DataProvider('invalidNames')]
    public function testMalformedUsernamesAreRefused(string $value): void
    {
        self::assertFalse(CandidateUsername::valid($value));
        $this->expectException(\InvalidArgumentException::class);
        CandidateUsername::require($value);
    }

    public static function invalidNames(): iterable
    {
        foreach (['', 'ab', '1abc', '_abc', 'Alice', ' alice', 'alice ', 'alice@example.test',
            'first.last', 'first-last', 'élise', 'аlice', "alice\n", "ali\0ce", "' OR 1=1 --", str_repeat('a', 33)] as $name) {
            yield [$name];
        }
    }

    public function testInvalidUsernameInSerializedSessionIsRefused(): void
    {
        $principal = (new \ReflectionClass(Principal::class))->newInstanceWithoutConstructor();
        $this->expectException(\UnexpectedValueException::class);
        $principal->__unserialize(['id' => 'fixture-id', 'username' => 'alice@example.test',
            'email' => 'contact@example.test', 'passwordHash' => 'hash', 'context' => 'user', 'active' => true]);
    }
}
