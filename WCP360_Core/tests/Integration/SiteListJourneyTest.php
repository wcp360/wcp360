<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\HostingJobsFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class SiteListJourneyTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        $this->browser = self::createClient([], ['HTTP_HOST' => 'user.localhost', 'HTTP_ORIGIN' => 'http://user.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        HostingJobsFixture::seed($this->store->db, $this->fixture, 60);
        HostingJobsFixture::seed($this->store->db, $this->fixture, 1, 'bob', 10000);
    }

    private function login(): void
    {
        $crawler = $this->browser->request('GET', 'http://user.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'alice', '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
        $this->browser->followRedirect();
    }

    private function url(array $query): string
    {
        return 'http://user.localhost/workbench/v1/sites?'.http_build_query($query);
    }

    public function testAnonymousInventoryRequiresTheExistingUserLogin(): void
    {
        $this->browser->request('GET', 'http://user.localhost/workbench/sites');
        self::assertResponseRedirects('/login');
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice']]));
        self::assertResponseStatusCodeSame(401);
        self::assertSame('AUTHENTICATION_REQUIRED', json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR)['error']['code']);
    }

    public function testNavigationFiltersSharedPaginationAndTaskLinkUseTheWcpLayout(): void
    {
        $this->store->db->executeStatement("UPDATE wcp_candidate_sites SET application_server='frankenphp_worker',worker_acknowledged=1,web_stack='advanced' WHERE hostname='job-alice-2.test'");
        $this->login();
        self::assertSelectorExists('a[href="/workbench/sites"]');
        $crawler = $this->browser->request('GET', 'http://user.localhost/workbench/sites');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h1', 'Mes hébergements');
        self::assertSelectorExists('a[aria-current="page"][href="/workbench/sites"]');
        self::assertSelectorCount(25, 'tr[data-site-id]');
        self::assertSelectorNotExists('a[rel="prev"]');
        $crawler = $this->browser->click($crawler->filter('a[rel="next"]')->link());
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(25, 'tr[data-site-id]');
        self::assertSelectorExists('a[rel="prev"]');
        $crawler = $this->browser->submit($crawler->selectButton('Filtrer')->form([
            'q' => 'job-alice-2.test', 'state' => 'PLAN_READY', 'server' => 'frankenphp_worker']));
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'tr[data-site-id]');
        self::assertSelectorTextContains('tbody', 'FrankenPHP Worker');
        self::assertSelectorTextContains('.field-help', 'sans caractère générique');
        self::assertStringContainsString('La mise en service du site est une étape distincte', $this->browser->getResponse()->getContent());
        self::assertSelectorNotExists('input[name="after"]');
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        $this->browser->click($crawler->filter('tr[data-site-id] a')->link());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h1', 'Mes tâches');
        self::assertSelectorCount(1, 'tr[data-job-id]');
        self::assertInputValueSame('q', 'job-alice-2.test');
    }

    public function testApiReturnsOnlyBoundedPreparedSiteMetadata(): void
    {
        $this->login();
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice']]));
        self::assertResponseIsSuccessful();
        $page = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(['items', 'pagination'], array_keys($page));
        self::assertCount(25, $page['items']);
        self::assertSame(['id', 'hostname', 'state', 'application_server', 'revision'], array_keys($page['items'][0]));
        self::assertIsInt($page['items'][0]['revision']);
        foreach (['tenant_id', 'worker_acknowledged', 'private_result_marker', 'fingerprint', 'idempotency_key', 'request_id', 'job-bob'] as $private) {
            self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
        }
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice'], 'after' => $page['pagination']['next']]));
        self::assertResponseIsSuccessful();
        $next = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame([], array_intersect(array_column($page['items'], 'id'), array_column($next['items'], 'id')));
        $this->browser->request('GET', 'http://user.localhost/workbench/v1/sites');
        self::assertResponseStatusCodeSame(400);
        $this->browser->request('POST', $this->url(['tenant' => $this->fixture['tenants']['alice']]));
        self::assertResponseStatusCodeSame(405);
    }

    public function testForeignScopeMalformedQueryAndRevokedGrantDoNotExposeSites(): void
    {
        $this->login();
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['bob']]));
        self::assertResponseStatusCodeSame(403);
        $foreign = $this->store->db->fetchOne("SELECT id FROM wcp_candidate_sites WHERE hostname='job-bob-10001.test'");
        foreach ([['server' => ['php_fpm']], ['q' => '<script>private-site-query</script>'], ['limit' => '10001'],
            ['after' => $foreign], ['state' => 'SUCCEEDED'], ['actor_id' => $this->fixture['users']['bob']]] as $patch) {
            $this->browser->request('GET', $this->url(array_replace(['tenant' => $this->fixture['tenants']['alice']], $patch)));
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-site-query', $this->browser->getResponse()->getContent());
        }
        self::getContainer()->get(CandidatePermissionAdministration::class)->setDenied(
            $this->fixture['users']['admin'], $this->fixture['users']['alice'], $this->fixture['tenants']['alice'], true);
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice']]));
        self::assertResponseStatusCodeSame(403);
        self::assertStringNotContainsString('job-alice', $this->browser->getResponse()->getContent());
    }

    public function testNoMembershipHasAnEmptyBrowserViewWithoutAnotherTenantFallback(): void
    {
        $this->store->db->delete('wcp_candidate_memberships', ['user_id' => $this->fixture['users']['alice']]);
        $this->login();
        $this->browser->request('GET', 'http://user.localhost/workbench/sites');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h2', 'Aucun espace accessible');
        self::assertSelectorNotExists('tr[data-site-id]');
        self::assertStringNotContainsString('job-bob', $this->browser->getResponse()->getContent());
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice']]));
        self::assertResponseStatusCodeSame(403);
    }

    public function testStoredMarkupIsEscapedAndListingDoesNotMutateBusinessState(): void
    {
        $this->login();
        $this->store->db->executeStatement("UPDATE wcp_candidate_sites SET hostname='<svg onload=private-site-marker>' WHERE hostname='job-alice-1.test'");
        $sites = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_sites ORDER BY id');
        $jobs = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id');
        $audit = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        $this->browser->request('GET', 'http://user.localhost/workbench/sites?limit=100');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('&lt;svg', $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString('<svg onload=private-site-marker>', $this->browser->getResponse()->getContent());
        self::assertSame($sites, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_sites ORDER BY id'));
        self::assertSame($jobs, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id'));
        self::assertSame($audit, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testAdminRoleCannotUseTheUserInventoryOnTheAdminSurface(): void
    {
        self::ensureKernelShutdown();
        $saved = [];
        foreach (['WCP_CONTEXT' => 'admin', 'WCP_PUBLIC_HOST' => 'admin.localhost'] as $name => $value) {
            $saved[$name] = [$_ENV[$name] ?? null, $_SERVER[$name] ?? null];
            $_ENV[$name] = $_SERVER[$name] = $value;
        }
        try {
            $this->browser = self::createClient([], ['HTTP_HOST' => 'admin.localhost', 'HTTP_ORIGIN' => 'http://admin.localhost']);
            $this->browser->disableReboot();
            $this->store = self::getContainer()->get(WorkbenchStore::class);
            $this->fixture = WorkbenchFixture::reset($this->store->db);
            $crawler = $this->browser->request('GET', 'http://admin.localhost/login');
            $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'admin', '_password' => WorkbenchFixture::PASSWORD]));
            $this->browser->request('GET', 'http://admin.localhost/workbench/sites');
            self::assertResponseStatusCodeSame(403);
            $this->browser->request('GET', 'http://admin.localhost/workbench/v1/sites?tenant='.$this->fixture['tenants']['alice']);
            self::assertResponseStatusCodeSame(403);
        } finally {
            self::ensureKernelShutdown();
            foreach ($saved as $name => [$env, $server]) {
                if ($env === null) unset($_ENV[$name]); else $_ENV[$name] = $env;
                if ($server === null) unset($_SERVER[$name]); else $_SERVER[$name] = $server;
            }
        }
    }
}
