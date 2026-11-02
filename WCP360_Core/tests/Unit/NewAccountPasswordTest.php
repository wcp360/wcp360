<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\IdentityAccess\Domain\NewAccountPassword;

final class NewAccountPasswordTest extends TestCase
{
    public function testPassphrasesUnicodeAndSpacesArePreserved(): void
    {
        foreach (['a long memorable passphrase', '  long phrase  ', str_repeat('é', 14), str_repeat('x', 256)] as $value) {
            self::assertSame($value, NewAccountPassword::require($value));
        }
    }

    public function testMalformedOrOutOfBoundsPasswordsAreRefused(): void
    {
        foreach (['', str_repeat('x', 13), str_repeat('é', 7), str_repeat('x', 257), "long password\0value", "long password".chr(255)] as $value) {
            try {
                NewAccountPassword::require($value);
                self::fail('Invalid new password accepted.');
            } catch (\InvalidArgumentException) {
                self::assertTrue(true);
            }
        }
    }
}
