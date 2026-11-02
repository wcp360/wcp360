<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\AuditEventsFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class AuditJourneyTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private array $saved = [];

    protected function setUp(): void
    {
        foreach (['WCP_CONTEXT', 'WCP_PUBLIC_HOST'] as $name) $this->saved[$name] = [$_ENV[$name] ?? null, $_SERVER[$name] ?? null];
        $this->bootBrowser('admin');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->saved as $name => [$env, $server]) {
            if ($env === null) unset($_ENV[$name]); else $_ENV[$name] = $env;
            if ($server === null) unset($_SERVER[$name]); else $_SERVER[$name] = $server;
        }
    }

    private function bootBrowser(string $context): void
    {
        self::ensureKernelShutdown();
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $context;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $context.'.localhost';
        $this->browser = self::createClient([], ['HTTP_HOST' => $context.'.localhost', 'HTTP_ORIGIN' => 'http://'.$context.'.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        AuditEventsFixture::seed($this->store->db, $this->fixture, 60);
    }

    private function login(string $name = 'admin'): void
    {
        $host = $name === 'admin' ? 'admin' : 'user';
        $crawler = $this->browser->request('GET', 'http://'.$host.'.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => $name, '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
        $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testAnonymousBrowserAndApiKeepTheirExistingAuthenticationRefusals(): void
    {
        $this->browser->request('GET', 'http://admin.localhost/workbench/audit');
        self::assertResponseRedirects('/login');
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/audit');
        self::assertResponseStatusCodeSame(401);
        self::assertSame('AUTHENTICATION_REQUIRED', json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR)['error']['code']);
    }

    public function testPasswordOnlyAdminCanBrowseFilterAndTraverseAuditWithoutMandatoryMfa(): void
    {
        $this->login();
        self::assertSelectorExists('a[href="/workbench/audit"]');
        $crawler = $this->browser->request('GET', 'http://admin.localhost/workbench/audit');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h1', 'Journal d’audit');
        self::assertSelectorCount(25, 'tr[data-audit-id]');
        self::assertSelectorExists('a[href="/workbench/audit"][aria-current="page"]');
        self::assertSelectorNotExists('a[rel="prev"]');
        $first = $crawler->filter('tr[data-audit-id]')->each(static fn ($node) => $node->attr('data-audit-id'));
        $crawler = $this->browser->click($crawler->filter('a[rel="next"]')->link());
        self::assertResponseIsSuccessful();
        self::assertNotSame($first, $crawler->filter('tr[data-audit-id]')->each(static fn ($node) => $node->attr('data-audit-id')));
        self::assertSelectorExists('a[rel="prev"]');
        $this->browser->submit($crawler->selectButton('Filtrer')->form(['request' => str_repeat('b', 32), 'kind' => 'hosting.plan.succeeded', 'limit' => '100']));
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(30, 'tr[data-audit-id]');
        self::assertSelectorNotExists('input[name="after"]');
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
    }

    public function testApiProjectionAndReferenceFilterNeverExposeAccountOrConfigurationContent(): void
    {
        $this->login();
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/audit?request='.str_repeat('a', 32).'&limit=100');
        self::assertResponseIsSuccessful();
        $page = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(['items','pagination'], array_keys($page));
        self::assertCount(30, $page['items']);
        self::assertSame(['id','actor_id','tenant_id','kind','resource_id','created_at','request_id'], array_keys($page['items'][0]));
        foreach (['password_hash','username','email','result','fingerprint','idempotency_key',WorkbenchFixture::PASSWORD] as $private) self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
        $this->browser->request('HEAD', 'http://admin.localhost/workbench/v1/audit');
        self::assertResponseIsSuccessful();
        self::assertSame('', $this->browser->getResponse()->getContent());
    }

    public function testInvalidFiltersAndUnsupportedMethodsDoNotEchoInputOrChangeAudit(): void
    {
        $this->login();
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        foreach ([['kind' => '<script>private-audit-filter</script>'], ['actor' => '../private-audit-filter'],
            ['request' => ['x']], ['limit' => '10000'], ['from' => '2026-02-31T00:00:00Z'],
            ['after' => AuditEventsFixture::id(9999)], ['columns' => 'password_hash']] as $input) {
            $this->browser->request('GET', 'http://admin.localhost/workbench/v1/audit?'.http_build_query($input));
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-audit-filter', $this->browser->getResponse()->getContent());
        }
        $this->browser->request('POST', 'http://admin.localhost/workbench/audit');
        self::assertResponseStatusCodeSame(405);
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testStoredEventTextIsEscapedAndSystemReferencesRemainReadable(): void
    {
        $this->login();
        $this->store->db->insert('wcp_candidate_audit', ['id' => AuditEventsFixture::id(9999), 'actor_id' => null,
            'tenant_id' => null, 'kind' => '<script>private-audit-mark</script>', 'resource_id' => null,
            'created_at' => '2026-11-03T00:00:00Z', 'request_id' => null]);
        $this->browser->request('GET', 'http://admin.localhost/workbench/audit');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('&lt;script&gt;', $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString('<script>private-audit-mark</script>', $this->browser->getResponse()->getContent());
        self::assertSelectorTextContains('tr[data-audit-id="'.AuditEventsFixture::id(9999).'"]', 'Non renseigné');
    }

    public function testUserSurfaceCannotViewAuditEvenWithValidTenantMembership(): void
    {
        $this->bootBrowser('user');
        $this->login('alice');
        self::assertSelectorNotExists('a[href="/workbench/audit"]');
        foreach (['/workbench/audit', '/workbench/v1/audit'] as $path) {
            $this->browser->request('GET', 'http://user.localhost'.$path);
            self::assertResponseStatusCodeSame(403);
            self::assertStringNotContainsString('hosting.plan.succeeded', $this->browser->getResponse()->getContent());
        }
    }

    public function testInactiveAdminSessionCannotUsePreviouslyVisibleAudit(): void
    {
        $this->login();
        $this->browser->request('GET', 'http://admin.localhost/workbench/audit');
        self::assertResponseIsSuccessful();
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['admin']]);
        $this->browser->request('GET', 'http://admin.localhost/workbench/audit');
        self::assertResponseRedirects('/login');
        self::assertStringNotContainsString('hosting.plan.succeeded', $this->browser->getResponse()->getContent());
    }
}
