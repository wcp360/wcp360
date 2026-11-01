<?php

declare(strict_types=1);
namespace Wcp360\Core\Tests\Integration;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
final class WorkbenchTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    protected function setUp():void
    {
        $this->browser=self::createClient([],['HTTP_HOST'=>'user.localhost']);$this->browser->disableReboot();
        $this->store=self::getContainer()->get(WorkbenchStore::class);
        $this->fixture=WorkbenchFixture::reset($this->store->db);
    }
    private function submit(array $override=[]):array{return self::getContainer()->get(SubmitHostingPlan::class)->submit($this->fixture['users']['alice'],array_replace(['tenant_id'=>$this->fixture['tenants']['alice'],'hostname'=>'example.test','idempotency_key'=>'test-key-00000001'],$override));}
    private function login(string $name='alice'):void
    {
        $crawler=$this->browser->request('GET','http://user.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username'=>$name.'@example.test','_password'=>WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
    }
    public function testHealthAndSecurityHeaders():void{$this->browser->request('GET','http://user.localhost/health');self::assertResponseIsSuccessful();self::assertResponseHeaderSame('X-Content-Type-Options','nosniff');self::assertResponseHeaderSame('X-WCP360-Status','development-candidate');self::assertStringContainsString("frame-ancestors 'none'",$this->browser->getResponse()->headers->get('Content-Security-Policy'));}
    public function testWrongRuntimeHostIsDenied():void{$this->browser->request('GET','http://admin.localhost/health');self::assertResponseStatusCodeSame(403);}
    public function testAnonymousDashboardRequiresLogin():void{$this->browser->request('GET','http://user.localhost/');self::assertResponseRedirects('/login');}
    public function testActualLoginFormAndDashboard():void
    {
        $crawler=$this->browser->request('GET','http://user.localhost/login');self::assertResponseIsSuccessful();
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username'=>'alice@example.test','_password'=>WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');$this->browser->followRedirect();self::assertResponseIsSuccessful();self::assertSelectorTextContains('h1','Tableau de bord');self::assertSelectorCount(1,'select[name="tenant_id"] option');
        self::assertStringNotContainsString('bob@example.test',$this->browser->getResponse()->getContent());
    }
    public function testLoginRejectsForgedCsrf():void{$this->browser->request('POST','http://user.localhost/login',['_username'=>'alice@example.test','_password'=>WorkbenchFixture::PASSWORD,'_csrf_token'=>'forged']);self::assertResponseRedirects('/login');$this->browser->request('GET','http://user.localhost/');self::assertResponseRedirects('/login');}
    public function testPlanSubmissionIsDurableAtomicAndIdempotent():void
    {
        $first=$this->submit();$second=$this->submit();self::assertSame($first['job_id'],$second['job_id']);self::assertTrue($second['replayed']);
        self::assertSame(1,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));self::assertSame(1,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        self::assertSame('QUEUED',$this->store->job($this->fixture['users']['alice'],$first['job_id'])['state']);
    }
    public function testSameKeyWithDifferentPayloadConflicts():void{$this->submit();$this->expectException(ConflictHttpException::class);$this->submit(['hostname'=>'other.test']);}
    public function testCrossTenantSubmissionDoesNotCreateAnything():void
    {
        try{$this->submit(['tenant_id'=>$this->fixture['tenants']['bob']]);self::fail('Cross-tenant submission accepted');}catch(AccessDeniedHttpException){}
        self::assertSame(0,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
    }
    public function testUnknownRequestFieldIsRejected():void{$this->expectException(BadRequestHttpException::class);$this->submit(['command'=>'rm -rf /']);}
    public function testNginxInjectionIsRejected():void{$this->expectException(BadRequestHttpException::class);$this->submit(['hostname'=>'example.test; include /etc/passwd;']);}
    public function testForeignKeyRejectsCrossTenantJobEvenWithoutRepository():void
    {
        $this->submit();$site=$this->store->db->fetchOne('SELECT id FROM wcp_candidate_sites');
        $this->expectException(\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException::class);
        $this->store->db->insert('wcp_candidate_jobs',['id'=>WorkbenchStore::id(),'tenant_id'=>$this->fixture['tenants']['bob'],'site_id'=>$site,'actor_id'=>$this->fixture['users']['bob'],'idempotency_key'=>'cross-tenant-00001','fingerprint'=>str_repeat('a',64),'state'=>'QUEUED','created_at'=>WorkbenchStore::now()]);
    }
    public function testFormRejectsForgedCsrf():void{$this->login();$this->browser->request('POST','http://user.localhost/workbench/plans',['_token'=>'forged']);self::assertResponseStatusCodeSame(403);}
    public function testActualHostingFormCreatesJobAndEscapesResultPage():void
    {
        $this->login();$crawler=$this->browser->request('GET','http://user.localhost/');
        $this->browser->submit($crawler->selectButton('Générer le plan')->form(['hostname'=>'form.test']));self::assertResponseStatusCodeSame(303);$this->browser->followRedirect();self::assertResponseIsSuccessful();self::assertSelectorTextContains('h1','Suivi de tâche');self::assertSelectorTextContains('.status','QUEUED');
    }
    public function testApiRejectsMissingOriginAndCsrf():void{$this->login();$this->browser->jsonRequest('POST','http://user.localhost/workbench/v1/plans',['tenant_id'=>$this->fixture['tenants']['alice'],'hostname'=>'api.test','idempotency_key'=>'api-key-000000001']);self::assertResponseStatusCodeSame(403);}
    public function testOtherTenantCannotReadJob():void
    {
        $job=$this->submit();$this->login('bob');$this->browser->request('GET','http://user.localhost/workbench/jobs/'.$job['job_id']);self::assertResponseStatusCodeSame(403);
    }
    public function testWorkerRechecksRevokedPermission():void
    {
        $job=$this->submit();$this->store->db->delete('wcp_candidate_memberships',['user_id'=>$this->fixture['users']['alice']]);
        self::getContainer()->get(RunHostingPlan::class)->runOne();self::assertSame('FAILED',$this->store->db->fetchOne('SELECT state FROM wcp_candidate_jobs WHERE id=?',[$job['job_id']]));
    }
    public function testWorkerProducesAPlanButNeverActivatesASite():void
    {
        $job=$this->submit();self::getContainer()->get(RunHostingPlan::class)->runOne();$row=$this->store->job($this->fixture['users']['alice'],$job['job_id']);self::assertSame('SUCCEEDED',$row['state']);self::assertStringContainsString('server_name example.test;',json_decode($row['result'],true)['nginx']);self::assertSame('PLAN_READY',$this->store->db->fetchOne('SELECT state FROM wcp_candidate_sites'));self::assertNull(self::getContainer()->get(RunHostingPlan::class)->runOne());
    }
    public function testInterruptedJobIsNotBlindlyReplayed():void
    {
        $job=$this->submit();$this->store->db->update('wcp_candidate_jobs',['state'=>'RUNNING'],['id'=>$job['job_id']]);$worker=self::getContainer()->get(RunHostingPlan::class);self::assertSame(1,$worker->markInterrupted());self::assertNull($worker->runOne());self::assertSame('UNKNOWN_OUTCOME',$this->store->job($this->fixture['users']['alice'],$job['job_id'])['state']);
    }
    public function testDisablingAccountRevokesSession():void
    {
        $this->login();$this->store->db->update('wcp_candidate_users',['active'=>0],['id'=>$this->fixture['users']['alice']]);$this->browser->request('GET','http://user.localhost/');self::assertResponseRedirects('/login');
    }
    public function testAuditOccurrencesCannotBeUpdated():void
    {
        $this->submit();
        try{$this->store->db->executeStatement("UPDATE wcp_candidate_audit SET kind='tampered'");self::fail('Mutable audit accepted');}catch(\Doctrine\DBAL\Exception){}
        self::assertSame('candidate.hosting.plan.queued',$this->store->db->fetchOne('SELECT kind FROM wcp_candidate_audit'));
    }
    public function testAuditFailureRollsBackTheEntireSubmission():void
    {
        $db=$this->store->db;
        if($db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform){$db->executeStatement("CREATE TRIGGER reject_audit BEFORE INSERT ON wcp_candidate_audit BEGIN SELECT RAISE(ABORT,'audit unavailable'); END");}
        else{$db->executeStatement("CREATE OR REPLACE FUNCTION wcp_candidate_reject_audit() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN RAISE EXCEPTION ''audit unavailable''; END;'");$db->executeStatement('CREATE TRIGGER reject_audit BEFORE INSERT ON wcp_candidate_audit FOR EACH ROW EXECUTE FUNCTION wcp_candidate_reject_audit()');}
        try{$this->submit();self::fail('Submission without audit accepted');}catch(\Doctrine\DBAL\Exception){}
        self::assertSame(0,(int)$db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));self::assertSame(0,(int)$db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_sites'));
    }
    public function testActivationFormRequiresExplicitGrantAndCsrf():void
    {
        $job=$this->submit();self::getContainer()->get(RunHostingPlan::class)->runOne();
        self::getContainer()->set(\Wcp360\Core\Module\Hosting\Application\SubmitActivation::class,new \Wcp360\Core\Module\Hosting\Application\SubmitActivation($this->store,true));
        $this->login();
        $this->browser->request('GET','http://user.localhost/workbench/jobs/'.$job['job_id']);
        self::assertSelectorNotExists('form[action$="/activate"]');
        $this->store->db->insert('wcp_candidate_activation_grants',['user_id'=>$this->fixture['users']['alice'],'tenant_id'=>$this->fixture['tenants']['alice']]);
        $crawler=$this->browser->request('GET','http://user.localhost/workbench/jobs/'.$job['job_id']);
        $form=$crawler->selectButton('Activer dans le conteneur d’essai')->form();
        $this->browser->submit($form);self::assertResponseStatusCodeSame(303);
        self::assertSame('QUEUED',$this->store->db->fetchOne('SELECT state FROM wcp_candidate_activations'));
        $this->browser->submit($form);self::assertResponseStatusCodeSame(303);
        self::assertSame(1,(int)$this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_activations'));
        $this->browser->request('POST','http://user.localhost/workbench/jobs/'.$job['job_id'].'/activate',['_token'=>'forged']);
        self::assertResponseStatusCodeSame(403);
    }
    public function testRenderedImportmapNonceMatchesCsp():void
    {
        $crawler=$this->browser->request('GET','http://user.localhost/login');
        $nonce=$crawler->filter('script[type="importmap"]')->attr('nonce');
        self::assertMatchesRegularExpression('/^[A-Za-z0-9+\/=]{24}$/',$nonce);
        $policy=$this->browser->getResponse()->headers->get('Content-Security-Policy');
        self::assertStringContainsString("'nonce-".$nonce."'",$policy);
        self::assertSame($nonce,$crawler->filter('meta[name="csp-nonce"]')->attr('content'));
        self::assertStringContainsString("script-src 'self' 'nonce-".$nonce."'",$policy);
        self::assertStringContainsString("style-src 'self' 'nonce-".$nonce."'",$policy);
        self::assertStringNotContainsString("'unsafe-inline'",$policy);
        $next=$this->browser->request('GET','http://user.localhost/login');
        self::assertNotSame($nonce,$next->filter('meta[name="csp-nonce"]')->attr('content'));
    }
    public function testSessionCannotRefreshIntoAnotherContext():void
    {
        $provider=self::getContainer()->get(\Wcp360\Core\Module\IdentityAccess\Infrastructure\PrincipalProvider::class);
        $this->expectException(\Symfony\Component\Security\Core\Exception\UserNotFoundException::class);
        $provider->refreshUser(new Principal($this->fixture['users']['alice'],'alice@example.test','unused','admin'));
    }
}
