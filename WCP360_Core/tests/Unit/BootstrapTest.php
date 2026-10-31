<?php
declare(strict_types=1);
namespace Wcp360\Core\Tests\Unit;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
final class BootstrapTest extends TestCase
{
    public function testPackagedBootstrapPopulatesServerWhenEnvironmentContainsValues():void
    {
        $core=dirname(__DIR__,2);$root=$core.'/var/bootstrap-probe-'.bin2hex(random_bytes(5));
        mkdir($root.'/config',0700,true);mkdir($root.'/vendor',0700);
        copy($core.'/config/bootstrap.php',$root.'/config/bootstrap.php');
        file_put_contents($root.'/vendor/autoload.php','<?php require '.var_export($core.'/vendor/autoload.php',true).';');
        file_put_contents($root.'/probe.php', '<?php unset($_SERVER["APP_ENV"],$_SERVER["APP_DEBUG"]); $_ENV["APP_ENV"]="test"; $_ENV["APP_DEBUG"]="0"; require __DIR__."/config/bootstrap.php"; echo json_encode([$_SERVER["APP_ENV"]??"missing",$_SERVER["APP_DEBUG"]??"missing"],JSON_THROW_ON_ERROR);');
        try{
            $process=new Process([PHP_BINARY,$root.'/probe.php'],null,null,null,10);$process->mustRun();
            self::assertSame(['test','0'],json_decode($process->getOutput(),true,8,JSON_THROW_ON_ERROR));
        }finally{
            foreach(['/probe.php','/config/bootstrap.php','/vendor/autoload.php']as $file)unlink($root.$file);
            rmdir($root.'/config');rmdir($root.'/vendor');rmdir($root);
        }
    }
}
