<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\HostingJobsFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class JobListJourneyTest extends WebTestCase
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
        return 'http://user.localhost/workbench/v1/jobs?'.http_build_query($query);
    }

    public function testAnonymousRequestsKeepBrowserLoginAndApiJsonRefusal(): void
    {
        $this->browser->request('GET', 'http://user.localhost/workbench/jobs');
        self::assertResponseRedirects('/login');
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice']]));
        self::assertResponseStatusCodeSame(401);
        self::assertSame('AUTHENTICATION_REQUIRED', json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR)['error']['code']);
    }

    public function testBrowserFiltersPaginationAndNavigationUseTheOfficialLayout(): void
    {
        $this->login();
        self::assertSelectorExists('a[href="/workbench/jobs"]');
        $crawler = $this->browser->request('GET', 'http://user.localhost/workbench/jobs');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h1', 'Mes tâches');
        self::assertSelectorExists('a[aria-current="page"][href="/workbench/jobs"]');
        self::assertSelectorCount(25, 'tr[data-job-id]');
        self::assertSelectorNotExists('a[rel="prev"]');
        $first = $crawler->filter('tr[data-job-id]')->each(static fn ($node) => $node->attr('data-job-id'));
        $crawler = $this->browser->click($crawler->filter('a[rel="next"]')->link());
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(25, 'tr[data-job-id]');
        self::assertNotSame($first, $crawler->filter('tr[data-job-id]')->each(static fn ($node) => $node->attr('data-job-id')));
        self::assertSelectorExists('a[rel="prev"]');
        $crawler = $this->browser->submit($crawler->selectButton('Filtrer')->form(['q' => 'job-alice-1', 'state' => 'SUCCEEDED']));
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(5, 'tr[data-job-id]');
        self::assertSelectorNotExists('input[name="after"]');
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
    }

    public function testApiHasBoundedProjectionCursorAndNoPrivateResultOrOtherTenant(): void
    {
        $this->login();
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice']]));
        self::assertResponseIsSuccessful();
        $page = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(['items', 'pagination'], array_keys($page));
        self::assertCount(25, $page['items']);
        self::assertNotNull($page['pagination']['next']);
        foreach (['private_result_marker', 'fingerprint', 'idempotency_key', 'actor_id', 'job-bob'] as $private) {
            self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
        }
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice'], 'after' => $page['pagination']['next']]));
        self::assertResponseIsSuccessful();
        $next = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame([], array_intersect(array_column($page['items'], 'id'), array_column($next['items'], 'id')));
    }

    public function testForeignScopeRevocationAndMalformedFiltersFailWithoutReflectingInput(): void
    {
        $this->login();
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['bob']]));
        self::assertResponseStatusCodeSame(403);
        foreach ([['q' => '<script>private-job-filter</script>'], ['limit' => '10000'], ['state' => ['RUNNING']],
            ['after' => '00000000-0000-7000-8000-000000010001'], ['actor_id' => $this->fixture['users']['bob']]] as $patch) {
            $this->browser->request('GET', $this->url(array_replace(['tenant' => $this->fixture['tenants']['alice']], $patch)));
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-job-filter', $this->browser->getResponse()->getContent());
        }
        self::getContainer()->get(CandidatePermissionAdministration::class)->setDenied($this->fixture['users']['admin'],
            $this->fixture['users']['alice'], $this->fixture['tenants']['alice'], true);
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice']]));
        self::assertResponseStatusCodeSame(403);
        self::assertStringNotContainsString('job-alice', $this->browser->getResponse()->getContent());
    }

    public function testNoMembershipShowsAnEmptyWorkspaceAndNeverFallsBackToAnotherTenant(): void
    {
        $this->store->db->delete('wcp_candidate_memberships', ['user_id' => $this->fixture['users']['alice']]);
        $this->login();
        $this->browser->request('GET', 'http://user.localhost/workbench/jobs');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h2', 'Aucun espace accessible');
        self::assertSelectorNotExists('tr[data-job-id]');
        self::assertStringNotContainsString('job-bob', $this->browser->getResponse()->getContent());
        $this->browser->request('GET', $this->url(['tenant' => $this->fixture['tenants']['alice']]));
        self::assertResponseStatusCodeSame(403);
    }

    public function testStoredDomainMarkupIsEscapedAndReadsDoNotWriteBusinessState(): void
    {
        $this->login();
        $this->store->db->executeStatement("UPDATE wcp_candidate_sites SET hostname='<img src=x onerror=private-marker>' WHERE id=(SELECT site_id FROM wcp_candidate_jobs WHERE id='00000000-0000-7000-8000-000000000060')");
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id');
        $audit = (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit');
        $this->browser->request('GET', 'http://user.localhost/workbench/jobs');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('&lt;img', $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString('<img src=x onerror=private-marker>', $this->browser->getResponse()->getContent());
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id'));
        self::assertSame($audit, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
    }

    public function testAdministratorRoleDoesNotProvideUserInventoryAccess(): void
    {
        self::ensureKernelShutdown();
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = 'admin';
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = 'admin.localhost';
        try {
            $this->browser = self::createClient([], ['HTTP_HOST' => 'admin.localhost', 'HTTP_ORIGIN' => 'http://admin.localhost']);
            $this->browser->disableReboot();
            $this->store = self::getContainer()->get(WorkbenchStore::class);
            $this->fixture = WorkbenchFixture::reset($this->store->db);
            $crawler = $this->browser->request('GET', 'http://admin.localhost/login');
            $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'admin', '_password' => WorkbenchFixture::PASSWORD]));
            $this->browser->request('GET', 'http://admin.localhost/workbench/jobs');
            self::assertResponseStatusCodeSame(403);
            $this->browser->request('GET', 'http://admin.localhost/workbench/v1/jobs?tenant='.$this->fixture['tenants']['alice']);
            self::assertResponseStatusCodeSame(403);
        } finally {
            self::ensureKernelShutdown();
            $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = 'user';
            $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = 'user.localhost';
        }
    }
}
