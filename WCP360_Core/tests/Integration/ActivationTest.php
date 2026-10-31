<?php
declare(strict_types=1);
namespace Wcp360\Core\Tests\Integration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Hosting\Application\{SubmitHostingPlan,SubmitActivation,RunActivation};
use Wcp360\Core\Module\Hosting\Domain\ProvisioningGateway;
final class ActivationTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;
    private SubmitActivation $submit;
    private object $gateway;
    protected function setUp():void
    {
        self::bootKernel();$this->store=self::getContainer()->get(WorkbenchStore::class);
        $this->fixture=WorkbenchFixture::reset($this->store->db);
        foreach(['alice','bob']as $name)$this->store->db->insert('wcp_candidate_activation_grants',['user_id'=>$this->fixture['users'][$name],'tenant_id'=>$this->fixture['tenants'][$name]]);
        $this->submit=new SubmitActivation($this->store,true);
        $this->gateway=new class implements ProvisioningGateway{
            public array $requests=[];public bool $fail=false;
            public function send(array $request):string{$this->requests[]=$request;if($this->fail)throw new \RuntimeException('Lost reply');return 'ACTIVE';}
        };
    }
    private function plan(bool $ready=true):string
    {
        $job=self::getContainer()->get(SubmitHostingPlan::class)->submit($this->fixture['users']['alice'],['tenant_id'=>$this->fixture['tenants']['alice'],'hostname'=>'activation.test','idempotency_key'=>'activation-key-001']);
        if($ready)self::getContainer()->get(RunHostingPlan::class)->runOne();
        return $job['job_id'];
    }
    private function activationWorker():RunActivation{return new RunActivation($this->store,$this->gateway,$this->submit);}
    private function state(string $id):string{return $this->store->db->fetchOne('SELECT state FROM wcp_candidate_activations WHERE id=?',[$id]);}
    public function testPlanningPermissionDoesNotGrantPhysicalActivation():void
    {
        $plan=$this->plan();
        $this->store->db->delete('wcp_candidate_activation_grants',['user_id'=>$this->fixture['users']['alice']]);
        $this->expectException(AccessDeniedHttpException::class);
        $this->submit->submit($this->fixture['users']['alice'],$plan);
    }
    public function testActivationGrantRevocationPreventsDispatch():void
    {
        $id=$this->submit->submit($this->fixture['users']['alice'],$this->plan());
        $this->store->db->delete('wcp_candidate_activation_grants',['user_id'=>$this->fixture['users']['alice']]);
        $this->activationWorker()->runOne();
        self::assertSame('FAILED',$this->state($id));self::assertSame([],$this->gateway->requests);
    }
    public function testDisabledActivationCreatesNoOperation():void
    {
        $id=$this->plan();
        try{self::getContainer()->get(SubmitActivation::class)->submit($this->fixture['users']['alice'],$id);self::fail('Disabled activation accepted');}catch(AccessDeniedHttpException){}
        self::assertSame(0,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_activations'));
    }
    public function testOnlySuccessfulPlansCanActivate():void
    {
        $id=$this->plan(false);$this->expectException(ConflictHttpException::class);$this->submit->submit($this->fixture['users']['alice'],$id);
    }
    public function testOtherTenantCannotActivatePlan():void
    {
        $id=$this->plan();$this->expectException(AccessDeniedHttpException::class);$this->submit->submit($this->fixture['users']['bob'],$id);
    }
    public function testQueueIsIdempotentAndSuccessIsPublishedWithAudit():void
    {
        $plan=$this->plan();$id=$this->submit->submit($this->fixture['users']['alice'],$plan);
        self::assertSame($id,$this->submit->submit($this->fixture['users']['alice'],$plan));
        self::assertSame('QUEUED',$this->state($id));self::assertSame($id,$this->activationWorker()->runOne());
        self::assertSame('ACTIVE',$this->state($id));self::assertCount(1,$this->gateway->requests);
        self::assertSame($this->fixture['tenants']['alice'],$this->gateway->requests[0]['tenant_id']);
        self::assertSame(1,(int)$this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind='candidate.hosting.activation.active'"));
        self::assertNull($this->activationWorker()->runOne());
    }
    public function testRevocationBeforeDispatchPreventsPhysicalRequest():void
    {
        $id=$this->submit->submit($this->fixture['users']['alice'],$this->plan());
        $this->store->db->delete('wcp_candidate_memberships',['user_id'=>$this->fixture['users']['alice']]);
        $this->activationWorker()->runOne();self::assertSame('FAILED',$this->state($id));self::assertSame([],$this->gateway->requests);
    }
    public function testRevisionMismatchPreventsPhysicalRequest():void
    {
        $id=$this->submit->submit($this->fixture['users']['alice'],$this->plan());
        $this->store->db->executeStatement('UPDATE wcp_candidate_sites SET revision=3');
        $this->activationWorker()->runOne();self::assertSame('FAILED',$this->state($id));self::assertSame([],$this->gateway->requests);
    }
    public function testLostResponseRequiresInspectionInsteadOfRetry():void
    {
        $id=$this->submit->submit($this->fixture['users']['alice'],$this->plan());$this->gateway->fail=true;
        $this->activationWorker()->runOne();self::assertSame('UNKNOWN_OUTCOME',$this->state($id));self::assertNull($this->activationWorker()->runOne());
        self::assertCount(1,$this->gateway->requests);$this->gateway->fail=false;
        self::assertSame(1,$this->activationWorker()->reconcile());self::assertSame('ACTIVE',$this->state($id));
        self::assertSame('inspect_php_fpm',$this->gateway->requests[1]['operation']);
        self::assertSame($this->gateway->requests[0]['job_id'],$this->gateway->requests[1]['job_id']);
    }
}
