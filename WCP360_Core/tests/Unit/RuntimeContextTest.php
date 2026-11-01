<?php

declare(strict_types=1);
namespace Wcp360\Core\Tests\Unit;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Shared\Security\ContextUserChecker;
final class RuntimeContextTest extends TestCase
{
    public function testProductionBootIsExplicitlyRefused():void{$this->expectException(\RuntimeException::class);new RuntimeContext('user',true,'prod','user.localhost');}
    public function testDisabledCandidateIsRefused():void{$this->expectException(\RuntimeException::class);new RuntimeContext('user',false,'dev','user.localhost');}
    public function testUnknownContextIsRefused():void{$this->expectException(\InvalidArgumentException::class);new RuntimeContext('root',true,'dev','localhost');}
    public function testRemoteHostIsRefused():void{$this->expectException(\InvalidArgumentException::class);new RuntimeContext('user',true,'dev','panel.example.com');}
    public function testAdminSessionCannotAuthenticateOnUserRuntime():void{
        $this->expectException(\Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException::class);
        (new ContextUserChecker(new RuntimeContext('user',true,'test','user.localhost')))->checkPostAuth(new Principal('id','admin','admin@example.test','hash','admin'));
    }
}
