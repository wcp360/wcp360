<?php
declare(strict_types=1);
namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\Hosting\Infrastructure\ApplicationServerSchema;
use Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer;
use Wcp360\Core\Module\Platform\Application\ApplicationServerSettings;

final class ApplicationServerTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;
    private ApplicationServerPolicy $enabled;
    private ApplicationServerSettings $settings;
    private SubmitHostingPlan $submit;

    protected function setUp():void
    {
        self::bootKernel();
        $this->store=self::getContainer()->get(WorkbenchStore::class);
        $this->fixture=WorkbenchFixture::reset($this->store->db);
        $this->enabled=new ApplicationServerPolicy(true);
        $this->settings=new ApplicationServerSettings($this->store,$this->enabled);
        $this->submit=new SubmitHostingPlan($this->store,$this->enabled,$this->settings);
    }
    private function request(array $override=[]):array
    {
        return array_replace(['tenant_id'=>$this->fixture['tenants']['alice'],'hostname'=>'runtime.test','idempotency_key'=>'runtime-key-00001'],$override);
    }
    private function workerRequest():array
    {
        return $this->request(['application_server'=>'frankenphp_worker','worker_acknowledged'=>true]);
    }
    private function send(array $input):array{return $this->submit->submit($this->fixture['users']['alice'],$input);}
    private function defaultWorker():void{$this->settings->change($this->fixture['users']['admin'],'frankenphp_worker',1);}

    public function testInitialDefaultAndExistingSiteMigrationRemainFpm():void
    {
        $this->fixture=WorkbenchFixture::reset($this->store->db,false);
        $site=WorkbenchStore::id();
        $this->store->db->insert('wcp_candidate_sites',['id'=>$site,'tenant_id'=>$this->fixture['tenants']['alice'],'hostname'=>'old.test','state'=>'PLAN_READY','revision'=>1]);
        foreach(ApplicationServerSchema::statements()as $sql)$this->store->db->executeStatement($sql);
        self::assertSame(['application_server'=>'php_fpm','revision'=>1],$this->settings->current());
        self::assertSame('php_fpm',$this->store->db->fetchOne('SELECT application_server FROM wcp_candidate_sites WHERE id=?',[$site]));
        self::assertSame(0,(int)$this->store->db->fetchOne('SELECT worker_acknowledged FROM wcp_candidate_sites WHERE id=?',[$site]));
    }
    public function testWorkerPlanningIsDisabledUnlessOperatorEnablesIt():void
    {
        $submit=self::getContainer()->get(SubmitHostingPlan::class);
        $this->expectException(AccessDeniedHttpException::class);
        $submit->submit($this->fixture['users']['alice'],$this->workerRequest());
    }
    public function testWorkerRequiresExplicitAcknowledgement():void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->send($this->request(['application_server'=>'frankenphp_worker']));
    }
    public function testDefaultWorkerAlsoRequiresAcknowledgement():void
    {
        $this->defaultWorker();
        $this->expectException(BadRequestHttpException::class);
        $this->send($this->request());
    }
    public function testRejectedSelectionCreatesNoSiteJobOrAudit():void
    {
        foreach([['application_server'=>'cgi'],['application_server'=>null],['worker_acknowledged'=>null],['worker_acknowledged'=>'yes'],['worker_acknowledged'=>1]]as $bad){
            try{$this->send($this->request($bad));self::fail('Invalid selection accepted');}catch(BadRequestHttpException){}
        }
        self::assertSame(0,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
        self::assertSame(0,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_sites'));
        self::assertSame(0,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
    }
    public function testGlobalChangeAffectsNewPlansOnlyAndOldRetryKeepsSnapshot():void
    {
        $first=$this->send($this->request());
        $this->defaultWorker();
        $retry=$this->send($this->request());
        self::assertTrue($retry['replayed']);
        self::assertSame($first['job_id'],$retry['job_id']);
        self::assertSame('php_fpm',$this->store->job($this->fixture['users']['alice'],$first['job_id'])['application_server']);
        $next=$this->send($this->request(['hostname'=>'next.test','idempotency_key'=>'runtime-next-0001','worker_acknowledged'=>true]));
        self::assertSame('frankenphp_worker',$this->store->job($this->fixture['users']['alice'],$next['job_id'])['application_server']);
    }
    public function testExplicitFpmOverridesGlobalWorkerWithoutWorkerAcknowledgement():void
    {
        $this->defaultWorker();
        $job=$this->send($this->request(['application_server'=>'php_fpm']));
        self::assertSame('php_fpm',$this->store->job($this->fixture['users']['alice'],$job['job_id'])['application_server']);
    }
    public function testChangingRequestedModeWithSameKeyConflicts():void
    {
        $this->send($this->workerRequest());
        $this->expectException(ConflictHttpException::class);
        $this->send($this->request(['application_server'=>'php_fpm']));
    }
    public function testOnlyCurrentActiveAdminMayChangeGlobalDefault():void
    {
        try{$this->settings->change($this->fixture['users']['alice'],'php_fpm',1);self::fail('Tenant changed global settings');}catch(AccessDeniedHttpException){}
        $this->store->db->update('wcp_candidate_users',['active'=>0],['id'=>$this->fixture['users']['admin']]);
        try{$this->settings->change($this->fixture['users']['admin'],'php_fpm',1);self::fail('Disabled admin changed settings');}catch(AccessDeniedHttpException){}
        self::assertSame(1,$this->settings->current()['revision']);
        self::assertSame(0,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
    }
    public function testSettingsRevisionPreventsLostUpdates():void
    {
        $this->defaultWorker();
        self::assertSame(2,$this->settings->current()['revision']);
        self::assertSame('candidate.runtime.default.changed',$this->store->db->fetchOne('SELECT kind FROM wcp_candidate_audit'));
        $this->expectException(ConflictHttpException::class);
        $this->settings->change($this->fixture['users']['admin'],'php_fpm',1);
    }
    public function testWorkerAvailabilityIsRecheckedBeforeExecution():void
    {
        $job=$this->send($this->workerRequest());
        self::getContainer()->get(RunHostingPlan::class)->runOne();
        $row=$this->store->job($this->fixture['users']['alice'],$job['job_id']);
        self::assertSame('FAILED',$row['state']);
        self::assertNull($row['result']);
    }
    public function testWorkerPlannerProducesDedicatedProcessConfigsWithoutActivatingSite():void
    {
        $job=$this->send($this->workerRequest());
        $worker=new RunHostingPlan($this->store,self::getContainer()->get(GoPlanRenderer::class),$this->enabled);
        $worker->runOne();
        $row=$this->store->job($this->fixture['users']['alice'],$job['job_id']);
        self::assertSame('SUCCEEDED',$row['state']);
        $plan=json_decode($row['result'],true,16,JSON_THROW_ON_ERROR);
        self::assertSame('wcp360.candidate.plan/2',$plan['schema']);
        self::assertSame('frankenphp_worker',$plan['application_server']);
        self::assertArrayNotHasKey('php_fpm',$plan);
        self::assertStringContainsString('admin off',$plan['caddy']);
        self::assertStringContainsString('NoNewPrivileges=true',$plan['systemd_unit']);
        self::assertSame('PLAN_READY',$this->store->db->fetchOne('SELECT state FROM wcp_candidate_sites'));
    }
    public function testDatabaseRejectsWorkerWithoutAcknowledgement():void
    {
        $this->send($this->request());
        $this->expectException(\Doctrine\DBAL\Exception\DriverException::class);
        $this->store->db->executeStatement("UPDATE wcp_candidate_sites SET application_server='frankenphp_worker',worker_acknowledged=0");
    }
}
