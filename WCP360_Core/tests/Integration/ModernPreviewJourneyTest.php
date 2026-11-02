<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ModernPreviewJourneyTest extends WebTestCase
{
    private const BROWSER = '/workbench/settings/modern-preview';
    private const API = '/workbench/v1/web-stacks/modern-preview';
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private array $saved = [];
    private string $surface = 'admin';

    protected function setUp(): void
    {
        foreach (['WCP_CONTEXT', 'WCP_PUBLIC_HOST', 'WCP_ALLOW_FRANKENPHP_PLANS'] as $name) {
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

    private function bootBrowser(string $surface = 'admin', bool $worker = true): void
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

    private function settings(): Crawler
    {
        $crawler = $this->browser->request('GET', $this->url('/workbench/settings'));
        self::assertResponseIsSuccessful();
        return $crawler;
    }

    private function token(): string
    {
        return $this->settings()->filter('form[action="'.self::BROWSER.'"] input[name="_token"]')->attr('value');
    }

    private function api(array $fields, string $token, array $headers = [], string $query = ''): void
    {
        $this->browser->request('POST', $this->url(self::API.$query), [], [],
            array_replace(['CONTENT_TYPE' => 'application/json', 'HTTP_X_CSRF_TOKEN' => $token], $headers),
            json_encode($fields, JSON_THROW_ON_ERROR));
    }

    private function data(): array
    {
        return json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
    }

    private function state(): array
    {
        $state = [];
        foreach (['jobs', 'sites', 'audit', 'runtime_settings', 'activations'] as $table) {
            $state[$table] = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_'.$table.' ORDER BY id');
        }
        return $state;
    }

    private function assertPrivateResponse(): void
    {
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('private'));
        self::assertSame('nosniff', $this->browser->getResponse()->headers->get('X-Content-Type-Options'));
    }

    public function testRealBrowserFormRendersOnlyModernArtifactsWithoutCreatingResources(): void
    {
        $this->login();
        $crawler = $this->settings();
        self::assertSelectorNotExists('option[value="modern"]');
        $before = $this->state();
        $form = $crawler->filter('form[action="'.self::BROWSER.'"]')->form([
            'hostname' => 'browser-preview.test', 'worker_acknowledged' => '1',
        ]);
        $this->browser->submit($form);
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(2, '[data-modern-artifact]');
        self::assertSelectorExists('[data-modern-artifact="caddy"]');
        self::assertSelectorExists('[data-modern-artifact="systemd"]');
        self::assertSelectorNotExists('[data-modern-artifact="nginx"]');
        self::assertSelectorNotExists('[data-modern-artifact="php-fpm"]');
        self::assertSelectorTextContains('[data-modern-artifact="caddy"] code', 'http://browser-preview.test:8082');
        self::assertSelectorTextContains('[data-modern-artifact="caddy"] code', 'bind 127.0.0.1');
        self::assertSelectorTextContains('[data-modern-artifact="systemd"] code', 'NoNewPrivileges=true');
        self::assertSelectorTextContains('[data-modern-scope]', '127.0.0.1:8082');
        $id = $this->browser->getCrawler()->filter('[data-modern-id]')->text();
        self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D', $id);
        self::assertNotContains($id, array_merge(array_values($this->fixture['users']), array_values($this->fixture['tenants'])));
        $this->assertPrivateResponse();
        self::assertSame($before, $this->state());
    }

    public function testRealApiAcceptsExplicitAcknowledgementAndReturnsNewSyntheticIds(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        $ids = [];
        foreach ([true, '1'] as $acknowledged) {
            $this->api(['hostname' => 'api-preview.test', 'worker_acknowledged' => $acknowledged], $token);
            self::assertResponseIsSuccessful();
            $data = $this->data();
            self::assertSame(['schema', 'web_stack', 'scope', 'host_changes', 'persisted', 'activation_available',
                'listener_allocation', 'preview_site_id', 'hostname', 'files'], array_keys($data));
            self::assertSame('wcp360.candidate.modern-preview/1', $data['schema']);
            self::assertSame('modern', $data['web_stack']);
            self::assertSame('single_site_loopback', $data['scope']);
            self::assertSame('not_reserved', $data['listener_allocation']);
            self::assertFalse($data['host_changes']);
            self::assertFalse($data['persisted']);
            self::assertFalse($data['activation_available']);
            self::assertSame(['caddy', 'systemd'], array_keys($data['files']));
            self::assertSame('Caddyfile', $data['files']['caddy']['name']);
            self::assertSame('frankenphp-modern.service', $data['files']['systemd']['name']);
            self::assertStringContainsString('http://api-preview.test:8082', $data['files']['caddy']['content']);
            self::assertStringContainsString('bind 127.0.0.1', $data['files']['caddy']['content']);
            self::assertStringContainsString('admin off', $data['files']['caddy']['content']);
            self::assertStringContainsString('CapabilityBoundingSet=', $data['files']['systemd']['content']);
            self::assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D', $data['preview_site_id']);
            $ids[] = $data['preview_site_id'];
            foreach (['password_hash', 'database_url', WorkbenchFixture::PASSWORD, $this->fixture['users']['admin']] as $private) {
                self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
            }
            $this->assertPrivateResponse();
        }
        self::assertNotSame($ids[0], $ids[1]);
        self::assertSame($before, $this->state());
    }

    public function testAnonymousBrowserAndApiCannotReachPreview(): void
    {
        $before = $this->state();
        $this->browser->request('POST', $this->url(self::BROWSER), ['hostname' => 'anonymous.test', 'worker_acknowledged' => '1']);
        self::assertResponseRedirects('/login');
        $this->api(['hostname' => 'anonymous.test', 'worker_acknowledged' => true], 'forged');
        self::assertResponseStatusCodeSame(401);
        self::assertSame('AUTHENTICATION_REQUIRED', $this->data()['error']['code']);
        self::assertSame($before, $this->state());
    }

    public function testHostingUserNamedRootCannotUseTheAdministratorPreview(): void
    {
        $this->bootBrowser('user');
        $this->store->db->update('wcp_candidate_users', ['username' => 'root'], ['id' => $this->fixture['users']['alice']]);
        $this->login('root');
        $before = $this->state();
        $this->api(['hostname' => 'foreign.test', 'worker_acknowledged' => true], 'forged');
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST', $this->url(self::BROWSER), [
            '_token' => 'forged', 'hostname' => 'foreign.test', 'worker_acknowledged' => '1',
        ]);
        self::assertResponseStatusCodeSame(403);
        self::assertSame($before, $this->state());
    }

    public function testDisabledWorkerGateRefusesForgedPreviewRequests(): void
    {
        $this->bootBrowser('admin', false);
        $this->login();
        $crawler = $this->settings();
        self::assertSelectorExists('[data-modern-unavailable]');
        self::assertSelectorExists('form[action="'.self::BROWSER.'"] button[disabled]');
        $token = $crawler->filter('form[action="'.self::BROWSER.'"] input[name="_token"]')->attr('value');
        $before = $this->state();
        $this->api(['hostname' => 'disabled.test', 'worker_acknowledged' => true], $token);
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST', $this->url(self::BROWSER), [
            '_token' => $token, 'hostname' => 'disabled.test', 'worker_acknowledged' => '1',
        ]);
        self::assertResponseStatusCodeSame(403);
        self::assertSame($before, $this->state());
    }

    public function testApiRejectsInvalidHostAcknowledgementTypesAndResourceSelectors(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        $valid = ['hostname' => 'invalid-preview.test', 'worker_acknowledged' => true];
        $invalid = [
            [], ['worker_acknowledged' => true], ['hostname' => 'invalid-preview.test'],
            array_replace($valid, ['hostname' => 'public.example.com']),
            array_replace($valid, ['hostname' => 'UPPER.test']),
            array_replace($valid, ['hostname' => "invalid.test\nprivate-preview-input"]),
            array_replace($valid, ['hostname' => ['invalid.test']]),
            array_replace($valid, ['hostname' => str_repeat('a', 254).'.test']),
            array_replace($valid, ['worker_acknowledged' => false]),
            array_replace($valid, ['worker_acknowledged' => '0']),
            array_replace($valid, ['worker_acknowledged' => 'true']),
            array_replace($valid, ['worker_acknowledged' => 1]),
            array_replace($valid, ['worker_acknowledged' => null]),
            array_replace($valid, ['worker_acknowledged' => ['1']]),
            $valid + ['actor' => $this->fixture['users']['admin']],
            $valid + ['site_id' => $this->fixture['tenants']['alice']],
            $valid + ['tenant_id' => $this->fixture['tenants']['alice']],
            $valid + ['path' => '/private-preview-input'],
            $valid + ['apply' => true],
            $valid + ['application_server' => 'frankenphp_worker'],
            $valid + ['web_stack' => 'modern'],
        ];
        foreach ($invalid as $fields) {
            $this->api($fields, $token);
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-preview-input', $this->browser->getResponse()->getContent());
        }
        self::assertSame($before, $this->state());
    }

    public function testBrowserRejectsMissingCompatibilityAndInjectedSelectors(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        foreach ([
            ['_token' => $token, 'hostname' => 'missing-ack.test'],
            ['_token' => $token, 'hostname' => 'bad-type.test', 'worker_acknowledged' => ['1']],
            ['_token' => $token, 'hostname' => 'valid.test', 'worker_acknowledged' => '1', 'apply' => '1'],
            ['_token' => $token, 'hostname' => 'valid.test', 'worker_acknowledged' => '1', 'actor' => $this->fixture['users']['admin']],
            ['_token' => $token, 'hostname' => 'valid.test', 'worker_acknowledged' => '1', 'path' => '/private-preview-input'],
        ] as $fields) {
            $this->browser->request('POST', $this->url(self::BROWSER), $fields);
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-preview-input', $this->browser->getResponse()->getContent());
        }
        self::assertSame($before, $this->state());
    }

    public function testPreviewRequiresItsOwnCsrfAndExactAdministratorOrigin(): void
    {
        $this->login();
        $crawler = $this->settings();
        $token = $crawler->filter('form[action="'.self::BROWSER.'"] input[name="_token"]')->attr('value');
        $otherToken = $crawler->filter('form[action^="/workbench/settings/application-server"] input[name="_token"]')->attr('value');
        $before = $this->state();
        $fields = ['hostname' => 'csrf.test', 'worker_acknowledged' => true];
        foreach (['forged', $otherToken, ''] as $badToken) {
            $this->api($fields, $badToken);
            self::assertResponseStatusCodeSame(403);
        }
        foreach (['http://user.localhost', 'http://other.localhost', 'null', ''] as $origin) {
            $this->api($fields, $token, ['HTTP_ORIGIN' => $origin]);
            self::assertResponseStatusCodeSame(403);
        }
        foreach (['forged', ['invalid']] as $badToken) {
            $this->browser->request('POST', $this->url(self::BROWSER), [
                '_token' => $badToken, 'hostname' => 'csrf.test', 'worker_acknowledged' => '1',
            ]);
            self::assertResponseStatusCodeSame(403);
        }
        self::assertSame($before, $this->state());
    }

    public function testApiRejectsNonJsonScalarMalformedAndOversizedBodies(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        foreach (['{', 'null', 'true', '"synthetic"', '[]', str_repeat(' ', 4097)] as $body) {
            $this->browser->request('POST', $this->url(self::API), [], [],
                ['CONTENT_TYPE' => 'application/json', 'HTTP_X_CSRF_TOKEN' => $token], $body);
            self::assertResponseStatusCodeSame(400);
        }
        $this->api(['hostname' => 'content-type.test', 'worker_acknowledged' => true], $token, ['CONTENT_TYPE' => 'text/plain']);
        self::assertResponseStatusCodeSame(400);
        self::assertSame($before, $this->state());
    }

    public function testQueryArgumentsAndUnsupportedMethodsCannotSelectOrApplyAnything(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        foreach (['actor=private-preview-input', 'site_id=private-preview-input', 'path=/private-preview-input', 'apply=1'] as $query) {
            $this->api(['hostname' => 'query.test', 'worker_acknowledged' => true], $token, [], '?'.$query);
            self::assertResponseStatusCodeSame(400);
            $this->browser->request('POST', $this->url(self::BROWSER.'?'.$query), [
                '_token' => $token, 'hostname' => 'query.test', 'worker_acknowledged' => '1',
            ]);
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-preview-input', $this->browser->getResponse()->getContent());
        }
        foreach ([self::BROWSER, self::API] as $path) {
            foreach (['GET', 'PUT', 'DELETE'] as $method) {
                $this->browser->request($method, $this->url($path));
                self::assertResponseStatusCodeSame(405);
            }
        }
        self::assertSame($before, $this->state());
    }

    public function testStaleRootProofBlocksBothPreviewsWhileSettingsRemainReadable(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_password_proof');
        self::assertIsArray($proof);
        $proof['at'] = time() - 301;
        $session->set('_wcp_password_proof', $proof);
        $session->save();
        $this->api(['hostname' => 'stale.test', 'worker_acknowledged' => true], $token);
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('POST', $this->url(self::BROWSER), [
            '_token' => $token, 'hostname' => 'stale.test', 'worker_acknowledged' => '1',
        ]);
        self::assertResponseStatusCodeSame(403);
        $this->settings();
        self::assertSame($before, $this->state());
    }

    public function testEnabledMfaBlocksPendingPreviewAndAllowsItAfterCompletion(): void
    {
        $mfa = self::getContainer()->get(CandidateMfa::class);
        $setup = $mfa->startEnrollment($this->fixture['users']['admin']);
        $result = $mfa->check($this->fixture['users']['admin'], TOTP::createFromSecret($setup['secret'])->now());
        self::assertNotNull($result);
        $this->login();
        $this->browser->request('POST', $this->url(self::BROWSER), [
            '_token' => 'pending-token', 'hostname' => 'pending.test', 'worker_acknowledged' => '1',
        ]);
        self::assertResponseRedirects();
        self::assertContains($this->browser->getResponse()->headers->get('Location'), ['/mfa', 'http://admin.localhost/mfa']);
        $this->api(['hostname' => 'pending.test', 'worker_acknowledged' => true], 'pending-token');
        self::assertResponseStatusCodeSame(403);
        self::assertSame('MFA_REQUIRED', $this->data()['error']['code']);
        $crawler = $this->browser->request('GET', $this->url('/mfa'));
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => $result['recovery_codes'][0]]));
        self::assertResponseRedirects('/');
        $token = $this->token();
        $before = $this->state();
        $this->api(['hostname' => 'completed-mfa.test', 'worker_acknowledged' => true], $token);
        self::assertResponseIsSuccessful();
        self::assertSame(['caddy', 'systemd'], array_keys($this->data()['files']));
        self::assertTrue($mfa->status($this->fixture['users']['admin'])['enabled']);
        self::assertSame($before, $this->state());
    }
}
