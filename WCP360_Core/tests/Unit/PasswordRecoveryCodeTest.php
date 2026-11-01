<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Module\IdentityAccess\Domain\PasswordRecoveryCode;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

final class PasswordRecoveryCodeTest extends TestCase
{
    private const NOW = 1793577600;

    private function actor(string $surface = 'user'): Principal
    {
        return new Principal(Uuid::v7()->toRfc4122(), 'alice', 'alice@example.test', 'synthetic-verifier-only', $surface);
    }

    public function testCodesHaveIndependentRandomnessAndHideCredentialFields(): void
    {
        $actor = $this->actor();
        $key = str_repeat('c', 64);
        $one = PasswordRecoveryCode::issue($actor, 0, $key, self::NOW);
        $two = PasswordRecoveryCode::issue($actor, 0, $key, self::NOW);
        self::assertNotSame($one, $two);
        self::assertTrue(PasswordRecoveryCode::valid($one, $actor, 0, $key, self::NOW));
        self::assertTrue(PasswordRecoveryCode::valid($two, $actor, 0, $key, self::NOW));
        foreach ([$actor->username, $actor->email, $actor->getPassword(), $key] as $secret) self::assertStringNotContainsString($secret, $one);
    }

    public function testExpiryFutureTimeAndSurfaceAreStrict(): void
    {
        $actor = $this->actor();
        $key = str_repeat('c', 64);
        $code = PasswordRecoveryCode::issue($actor, 0, $key, self::NOW);
        self::assertSame($actor->id, PasswordRecoveryCode::subject($code, 'user', self::NOW + 899));
        self::assertFalse(PasswordRecoveryCode::valid($code, $actor, 0, $key, self::NOW + 900));
        self::assertFalse(PasswordRecoveryCode::valid($code, $actor, 0, $key, self::NOW - 1));
        self::assertNull(PasswordRecoveryCode::subject($code, 'admin', self::NOW));
        self::assertNull(PasswordRecoveryCode::subject($code, 'worker', self::NOW));
    }

    public function testEveryMutableAccountFieldAndFactorRevisionInvalidateTheCode(): void
    {
        $actor = $this->actor();
        $key = str_repeat('c', 64);
        $code = PasswordRecoveryCode::issue($actor, 3, $key, self::NOW);
        $changed = [
            new Principal($actor->id, 'alice_new', $actor->email, $actor->getPassword(), 'user'),
            new Principal($actor->id, $actor->username, 'new@example.test', $actor->getPassword(), 'user'),
            new Principal($actor->id, $actor->username, $actor->email, 'new-verifier', 'user'),
            new Principal($actor->id, $actor->username, $actor->email, $actor->getPassword(), 'admin'),
            new Principal($actor->id, $actor->username, $actor->email, $actor->getPassword(), 'user', false),
            $this->actor(),
        ];
        foreach ($changed as $other) self::assertFalse(PasswordRecoveryCode::valid($code, $other, 3, $key, self::NOW));
        self::assertFalse(PasswordRecoveryCode::valid($code, $actor, 4, $key, self::NOW));
        self::assertFalse(PasswordRecoveryCode::valid($code, $actor, 3, str_repeat('d',64), self::NOW));
    }

    public function testTamperingMalformedOversizedAndUnknownCodesFailClosed(): void
    {
        $actor = $this->actor();
        $key = str_repeat('c',64);
        $code = PasswordRecoveryCode::issue($actor,0,$key,self::NOW);
        $parts = explode('.',$code);
        foreach (['', ' '.$code, $code."\n", str_repeat('a',1000), str_replace('wcp-cr1','wcp-cr2',$code)] as $invalid) {
            self::assertFalse(PasswordRecoveryCode::valid($invalid,$actor,0,$key,self::NOW));
        }
        foreach ([1=>'admin',2=>Uuid::v7()->toRfc4122(),3=>(string)(self::NOW+1),4=>str_repeat('0',64),5=>str_repeat('0',64)] as $index=>$value) {
            $modified=$parts;
            $modified[$index]=$value;
            self::assertFalse(PasswordRecoveryCode::valid(implode('.',$modified),$actor,0,$key,self::NOW));
        }
        self::assertFalse(PasswordRecoveryCode::configured(''));
        self::assertFalse(PasswordRecoveryCode::configured(str_repeat('x',64)));
        self::assertFalse(PasswordRecoveryCode::configured(' '.str_repeat('c',64)));
    }
}
