<?php

declare(strict_types=1);
namespace Wcp360\Core\Tests\Lifecycle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Process\Process;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\ExtensionManagement\Application\PluginRegistry;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
final class PluginLifecycleTest extends KernelTestCase
{
    private WorkbenchStore $store;private PluginRegistry $plugins;private array $fixture;
    protected function setUp():void{self::bootKernel();$this->store=self::getContainer()->get(WorkbenchStore::class);$this->fixture=WorkbenchFixture::reset($this->store->db);$this->plugins=self::getContainer()->get(PluginRegistry::class);}
    private function transition(string $action):void{$this->plugins->transition($this->fixture['users']['admin'],'wcp360-reference-report',$action);}
    private function submit():array{return self::getContainer()->get(SubmitHostingPlan::class)->submit($this->fixture['users']['alice'],['tenant_id'=>$this->fixture['tenants']['alice'],'hostname'=>'plugin.test','idempotency_key'=>'plugin-key-0000001']);}
    public function testLifecyclePreservesCoreAndStandaloneReportWorks():void
    {
        self::assertSame([],$this->plugins->installed());$job=$this->submit();
        $this->transition('install');self::assertSame('DISABLED',$this->plugins->installed()[0]['state']);
        $this->transition('enable');$summary=$this->plugins->summary($this->fixture['users']['alice'],$this->fixture['tenants']['alice']);
        $process=new Process([PHP_BINARY,dirname(__DIR__,3).'/WCP360_Plugins/wcp360-reference-report/bin/report.php'],null,null,json_encode($summary,JSON_THROW_ON_ERROR),5);$process->run();self::assertSame(0,$process->getExitCode(),$process->getErrorOutput());self::assertSame("QUEUED: 1\n",str_replace("\r\n","\n",$process->getOutput()));
        $this->transition('disable');$this->transition('uninstall');self::assertSame([],$this->plugins->installed());self::assertSame('QUEUED',$this->store->job($this->fixture['users']['alice'],$job['job_id'])['state']);
    }
    public function testUserCannotInstallPlugin():void{$this->expectException(AccessDeniedHttpException::class);$this->plugins->transition($this->fixture['users']['alice'],'wcp360-reference-report','install');}
    public function testEnabledPackageMustBeDisabledBeforeUninstall():void{$this->transition('install');$this->transition('enable');$this->expectException(ConflictHttpException::class);$this->transition('uninstall');}
    public function testDisabledPackageCannotReadSummary():void{$this->transition('install');$this->expectException(NotFoundHttpException::class);$this->plugins->summary($this->fixture['users']['alice'],$this->fixture['tenants']['alice']);}
    public function testEnabledPackageDoesNotBypassTenantPermission():void{$this->transition('install');$this->transition('enable');$this->expectException(AccessDeniedHttpException::class);$this->plugins->summary($this->fixture['users']['alice'],$this->fixture['tenants']['bob']);}
    public function testMalformedWireDataIsRejectedByDetachedProcess():void
    {
        $process=new Process([PHP_BINARY,dirname(__DIR__,3).'/WCP360_Plugins/wcp360-reference-report/bin/report.php'],null,null,'{"schema":"wrong","counts":[]}',5);$process->run();self::assertSame(2,$process->getExitCode());self::assertSame('',$process->getOutput());
    }
    public function testMissingPackageCannotPreventDisablingAndUninstalling():void
    {
        $this->transition('install');$this->transition('enable');
        $unavailable=new PluginRegistry($this->store,dirname(__DIR__).'/Fixtures/nonexistent-package-root');
        $unavailable->transition($this->fixture['users']['admin'],'wcp360-reference-report','disable');
        self::assertSame('DISABLED',$this->plugins->installed()[0]['state']);
        $unavailable->transition($this->fixture['users']['admin'],'wcp360-reference-report','uninstall');
        self::assertSame([],$this->plugins->installed());
        self::assertNotEmpty($this->submit()['job_id']);
    }
}
