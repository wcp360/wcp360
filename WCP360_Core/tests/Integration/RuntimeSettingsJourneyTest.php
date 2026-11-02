<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Platform\Application\ApplicationServerSettings;
use Wcp360\Core\Shared\Http\RequestReference;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class RuntimeSettingsJourneyTest extends WebTestCase
{
    private const API = '/workbench/v1/settings/application-server';
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private array $saved = [];
    private string $surface = 'admin';

    protected function setUp(): void
    {
        foreach (['WCP_CONTEXT','WCP_PUBLIC_HOST','WCP_ALLOW_FRANKENPHP_PLANS'] as $name) {
            $this->saved[$name] = [$_ENV[$name] ?? null, $_SERVER[$name] ?? null];
        }
        $this->bootBrowser();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->saved as $name => [$env, $server]) {
            if ($env === null) unset($_ENV[$name]); else $_ENV[$name] = $env;
            if ($server === null) unset($_SERVER[$name]); else $_SERVER[$name] = $server;
        }
    }

    private function bootBrowser(string $surface = 'admin', bool $worker = false): void
    {
        self::ensureKernelShutdown();
        $this->surface = $surface;
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $surface;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $surface.'.localhost';
        $_ENV['WCP_ALLOW_FRANKENPHP_PLANS'] = $_SERVER['WCP_ALLOW_FRANKENPHP_PLANS'] = $worker ? '1' : '0';
        $this->browser = self::createClient([], ['HTTP_HOST' => $surface.'.localhost', 'HTTP_ORIGIN' => 'http://'.$surface.'.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function url(string $path): string { return 'http://'.$this->surface.'.localhost'.$path; }

    private function login(string $name = 'admin'): void
    {
        $crawler = $this->browser->request('GET', $this->url('/login'));
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => $name, '_password' => WorkbenchFixture::PASSWORD]));
        for ($i = 0; $i < 3 && $this->browser->getResponse()->isRedirect(); ++$i) $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
    }

    private function page(): Crawler
    {
        $crawler = $this->browser->request('GET', $this->url('/workbench/settings'));
        self::assertResponseIsSuccessful();
        return $crawler;
    }

    private function token(): string
    {
        return $this->page()->selectButton('Enregistrer le défaut')->form()->getValues()['_token'];
    }

    private function apiWrite(array $fields, string $token, array $headers = []): void
    {
        $this->browser->request('POST', $this->url(self::API), [], [],
            array_replace(['CONTENT_TYPE' => 'application/json', 'HTTP_X_CSRF_TOKEN' => $token], $headers),
            json_encode($fields, JSON_THROW_ON_ERROR));
    }

    private function data(): array
    {
        return json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
    }

    private function revision(): int { return (int) $this->store->db->fetchOne('SELECT revision FROM wcp_candidate_runtime_settings'); }

    public function testAnonymousBrowserAndApiKeepTheirExistingAuthenticationBoundaries(): void
    {
        $this->browser->request('GET', $this->url('/workbench/settings'));
        self::assertResponseRedirects('/login');
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseStatusCodeSame(401);
        self::assertSame('AUTHENTICATION_REQUIRED', $this->data()['error']['code']);
        $this->apiWrite(['application_server' => 'php_fpm', 'revision' => 1], 'forged');
        self::assertResponseStatusCodeSame(401);
        self::assertSame(1, $this->revision());
    }

    public function testUserNamedRootCannotReadOrChangeServerWideDefaults(): void
    {
        $this->bootBrowser('user');
        $this->store->db->update('wcp_candidate_users', ['username' => 'root'], ['id' => $this->fixture['users']['alice']]);
        $this->login('root');
        self::assertSelectorNotExists('.wcp-nav a[href="/workbench/settings"]');
        foreach (['/workbench/settings', self::API] as $path) {
            $this->browser->request('GET', $this->url($path));
            self::assertResponseStatusCodeSame(403);
        }
        $this->apiWrite(['application_server' => 'php_fpm', 'revision' => 1], 'forged');
        self::assertResponseStatusCodeSame(403);
        self::assertSame(1, $this->revision());
    }

    public function testPageAndApiExposeTheCurrentDefaultWithoutMutatingOrInventingReadiness(): void
    {
        $this->login();
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        $this->page();
        self::assertSelectorTextSame('h1', 'Paramètres globaux');
        self::assertSelectorExists('a[aria-current="page"][href="/workbench/settings"]');
        self::assertSelectorExists('#settings-application-server option[value="php_fpm"][selected]');
        self::assertSelectorExists('#settings-application-server option[value="frankenphp_worker"][disabled]');
        self::assertSelectorExists('[data-runtime-worker-unavailable]');
        self::assertSelectorExists('a[href="/workbench/audit?kind=candidate.runtime.default.changed"]');
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseIsSuccessful();
        self::assertSame(['schema','settings','capabilities'], array_keys($this->data()));
        self::assertSame(['application_server' => 'php_fpm', 'revision' => 1], $this->data()['settings']);
        self::assertSame(['frankenphp_worker_plans' => false], $this->data()['capabilities']);
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        foreach (['password_hash','database_url','installed','ready',WorkbenchFixture::PASSWORD] as $private) {
            self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
        }
        $this->browser->request('HEAD', $this->url(self::API));
        self::assertResponseIsSuccessful();
        self::assertSame('', $this->browser->getResponse()->getContent());
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testDedicatedFormSavesWithFeedbackAndARealServerGeneratedAuditReference(): void
    {
        $this->login();
        $crawler = $this->page();
        $this->browser->submit($crawler->selectButton('Enregistrer le défaut')->form());
        self::assertResponseRedirects('/workbench/settings', 303);
        $reference = $this->browser->getResponse()->headers->get(RequestReference::HEADER);
        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/D', $reference);
        self::assertSame($reference, $this->store->db->fetchOne("SELECT request_id FROM wcp_candidate_audit WHERE kind='candidate.runtime.default.changed'"));
        $this->browser->followRedirect();
        self::assertSelectorTextContains('[data-runtime-saved]', 'a été enregistré');
        self::assertSelectorTextSame('[data-runtime-revision]', '2');
    }

    public function testConcurrentBrowserChangeReturns409AndCurrentValuesWithoutOverwriting(): void
    {
        $this->login();
        $form = $this->page()->selectButton('Enregistrer le défaut')->form();
        $settings = new ApplicationServerSettings($this->store, new ApplicationServerPolicy(true));
        $settings->change($this->fixture['users']['admin'], 'frankenphp_worker', 1);
        $this->browser->submit($form);
        self::assertResponseStatusCodeSame(409);
        self::assertSelectorExists('[data-runtime-conflict][role="alert"]');
        self::assertSelectorTextSame('[data-runtime-current]', 'FrankenPHP Worker');
        self::assertSelectorTextSame('[data-runtime-revision]', '2');
        self::assertSelectorExists('input[name="revision"][value="2"]');
        self::assertSame('frankenphp_worker', $settings->current()['application_server']);
        self::assertSame(1, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind='candidate.runtime.default.changed'"));
    }

    public function testApiWriteUsesTheSameCsrfRevisionAndCorrelationAsTheBrowser(): void
    {
        $this->login();
        $token = $this->token();
        $this->apiWrite(['application_server' => 'php_fpm', 'revision' => 1], $token,
            ['HTTP_X_WCP360_REQUEST_ID' => str_repeat('f', 32)]);
        self::assertResponseIsSuccessful();
        self::assertSame(2, $this->data()['settings']['revision']);
        $reference = $this->browser->getResponse()->headers->get(RequestReference::HEADER);
        self::assertNotSame(str_repeat('f', 32), $reference);
        self::assertSame($reference, $this->store->db->fetchOne("SELECT request_id FROM wcp_candidate_audit WHERE kind='candidate.runtime.default.changed'"));
        $this->apiWrite(['application_server' => 'php_fpm', 'revision' => 1], $token);
        self::assertResponseStatusCodeSame(409);
        self::assertSame(2, $this->revision());
        self::assertSame(1, (int) $this->store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_audit WHERE kind='candidate.runtime.default.changed'"));
    }

    public function testWorkerAvailabilityRemainsAnOperatorGateAndChangingDefaultsCreatesNoResources(): void
    {
        $this->login();
        $this->apiWrite(['application_server' => 'frankenphp_worker', 'revision' => 1], $this->token());
        self::assertResponseStatusCodeSame(403);
        self::assertSame(1, $this->revision());
        $this->bootBrowser('admin', true);
        $this->login();
        $this->apiWrite(['application_server' => 'frankenphp_worker', 'revision' => '1'], $this->token());
        self::assertResponseIsSuccessful();
        self::assertSame('frankenphp_worker', $this->data()['settings']['application_server']);
        self::assertTrue($this->data()['capabilities']['frankenphp_worker_plans']);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_sites'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
    }

    public function testInvalidJsonShapesTypesAndFieldsCannotChangeSettings(): void
    {
        $this->login();
        $token = $this->token();
        foreach ([[], ['application_server' => 'php_fpm'], ['application_server' => ['php_fpm'], 'revision' => 1],
            ['application_server' => 'php_fpm', 'revision' => true], ['application_server' => 'php_fpm', 'revision' => 1.5],
            ['application_server' => 'php_fpm', 'revision' => '01'], ['application_server' => 'php_fpm', 'revision' => 0],
            ['application_server' => 'php_fpm', 'revision' => 1000000000],
            ['application_server' => 'php_fpm', 'revision' => 1, 'command' => '<script>private-runtime-input</script>'],
            ['application_server' => 'inherit', 'revision' => 1]] as $fields) {
            $this->apiWrite($fields, $token);
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-runtime-input', $this->browser->getResponse()->getContent());
        }
        foreach (['{', 'null', str_repeat(' ', 4097)] as $body) {
            $this->browser->request('POST', $this->url(self::API), [], [], ['CONTENT_TYPE' => 'application/json','HTTP_X_CSRF_TOKEN' => $token], $body);
            self::assertResponseStatusCodeSame(400);
        }
        self::assertSame(1, $this->revision());
    }

    public function testApiRequiresExactOriginItsOwnCsrfAndJsonContentType(): void
    {
        $this->login();
        $token = $this->token();
        $fields = ['application_server' => 'php_fpm', 'revision' => 1];
        $this->apiWrite($fields, 'invalid');
        self::assertResponseStatusCodeSame(403);
        foreach (['http://other.localhost', 'null', ''] as $origin) {
            $this->apiWrite($fields, $token, ['HTTP_ORIGIN' => $origin]);
            self::assertResponseStatusCodeSame(403);
        }
        $this->apiWrite($fields, $token, ['CONTENT_TYPE' => 'text/plain']);
        self::assertResponseStatusCodeSame(400);
        self::assertSame(1, $this->revision());
    }

    public function testFixedNavigationQueryAndMethodsRefuseBeforeMutation(): void
    {
        $this->login();
        $token = $this->token();
        foreach (['/workbench/settings?command=private-runtime-input', self::API.'?tenant=private-runtime-input'] as $path) {
            $this->browser->request('GET', $this->url($path));
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-runtime-input', $this->browser->getResponse()->getContent());
        }
        foreach (['view=https://example.test', 'view[]=settings', 'return=/workbench/settings'] as $query) {
            $this->browser->request('POST', $this->url('/workbench/settings/application-server?'.$query),
                ['_token' => $token,'application_server' => 'php_fpm','revision' => '1']);
            self::assertResponseStatusCodeSame(400);
        }
        $this->browser->request('POST', $this->url('/workbench/settings'));
        self::assertResponseStatusCodeSame(405);
        $this->browser->request('DELETE', $this->url(self::API));
        self::assertResponseStatusCodeSame(405);
        self::assertSame(1, $this->revision());
    }

    public function testStaleRootProofBlocksWriteButStillAllowsInspection(): void
    {
        $this->login();
        $token = $this->token();
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_password_proof');
        self::assertIsArray($proof);
        $proof['at'] = time() - 301;
        $session->set('_wcp_password_proof', $proof);
        $session->save();
        $this->apiWrite(['application_server' => 'php_fpm', 'revision' => 1], $token);
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseIsSuccessful();
        self::assertSame(1, $this->revision());
    }

    public function testEnabledMfaRemainsRequiredBeforeTheSettingsPageAndWrite(): void
    {
        $mfa = self::getContainer()->get(CandidateMfa::class);
        $setup = $mfa->startEnrollment($this->fixture['users']['admin']);
        $result = $mfa->check($this->fixture['users']['admin'], TOTP::createFromSecret($setup['secret'])->now());
        self::assertNotNull($result);
        $this->login();
        $this->browser->request('GET', $this->url('/workbench/settings'));
        self::assertResponseRedirects();
        self::assertContains($this->browser->getResponse()->headers->get('Location'), ['/mfa','http://admin.localhost/mfa']);
        $crawler = $this->browser->request('GET', $this->url('/mfa'));
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => $result['recovery_codes'][0]]));
        self::assertResponseRedirects('/');
        $this->apiWrite(['application_server' => 'php_fpm', 'revision' => 1], $this->token());
        self::assertResponseIsSuccessful();
        self::assertTrue($mfa->status($this->fixture['users']['admin'])['enabled']);
    }

    public function testMissingSettingsReturnSafeApiErrorsWithoutDebugData(): void
    {
        $this->login();
        $this->store->db->executeStatement('DELETE FROM wcp_candidate_runtime_settings');
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseStatusCodeSame(500);
        self::assertSame(['error','request_id'], array_keys($this->data()));
        foreach (['Migrate the candidate', 'password_hash', 'SELECT ', WorkbenchFixture::PASSWORD] as $private) {
            self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
        }
    }

    public function testAuditFailureDoesNotAdvanceTheDefaultAndTheApiErrorIsRedacted(): void
    {
        $this->login();
        $token = $this->token();
        $sqlite = $this->store->db->getDatabasePlatform() instanceof SQLitePlatform;
        $this->store->db->executeStatement($sqlite
            ? "CREATE TRIGGER reject_runtime_http_audit BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind='candidate.runtime.default.changed' BEGIN SELECT RAISE(ABORT,'private-runtime-marker'); END"
            : "ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_runtime_http_audit CHECK (kind <> 'candidate.runtime.default.changed')");
        try {
            $this->apiWrite(['application_server' => 'php_fpm', 'revision' => 1], $token);
            self::assertResponseStatusCodeSame(500);
            self::assertSame(1, $this->revision());
            self::assertStringNotContainsString('private-runtime-marker', $this->browser->getResponse()->getContent());
        } finally {
            $this->store->db->executeStatement($sqlite ? 'DROP TRIGGER reject_runtime_http_audit'
                : 'ALTER TABLE wcp_candidate_audit DROP CONSTRAINT reject_runtime_http_audit');
        }
    }
}
