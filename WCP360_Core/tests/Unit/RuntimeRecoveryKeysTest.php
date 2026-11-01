<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\RuntimeRecoveryKeys;
use Wcp360\Core\Shared\Security\RuntimeContext;

final class RuntimeRecoveryKeysTest extends TestCase
{
    public function testOnlyOwnSurfaceOrExplicitSetupCanReadAKey(): void
    {
        $names=['WCP_RECOVERY_USER_KEY','WCP_RECOVERY_ADMIN_KEY'];
        $saved=[];
        foreach($names as $name)$saved[$name]=[$_SERVER[$name]??null,$_ENV[$name]??null];
        try {
            $_SERVER[$names[0]]=$_ENV[$names[0]]=str_repeat('c',64);
            $_SERVER[$names[1]]=$_ENV[$names[1]]=str_repeat('d',64);
            foreach(['user','admin'] as $surface){
                $keys=new RuntimeRecoveryKeys(new RuntimeContext($surface,true,'test','localhost'));
                self::assertSame(str_repeat($surface==='user'?'c':'d',64),$keys->forSurface($surface));
                self::assertSame('',$keys->forSurface($surface==='user'?'admin':'user'));
                self::assertSame('',$keys->forSurface('unknown'));
            }
            $setup=new RuntimeRecoveryKeys(new RuntimeContext('setup',true,'test','localhost'));
            self::assertSame(str_repeat('c',64),$setup->forSurface('user'));
            self::assertSame(str_repeat('d',64),$setup->forSurface('admin'));
            $worker=new RuntimeRecoveryKeys(new RuntimeContext('worker',true,'test','localhost'));
            self::assertSame('',$worker->forSurface('user'));
            self::assertSame('',$worker->forSurface('admin'));
            $_SERVER[$names[0]]=$_ENV[$names[0]]='';
            self::assertSame('',$setup->forSurface('user'));
            $_SERVER[$names[0]]=$_ENV[$names[0]]='not-a-valid-recovery-key';
            self::assertSame('',$setup->forSurface('user'));
        } finally {
            foreach($saved as $name=>[$server,$environment]){
                if($server===null)unset($_SERVER[$name]);else $_SERVER[$name]=$server;
                if($environment===null)unset($_ENV[$name]);else $_ENV[$name]=$environment;
            }
        }
    }
}
