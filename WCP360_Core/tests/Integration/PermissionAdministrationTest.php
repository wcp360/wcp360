<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class PermissionAdministrationTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    /** @var array{users: array<string, string>, tenants: array<string, string>} */
    private array $fixture;

    protected function setUp(): void
    {
        $this->bootBrowser('admin');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->context('user');
    }

    private function context(string $context): void
    {
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $context;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $context.'.localhost';
    }

    private function bootBrowser(string $context): void
    {
        self::ensureKernelShutdown();
        $this->context($context);
        $this->browser = self::createClient([], ['HTTP_HOST' => $context.'.localhost', 'HTTP_ORIGIN' => 'http://'.$context.'.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function primaryAdminLogin(): Crawler
    {
        $crawler = $this->browser->request('GET', 'http://admin.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form([
            '_username' => 'admin@example.test',
            '_password' => WorkbenchFixture::PASSWORD,
        ]));
        for ($i = 0; $i < 3 && $this->browser->getResponse()->isRedirect(); $i++) {
            $crawler = $this->browser->followRedirect();
        }
        self::assertResponseIsSuccessful();
        self::assertSame('/mfa', $this->browser->getRequest()->getPathInfo());

        return $crawler;
    }

    private function completeAdminLogin(): void
    {
        $crawler = $this->primaryAdminLogin();
        $crawler = $this->browser->submit($crawler->selectButton('Configurer la MFA')->form());
        self::assertResponseIsSuccessful();
        $secret = trim($crawler->filter('#mfa-secret')->text());
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => TOTP::createFromSecret($secret)->now()]));
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, '#recovery-codes code');
    }

    private function denialPath(?string $actor = null, ?string $tenant = null): string
    {
        return '/workbench/access/'.($actor ?? $this->fixture['users']['alice']).'/'.($tenant ?? $this->fixture['tenants']['alice']).'/denial';
    }

    /** @return array<string, string> */
    private function denialFields(bool $denied = true): array
    {
        $crawler = $this->browser->request('GET', 'http://admin.localhost/workbench/access');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Accès des utilisateurs');
        $form = $crawler->filter('form[action="'.$this->denialPath().'"]')->form();
        $fields = $form->getValues();
        self::assertIsString($fields['_token'] ?? null);

        return ['_token' => $fields['_token'], 'denied' => $denied ? '1' : '0'];
    }

    private function assertNoDenials(): void
    {
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_permission_denials'));
    }

    public function testPasswordOnlyAdministratorCannotReadOrChangeAccess(): void
    {
        $this->primaryAdminLogin();
        foreach (['GET', 'POST'] as $method) {
            $path = $method === 'GET' ? '/workbench/access' : $this->denialPath();
            $this->browser->request($method, 'http://admin.localhost'.$path, ['_token' => 'forged', 'denied' => '1']);
            self::assertContains($this->browser->getResponse()->getStatusCode(), [302, 303]);
            self::assertContains($this->browser->getResponse()->headers->get('Location'), ['/mfa', 'http://admin.localhost/mfa']);
        }
        $this->assertNoDenials();
    }

    public function testMfaAdministratorCanBlockAndUnblockAnExistingMembershipIdempotently(): void
    {
        $this->completeAdminLogin();
        $fields = $this->denialFields();
        self::assertSelectorExists('a[href="/workbench/access"]');
        self::assertSelectorCount(2, 'form[action$="/denial"]');
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        $this->assertNoDenials();
        $memberships = (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_memberships');
        $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), $fields);
        self::assertResponseRedirects('/workbench/access', 303);
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_permission_denials'));
        $authorization = self::getContainer()->get(CandidateAuthorization::class);
        self::assertFalse($authorization->allowsTenantPermission($this->fixture['users']['alice'], $this->fixture['tenants']['alice']));
        self::assertTrue($authorization->allowsTenantPermission($this->fixture['users']['bob'], $this->fixture['tenants']['bob']));
        $auditCount = (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit');
        $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), $fields);
        self::assertResponseRedirects('/workbench/access', 303);
        self::assertSame($auditCount, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), $this->denialFields(false));
        self::assertResponseRedirects('/workbench/access', 303);
        $this->assertNoDenials();
        self::assertTrue($authorization->allowsTenantPermission($this->fixture['users']['alice'], $this->fixture['tenants']['alice']));
        self::assertSame($memberships, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_memberships'));
    }

    public function testDeniedChangesRequireExactOriginAndCsrfBeforeAnyMutation(): void
    {
        $this->completeAdminLogin();
        $fields = $this->denialFields();
        $auditCount = (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit');
        $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), array_replace($fields, ['_token' => 'forged']));
        self::assertResponseStatusCodeSame(403);
        foreach (['https://foreign.example', ''] as $origin) {
            $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), $fields, [], ['HTTP_ORIGIN' => $origin]);
            self::assertResponseStatusCodeSame(403);
        }
        $this->assertNoDenials();
        self::assertSame($auditCount, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), $fields);
        self::assertResponseRedirects('/workbench/access', 303);
    }

    public function testAccessMutationRejectsPermissionInjectionAndInvalidStates(): void
    {
        $this->completeAdminLogin();
        $fields = $this->denialFields();
        foreach ([['permission' => '*'], ['command' => 'grant'], ['denied' => 'true'], ['denied' => '2'], ['denied' => ['1']]] as $extra) {
            $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), array_replace($fields, $extra));
            self::assertResponseStatusCodeSame(400);
        }
        $this->assertNoDenials();
    }

    public function testAdministratorCannotUseOneMembershipTokenForAnotherTarget(): void
    {
        $this->completeAdminLogin();
        $fields = $this->denialFields();
        $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath($this->fixture['users']['bob'], $this->fixture['tenants']['bob']), $fields);
        self::assertResponseStatusCodeSame(403);
        $this->assertNoDenials();
    }

    public function testValidCsrfCannotBlockAnAdministratorOrDeletedUserTarget(): void
    {
        $this->completeAdminLogin();
        $fields = $this->denialFields();
        $actor = $this->fixture['users']['alice'];
        $this->store->db->update('wcp_candidate_users', ['context' => 'admin'], ['id' => $actor]);
        $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), $fields);
        self::assertResponseStatusCodeSame(403);
        $this->assertNoDenials();
        $this->store->db->delete('wcp_candidate_memberships', ['user_id' => $actor]);
        $this->store->db->delete('wcp_candidate_users', ['id' => $actor]);
        $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), $fields);
        self::assertResponseStatusCodeSame(403);
        $this->assertNoDenials();
    }

    public function testExpiredFreshMfaProofCannotChangeAccess(): void
    {
        $this->completeAdminLogin();
        $fields = $this->denialFields();
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_mfa_proof');
        self::assertIsArray($proof);
        $proof['at'] = time() - 301;
        $session->set('_wcp_mfa_proof', $proof);
        $session->save();
        $this->browser->request('POST', 'http://admin.localhost'.$this->denialPath(), $fields);
        self::assertResponseStatusCodeSame(403);
        $this->assertNoDenials();
        $this->browser->request('GET', 'http://admin.localhost/workbench/access');
        self::assertResponseIsSuccessful();
    }

    public function testUserCannotReadOrChangeAdministratorAccessSurface(): void
    {
        $this->bootBrowser('user');
        $this->loginUser();
        $this->browser->request('GET', 'http://user.localhost/workbench/access');
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST', 'http://user.localhost'.$this->denialPath(), ['_token' => 'forged', 'denied' => '1']);
        self::assertResponseStatusCodeSame(403);
        $this->assertNoDenials();
    }

    private function loginUser(): Crawler
    {
        $crawler = $this->browser->request('GET', 'http://user.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'alice@example.test', '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');

        return $this->browser->followRedirect();
    }

    public function testBlockIsEnforcedForExistingUserSessionHttpApiAndQueuedWorker(): void
    {
        $this->bootBrowser('user');
        $crawler = $this->loginUser();
        self::assertResponseIsSuccessful();
        $form = $crawler->filter('form[action="/workbench/plans"]')->form(['hostname' => 'blocked-form.test']);
        $fields = $form->getValues();
        self::assertIsString($fields['_token'] ?? null);
        $job = self::getContainer()->get(SubmitHostingPlan::class)->submit($this->fixture['users']['alice'], [
            'tenant_id' => $this->fixture['tenants']['alice'],
            'hostname' => 'queued-before-denial.test',
            'idempotency_key' => 'permission-revocation-000001',
        ]);
        $administration = self::getContainer()->get(CandidatePermissionAdministration::class);
        self::assertTrue($administration->setDenied($this->fixture['users']['admin'], $this->fixture['users']['alice'], $this->fixture['tenants']['alice'], true));
        $this->browser->request('GET', 'http://user.localhost/');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('select[name="tenant_id"]');
        self::assertSelectorNotExists('a[href="/workbench/access"]');
        $this->browser->request('GET', 'http://user.localhost/?tenant='.$this->fixture['tenants']['alice']);
        self::assertResponseStatusCodeSame(403);
        $this->browser->submit($form);
        self::assertResponseStatusCodeSame(403);
        $this->browser->jsonRequest('POST', 'http://user.localhost/workbench/v1/plans', [
            'tenant_id' => $this->fixture['tenants']['alice'],
            'hostname' => 'blocked-api.test',
            'idempotency_key' => 'blocked-api-request-000001',
        ], ['HTTP_ORIGIN' => 'http://user.localhost', 'HTTP_X_CSRF_TOKEN' => $fields['_token']]);
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('GET', 'http://user.localhost/workbench/jobs/'.$job['job_id']);
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('GET', 'http://user.localhost/workbench/v1/summary/'.$this->fixture['tenants']['alice']);
        self::assertResponseStatusCodeSame(403);
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
        self::assertSame($job['job_id'], self::getContainer()->get(RunHostingPlan::class)->runOne());
        self::assertSame('FAILED', $this->store->db->fetchOne('SELECT state FROM wcp_candidate_jobs WHERE id = ?', [$job['job_id']]));
        self::assertNull($this->store->db->fetchOne('SELECT result FROM wcp_candidate_jobs WHERE id = ?', [$job['job_id']]));
        self::assertSame('PLAN_FAILED', $this->store->db->fetchOne('SELECT state FROM wcp_candidate_sites'));
    }

    public function testRemovingADenialAfterMembershipRevocationNeverGrantsAccess(): void
    {
        $this->bootBrowser('user');
        $this->loginUser();
        $administration = self::getContainer()->get(CandidatePermissionAdministration::class);
        $admin = $this->fixture['users']['admin'];
        $alice = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        self::assertTrue($administration->setDenied($admin, $alice, $tenant, true));
        $this->store->db->delete('wcp_candidate_memberships', ['user_id' => $alice, 'tenant_id' => $tenant]);
        self::assertTrue($administration->setDenied($admin, $alice, $tenant, false));
        $this->assertNoDenials();
        self::assertFalse(self::getContainer()->get(CandidateAuthorization::class)->allowsTenantPermission($alice, $tenant));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_memberships WHERE user_id = ? AND tenant_id = ?', [$alice, $tenant]));
        $this->browser->request('GET', 'http://user.localhost/');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('select[name="tenant_id"]');
        $this->browser->request('GET', 'http://user.localhost/?tenant='.$tenant);
        self::assertResponseStatusCodeSame(403);
    }
}
