<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Application\CompletePasswordRecovery;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Setup\Application\IssuePasswordRecoveryCode;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateAccountMaintenance;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class PasswordRecoveryJourneyTest extends WebTestCase
{
    private const KEY = 'cccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccccc';
    private const NEXT = 'synthetic-browser-recovery-password-2026';
    private static int $client = 40;
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private string $surface='user';

    protected function setUp(): void { $this->bootSurface('user'); }
    protected function tearDown(): void
    {
        parent::tearDown();
        $_ENV['WCP_CONTEXT']=$_SERVER['WCP_CONTEXT']='user';
        $_ENV['WCP_PUBLIC_HOST']=$_SERVER['WCP_PUBLIC_HOST']='user.localhost';
    }
    private function bootSurface(string $surface, string $mode = 'shared'): void
    {
        self::ensureKernelShutdown();
        $this->surface=$surface;
        $_ENV['WCP_CONTEXT']=$_SERVER['WCP_CONTEXT']=$surface;
        $_ENV['WCP_PUBLIC_HOST']=$_SERVER['WCP_PUBLIC_HOST']=$surface.'.localhost';
        $this->browser=self::createClient([],['HTTP_HOST'=>$surface.'.localhost','HTTP_ORIGIN'=>'http://'.$surface.'.localhost',
            'REMOTE_ADDR'=>'192.0.2.'.(++self::$client)]);
        $this->browser->disableReboot();
        $this->store=self::getContainer()->get(WorkbenchStore::class);
        $this->fixture=WorkbenchFixture::reset($this->store->db);
        $this->installRecovery($mode);
    }
    private function installRecovery(string $mode='shared',bool $enabled=true): void
    {
        self::getContainer()->set(CompletePasswordRecovery::class,new CompletePasswordRecovery($this->store,new CandidateSessions($this->store),
            self::getContainer()->get(UserPasswordHasherInterface::class),new RuntimeContext($this->surface,true,'test',$this->surface.'.localhost'),
            $mode,new \Wcp360\Core\Tests\Fixtures\RecoveryKeysFixture(self::KEY),$enabled));
    }
    private function code(string $name='alice'): string
    {
        $setup=new RuntimeContext('setup',true,'test','localhost');
        $issuer=new IssuePasswordRecoveryCode($this->store,new CandidateAccountMaintenance($this->store,$setup),$setup,'shared',new \Wcp360\Core\Tests\Fixtures\RecoveryKeysFixture(self::KEY),true);
        return $issuer->issue($this->fixture['users'][$name],$name,$this->surface);
    }
    private function url(string $path): string { return 'http://'.$this->surface.'.localhost'.$path; }
    private function fields(string $code): array
    {
        $crawler=$this->browser->request('GET',$this->url('/reset-password'));
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.auth-brand img');
        $fields=$crawler->selectButton('Réinitialiser le mot de passe')->form()->getValues();
        return array_replace($fields,['recovery_code'=>$code,'new_password'=>self::NEXT,'repeat_password'=>self::NEXT]);
    }

    public function testAnonymousRecoveryEndsOldSessionsRejectsReplayAndRequiresNormalLogin(): void
    {
        $sessions=new CandidateSessions($this->store);
        $id=$this->fixture['users']['alice'];
        $selector=$sessions->issue($id,'user',str_repeat('s',32));
        $code=$this->code();
        $this->browser->request('GET',$this->url('/login'));
        self::assertSelectorExists('a[href="/reset-password"]');
        $this->browser->request('POST',$this->url('/reset-password'),$this->fields($code));
        self::assertResponseRedirects('/login',303);
        self::assertFalse($sessions->current($id,'user',$selector,str_repeat('s',32)));
        $this->browser->request('GET',$this->url('/'));
        self::assertResponseRedirects('/login');
        $this->browser->request('POST',$this->url('/reset-password'),$this->fields($code));
        self::assertResponseStatusCodeSame(422);
        self::assertStringNotContainsString($code,$this->browser->getResponse()->getContent());
        self::assertSelectorNotExists('input[name="recovery_code"][value]');
        $crawler=$this->browser->request('GET',$this->url('/login'));
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username'=>'alice','_password'=>self::NEXT]));
        self::assertResponseRedirects('/');
        $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testOriginCsrfUnexpectedFieldsAndCredentialEchoAreRefused(): void
    {
        $code=$this->code();$fields=$this->fields($code);
        foreach (['','http://other.example'] as $origin) {
            $this->browser->request('POST',$this->url('/reset-password'),$fields,[],['HTTP_ORIGIN'=>$origin]);
            self::assertResponseStatusCodeSame(403);
        }
        $this->browser->request('POST',$this->url('/reset-password'),array_replace($fields,['_token'=>'wrong']));
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST',$this->url('/reset-password'),$fields+['target_id'=>$this->fixture['users']['bob']]);
        self::assertResponseStatusCodeSame(400);
        $this->browser->request('POST',$this->url('/reset-password'),array_replace($fields,['recovery_code'=>['bad']]));
        self::assertResponseStatusCodeSame(400);
        $this->browser->request('POST',$this->url('/reset-password'),array_replace($fields,['recovery_code'=>'<script>private-code-marker</script>']));
        self::assertResponseStatusCodeSame(422);
        foreach ([$code,self::NEXT,'<script>private-code-marker</script>'] as $secret) {
            self::assertStringNotContainsString($secret,$this->browser->getResponse()->getContent());
        }
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD,$this->store->db->fetchOne('SELECT password_hash FROM wcp_candidate_users WHERE id=?',[$this->fixture['users']['alice']])));
    }

    public function testInvalidAttemptsAreLimitedAndResponsesNeverCacheSecrets(): void
    {
        $fields=$this->fields('invalid-code');
        for($i=0;$i<5;$i++){
            $this->browser->request('POST',$this->url('/reset-password'),$fields);
            self::assertResponseStatusCodeSame(422);
            self::assertStringContainsString('no-store',$this->browser->getResponse()->headers->get('Cache-Control'));
        }
        $this->browser->request('POST',$this->url('/reset-password'),$fields);
        self::assertResponseStatusCodeSame(429);
        self::assertGreaterThan(0,(int)$this->browser->getResponse()->headers->get('Retry-After'));
    }

    public function testAdministratorRecoveryStillRequiresItsEnabledFactor(): void
    {
        $this->bootSurface('admin');
        $id=$this->fixture['users']['admin'];
        $mfa=self::getContainer()->get(CandidateMfa::class);
        $enrollment=$mfa->startEnrollment($id);
        self::assertNotNull($mfa->check($id,TOTP::createFromSecret($enrollment['secret'])->now(),1));
        $before=$this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery WHERE user_id=? ORDER BY code_hash',[$id]);
        $this->browser->request('POST',$this->url('/reset-password'),$this->fields($this->code('admin')));
        self::assertResponseRedirects('/login',303);
        $crawler=$this->browser->followRedirect();
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username'=>'admin','_password'=>self::NEXT]));
        self::assertResponseRedirects('/');
        $this->browser->followRedirect();
        self::assertContains($this->browser->getResponse()->getStatusCode(), [302, 303]);
        self::assertContains($this->browser->getResponse()->headers->get('Location'), ['/mfa', $this->url('/mfa')]);
        $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('input[name="_auth_code"]');
        self::assertTrue($mfa->status($id)['enabled']);
        self::assertSame($before,$this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_mfa_recovery WHERE user_id=? ORDER BY code_hash',[$id]));
    }

    public function testUnavailableProfilesHaveNoFormOrPrivilegedFallback(): void
    {
        $this->bootSurface('user', 'session-isolated');
        $this->browser->request('GET',$this->url('/reset-password'));
        self::assertResponseStatusCodeSame(503);
        self::assertSelectorNotExists('form.auth-form');
        $this->browser->request('POST',$this->url('/reset-password'),['recovery_code'=>'invalid']);
        self::assertResponseStatusCodeSame(503);
        $this->browser->request('GET',$this->url('/login'));
        self::assertSelectorNotExists('a[href="/reset-password"]');
    }

    public function testCodesAreNotAcceptedInUrlsAndBeforeControllerErrorsAreRedacted(): void
    {
        $code=$this->code();$fields=$this->fields($code);
        $this->browser->request('GET',$this->url('/reset-password').'?recovery_code='.rawurlencode($code));
        self::assertResponseStatusCodeSame(400);
        self::assertStringNotContainsString($code,$this->browser->getResponse()->getContent());
        $this->browser->request('POST','http://outside.example/reset-password',$fields);
        self::assertSame('outside.example', $this->browser->getRequest()->headers->get('Host'));
        // Symfony may already have rejected an untrusted host; getHost() then returns an empty string.
        self::assertContains($this->browser->getResponse()->getStatusCode(),[400,403]);
        foreach([$code,self::NEXT] as $secret)self::assertStringNotContainsString($secret,$this->browser->getResponse()->getContent());
        self::assertTrue(password_verify(WorkbenchFixture::PASSWORD, $this->store->db->fetchOne(
            'SELECT password_hash FROM wcp_candidate_users WHERE id=?', [$this->fixture['users']['alice']])));
    }
}
