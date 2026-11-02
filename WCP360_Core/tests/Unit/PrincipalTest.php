<?php
declare(strict_types=1);
namespace Wcp360\Core\Tests\Unit;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
final class PrincipalTest extends TestCase
{
    public function testMalformedSessionIdentityIsRejected():void
    {
        $principal=(new \ReflectionClass(Principal::class))->newInstanceWithoutConstructor();
        $this->expectException(\UnexpectedValueException::class);
        $principal->__unserialize(['id'=>'id','email'=>[],'passwordHash'=>'value','context'=>'user','active'=>true]);
    }
}
