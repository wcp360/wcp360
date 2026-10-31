<?php
declare(strict_types=1);
namespace Wcp360\Core\Tests\Unit;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Hosting\Infrastructure\DisposableExecution;
final class DisposableExecutionTest extends TestCase
{
    public function testOnlyExplicitRootOwnedCurrentBootDebianVmIsAccepted(): void
    {
        $boot = "13e80812-7be4-4c72-90be-602091be2740\n";
        $marker = "wcp360.candidate.vm/1\n".$boot;
        $product = 'WCP360 disposable candidate VM';
        $release = "ID=debian\nVERSION_ID=\"13\"\n";
        self::assertTrue(DisposableExecution::acceptsVmEvidence($marker,$boot,$product,$release,0,0100644));
        foreach ([
            [$marker,str_replace('2740','2741',$boot),$product,$release,0,0644],
            [$marker,$boot,'Standard PC',$release,0,0644],
            [$marker,$boot,$product,str_replace('debian','ubuntu',$release),0,0644],
            [$marker,$boot,$product,str_replace('13','12',$release),0,0644],
            [$marker,$boot,$product,$release,31003,0644],
            [$marker,$boot,$product,$release,0,0664],
            [$marker,$boot,$product,$release,0,0646],
            ["wcp360.candidate.vm/1\n",'',$product,$release,0,0644],
            [$marker.'extra',$boot,$product,$release,0,0644],
        ] as $evidence) self::assertFalse(DisposableExecution::acceptsVmEvidence(...$evidence));
    }
}
