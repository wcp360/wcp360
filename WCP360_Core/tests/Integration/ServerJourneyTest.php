<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Platform\Application\ServerObservation;
use Wcp360\Core\Module\Platform\Application\ServerOverview;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ServerJourneyTest extends WebTestCase
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
        $name = $context === 'admin' ? 'admin' : 'alice';
        $this->store->db->update('wcp_candidate_users', ['username' => 'root'], ['id' => $this->fixture['users'][$name]]);
    }

    private function login(): void
    {
        $host = $_ENV['WCP_CONTEXT'].'.localhost';
        $crawler = $this->browser->request('GET', 'http://'.$host.'/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'root', '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
        $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testRootLoginExplainsServerAdministrationAndAnonymousReadsRemainProtected(): void
    {
        $this->browser->request('GET', 'http://admin.localhost/login');
        self::assertSelectorTextContains('.auth-eyebrow', 'ADMINISTRATION ROOT');
        self::assertSelectorTextContains('.auth-context', 'Administration du serveur');
        self::assertSelectorExists('input[name="_username"][placeholder="root"]');
        $this->browser->request('GET', 'http://admin.localhost/workbench/server');
        self::assertResponseRedirects('/login');
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/server');
        self::assertResponseStatusCodeSame(401);
        self::assertSame('AUTHENTICATION_REQUIRED', json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR)['error']['code']);
    }

    public function testRootCanUseServerNavigationWithOptionalMfa(): void
    {
        $this->login();
        self::assertSelectorTextSame('.wcp-core-scope', 'ROOT');
        self::assertSelectorTextContains('.wcp-page-eyebrow', 'ADMINISTRATION DU SERVEUR');
        $crawler = $this->browser->request('GET', 'http://admin.localhost/workbench/server');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h1', 'Vue du serveur');
        self::assertSelectorExists('a[aria-current="page"][href="/workbench/server"]');
        self::assertSelectorExists('[data-server-observed-at]');
        self::assertSelectorExists('[data-server-architecture]');
        self::assertSelectorExists('a[href="/workbench/audit"]');
        $this->browser->click($crawler->selectLink('Actualiser')->link());
        self::assertResponseIsSuccessful();
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
    }

    public function testApiHasOnlyScopedCapacityMetadataAndHeadHasNoBody(): void
    {
        $this->login();
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/server');
        self::assertResponseIsSuccessful();
        $data = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(['schema','observed_at','scope','server'], array_keys($data));
        self::assertSame('current_process_namespace', $data['scope']);
        self::assertSame(['os_family','platform','architecture','memory','disk'], array_keys($data['server']));
        foreach (['effective_uid','managed_paths','network','password_hash','session_id','database_url',WorkbenchFixture::PASSWORD] as $private) self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
        $this->browser->request('HEAD', 'http://admin.localhost/workbench/v1/server');
        self::assertResponseIsSuccessful();
        self::assertSame('', $this->browser->getResponse()->getContent());
    }

    public function testQuerySelectorsAndMutatingMethodsCannotTurnTheOverviewIntoAnExecutor(): void
    {
        $this->login();
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        foreach (['path' => '/etc/private-server-marker', 'command' => 'private-server-marker', 'refresh' => ['x']] as $key => $value) {
            $this->browser->request('GET', 'http://admin.localhost/workbench/v1/server?'.http_build_query([$key => $value]));
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-server-marker', $this->browser->getResponse()->getContent());
        }
        $this->browser->request('POST', 'http://admin.localhost/workbench/server');
        self::assertResponseStatusCodeSame(405);
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testUserNamedRootKeepsUserAuthorityAndCannotReadServerData(): void
    {
        $this->bootBrowser('user');
        $this->login();
        self::assertSelectorTextSame('.wcp-core-scope', 'USER');
        self::assertSelectorNotExists('a[href="/workbench/server"]');
        foreach (['/workbench/server','/workbench/v1/server','/workbench/audit'] as $path) {
            $this->browser->request('GET', 'http://user.localhost'.$path);
            self::assertResponseStatusCodeSame(403);
            self::assertStringNotContainsString('data-server-observed-at', $this->browser->getResponse()->getContent());
        }
    }

    public function testInactiveRootSessionCannotKeepReadingServerCapacity(): void
    {
        $this->login();
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['admin']]);
        $this->browser->request('GET', 'http://admin.localhost/workbench/server');
        self::assertResponseRedirects('/login');
        self::assertStringNotContainsString('data-server-observed-at', $this->browser->getResponse()->getContent());
    }

    public function testWindowsUnknownMeasurementsAndUntrustedArchitectureRenderWithoutFakeZeroOrHtmlExecution(): void
    {
        $this->login();
        $probe = new class implements ServerObservation {
            public function read(): array { return ['os_family' => 'Windows','platform' => null,
                'architecture' => '<script>private-arch-mark</script>','memory' => null,'disk' => null]; }
        };
        self::getContainer()->set(ServerOverview::class, new ServerOverview(new CandidateAuthorization($this->store->db), $probe));
        $this->browser->request('GET', 'http://admin.localhost/workbench/server');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('[data-server-memory-total]', 'Non disponible');
        self::assertSelectorTextContains('[data-server-storage-free]', 'Non disponible');
        self::assertSelectorTextContains('[role="status"]', 'Windows');
        self::assertStringContainsString('&lt;script&gt;', $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString('<script>private-arch-mark</script>', $this->browser->getResponse()->getContent());
        self::assertSelectorNotExists('meter');
    }

    public function testEnabledMfaStillBlocksRootServerReadsUntilSecondFactorCompletes(): void
    {
        $mfa = self::getContainer()->get(CandidateMfa::class);
        $setup = $mfa->startEnrollment($this->fixture['users']['admin']);
        $result = $mfa->check($this->fixture['users']['admin'], TOTP::createFromSecret($setup['secret'])->now());
        self::assertNotNull($result);
        $crawler = $this->browser->request('GET', 'http://admin.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'root','_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/mfa');
        $this->browser->request('GET', 'http://admin.localhost/workbench/server');
        self::assertResponseRedirects('/mfa');
        self::assertStringNotContainsString('data-server-observed-at', $this->browser->getResponse()->getContent());
        $crawler = $this->browser->request('GET', 'http://admin.localhost/mfa');
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => $result['recovery_codes'][0]]));
        self::assertResponseRedirects('/');
        $this->browser->request('GET', 'http://admin.localhost/workbench/server');
        self::assertResponseIsSuccessful();
    }
}
