<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\HostingUsersFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class UserListJourneyTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private array $saved = [];

    protected function setUp(): void
    {
        foreach (['WCP_CONTEXT','WCP_PUBLIC_HOST'] as $name) $this->saved[$name] = [$_ENV[$name] ?? null, $_SERVER[$name] ?? null];
        $this->bootBrowser('admin');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->saved as $name => [$env,$server]) {
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
        HostingUsersFixture::seed($this->store->db, $this->fixture, 60);
    }

    private function login(string $username = 'admin'): void
    {
        $host = $_ENV['WCP_CONTEXT'].'.localhost';
        $crawler = $this->browser->request('GET', 'http://'.$host.'/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => $username, '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
        $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testAnonymousInventoryHasTheExistingBrowserAndApiAuthenticationBoundaries(): void
    {
        $this->browser->request('GET', 'http://admin.localhost/workbench/users');
        self::assertResponseRedirects('/login');
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/users');
        self::assertResponseStatusCodeSame(401);
        self::assertSame('AUTHENTICATION_REQUIRED', json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR)['error']['code']);
    }

    public function testRootCanFilterAndNavigateUsersInTheOfficialLayoutWithoutMandatoryMfa(): void
    {
        $this->login();
        self::assertSelectorTextSame('.wcp-core-scope', 'ROOT');
        self::assertSelectorExists('a[href="/workbench/users"]');
        $crawler = $this->browser->request('GET', 'http://admin.localhost/workbench/users?q=acct');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h1', 'Comptes d’hébergement');
        self::assertSelectorExists('a[aria-current="page"][href="/workbench/users"]');
        self::assertSelectorCount(25, 'tr[data-hosting-user-id]');
        $first = $crawler->filter('tr[data-hosting-user-id]')->each(static fn ($node) => $node->attr('data-hosting-user-id'));
        $crawler = $this->browser->click($crawler->filter('a[rel="next"]')->link());
        self::assertResponseIsSuccessful();
        self::assertNotSame($first, $crawler->filter('tr[data-hosting-user-id]')->each(static fn ($node) => $node->attr('data-hosting-user-id')));
        $this->browser->submit($crawler->selectButton('Filtrer')->form(['q' => 'acct', 'state' => 'inactive', 'limit' => '100']));
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(30, 'tr[data-hosting-user-id]');
        self::assertSelectorNotExists('input[name="after"]');
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
    }

    public function testApiProjectionAndHeadNeverExposeCredentialsOrAdministratorRows(): void
    {
        $this->login();
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/users?q=alice');
        self::assertResponseIsSuccessful();
        $page = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertCount(1, $page['items']);
        self::assertSame(['id','username','email','active'], array_keys($page['items'][0]));
        self::assertSame('alice', $page['items'][0]['username']);
        self::assertTrue($page['items'][0]['active']);
        foreach (['password_hash','password_revision','mfa','session','lock_guard',WorkbenchFixture::PASSWORD] as $private) self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
        $this->browser->request('HEAD', 'http://admin.localhost/workbench/v1/users');
        self::assertResponseIsSuccessful();
        self::assertSame('', $this->browser->getResponse()->getContent());
    }

    public function testAccountLinkKeepsTheSelectedUserAfterBlockingAndRestoringItsExistingAccess(): void
    {
        $this->login();
        $crawler = $this->browser->request('GET', 'http://admin.localhost/workbench/users?q=alice');
        $crawler = $this->browser->click($crawler->selectLink('Gérer les accès de alice')->link());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('[data-selected-hosting-user]', 'alice');
        self::assertSelectorCount(1, 'tr[data-user-id]');
        self::assertSelectorExists('tr[data-user-id="'.$this->fixture['users']['alice'].'"]');
        self::assertSelectorNotExists('tr[data-user-id="'.$this->fixture['users']['bob'].'"]');
        $this->browser->submit($crawler->selectButton('Bloquer')->form());
        self::assertResponseRedirects('/workbench/access?user='.$this->fixture['users']['alice'], 303);
        $crawler = $this->browser->followRedirect();
        self::assertSelectorTextContains('[data-selected-hosting-user]', 'alice');
        self::assertSelectorCount(1, 'tr[data-user-id]');
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_permission_denials WHERE user_id=?', [$this->fixture['users']['alice']]));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_permission_denials WHERE user_id=?', [$this->fixture['users']['bob']]));
        $this->browser->submit($crawler->selectButton('Lever le blocage')->form());
        self::assertResponseRedirects('/workbench/access?user='.$this->fixture['users']['alice'], 303);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_permission_denials'));
    }

    public function testSelectedUserSurvivesPermissionPageNavigationAndAnEmptyMembershipIsValid(): void
    {
        $this->login();
        $db = $this->store->db;
        $account = $db->fetchOne('SELECT account_id FROM wcp_candidate_tenants WHERE id=?', [$this->fixture['tenants']['alice']]);
        for ($i = 0; $i < 101; $i++) {
            $tenant = WorkbenchStore::id();
            $db->insert('wcp_candidate_tenants', ['id' => $tenant, 'account_id' => $account, 'name' => sprintf('extra-space-%03d', $i)]);
            $db->insert('wcp_candidate_memberships', ['user_id' => $this->fixture['users']['alice'], 'tenant_id' => $tenant, 'permission' => 'candidate.hosting.plan']);
        }
        $crawler = $this->browser->request('GET', 'http://admin.localhost/workbench/access?user='.$this->fixture['users']['alice']);
        self::assertSelectorCount(100, 'tr[data-user-id]');
        $this->browser->click($crawler->selectLink('Suivante')->link());
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(2, 'tr[data-user-id]');
        self::assertSelectorTextContains('[data-selected-hosting-user]', 'alice');
        self::assertSelectorNotExists('tr[data-user-id="'.$this->fixture['users']['bob'].'"]');
        $id = $db->fetchOne("SELECT id FROM wcp_candidate_users WHERE username='acct0001'");
        $this->browser->request('GET', 'http://admin.localhost/workbench/access?user='.$id);
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('[data-selected-hosting-user]', 'acct0001');
        self::assertSelectorCount(0, 'tr[data-user-id]');
    }

    public function testInvalidFiltersSelectionAndReturnNavigationDoNotReflectInputOrChangeAccess(): void
    {
        $this->login();
        foreach ([['q' => '<script>private-account-filter</script>'], ['state' => ['active']], ['limit' => '1000'], ['sort' => 'password_hash']] as $query) {
            $this->browser->request('GET', 'http://admin.localhost/workbench/v1/users?'.http_build_query($query));
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-account-filter', $this->browser->getResponse()->getContent());
        }
        foreach (['private-invalid-user' => 400, WorkbenchStore::id() => 404, $this->fixture['users']['admin'] => 404] as $user => $status) {
            $this->browser->request('GET', 'http://admin.localhost/workbench/access?user='.$user);
            self::assertResponseStatusCodeSame($status);
        }
        $crawler = $this->browser->request('GET', 'http://admin.localhost/workbench/access?user='.$this->fixture['users']['alice']);
        $form = $crawler->selectButton('Bloquer')->form();
        $this->browser->request('POST', $form->getUri().'&return_url=https%3A%2F%2Fexample.test', $form->getPhpValues());
        self::assertResponseStatusCodeSame(400);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_permission_denials'));
        $this->browser->request('POST', 'http://admin.localhost/workbench/users');
        self::assertResponseStatusCodeSame(405);
    }

    public function testStoredContactDataIsEscapedAndReadonlyNavigationDoesNotAppendAudit(): void
    {
        $this->login();
        $this->store->db->update('wcp_candidate_users', ['email' => '<script>private-contact-mark</script>'], ['id' => $this->fixture['users']['bob']]);
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        $this->browser->request('GET', 'http://admin.localhost/workbench/users?q=bob');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('&lt;script&gt;', $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString('<script>private-contact-mark</script>', $this->browser->getResponse()->getContent());
        $this->browser->request('GET', 'http://admin.localhost/workbench/access?user='.$this->fixture['users']['bob']);
        self::assertResponseIsSuccessful();
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testUserNamedRootCannotListAccountsOrReadSelectedPermissionViews(): void
    {
        $this->bootBrowser('user');
        $this->store->db->update('wcp_candidate_users', ['username' => 'root'], ['id' => $this->fixture['users']['alice']]);
        $this->login('root');
        self::assertSelectorNotExists('a[href="/workbench/users"]');
        foreach (['/workbench/users','/workbench/v1/users','/workbench/access?user='.$this->fixture['users']['bob']] as $path) {
            $this->browser->request('GET', 'http://user.localhost'.$path);
            self::assertResponseStatusCodeSame(403);
            self::assertStringNotContainsString('bob@example.test', $this->browser->getResponse()->getContent());
        }
    }

    public function testInactivatedRootSessionCannotKeepReadingContactData(): void
    {
        $this->login();
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['admin']]);
        $this->browser->request('GET', 'http://admin.localhost/workbench/users');
        self::assertResponseRedirects('/login');
        self::assertStringNotContainsString('alice@example.test', $this->browser->getResponse()->getContent());
    }
}
