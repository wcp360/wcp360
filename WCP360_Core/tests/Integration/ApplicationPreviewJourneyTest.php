<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ApplicationPreviewJourneyTest extends WebTestCase
{
    private const FORM = '/workbench/settings/application-preview';
    private const API = '/workbench/v1/application/preview';
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private array $saved = [];
    private string $surface = 'admin';

    protected function setUp(): void
    {
        foreach (['WCP_CONTEXT','WCP_PUBLIC_HOST'] as $name) $this->saved[$name] = [$_ENV[$name] ?? null, $_SERVER[$name] ?? null];
        $this->bootBrowser();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->saved as $name => [$env,$server]) {
            if ($env === null) unset($_ENV[$name]); else $_ENV[$name] = $env;
            if ($server === null) unset($_SERVER[$name]); else $_SERVER[$name] = $server;
        }
    }

    private function bootBrowser(string $surface = 'admin'): void
    {
        self::ensureKernelShutdown();
        $this->surface = $surface;
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $surface;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $surface.'.localhost';
        $this->browser = self::createClient([], ['HTTP_HOST' => $surface.'.localhost', 'HTTP_ORIGIN' => 'http://'.$surface.'.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function url(string $path): string { return 'http://'.$this->surface.'.localhost'.$path; }

    private function login(string $username = 'admin'): void
    {
        $crawler = $this->browser->request('GET', $this->url('/login'));
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => $username, '_password' => WorkbenchFixture::PASSWORD]));
        for ($i=0; $i<3 && $this->browser->getResponse()->isRedirect(); ++$i) $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
    }

    private function token(): string
    {
        $crawler = $this->browser->request('GET', $this->url('/workbench/settings'));
        self::assertResponseIsSuccessful();
        return $crawler->filter('form[action="'.self::FORM.'"] input[name="_token"]')->attr('value');
    }

    private function api(array $fields, string $token, array $headers = [], string $query = ''): void
    {
        $this->browser->request('POST', $this->url(self::API.$query), [], [],
            array_replace(['CONTENT_TYPE' => 'application/json','HTTP_X_CSRF_TOKEN' => $token], $headers),
            json_encode($fields, JSON_THROW_ON_ERROR));
    }

    private function data(): array { return json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR); }

    private function state(): array
    {
        $state = [];
        foreach (['sites','jobs','activations','audit','runtime_settings'] as $table) $state[$table] = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_'.$table.' ORDER BY id');
        return $state;
    }

    public function testRootCanInspectSevenRecipesAndThreeProfilesWithPrivateHead(): void
    {
        $this->login();
        $before = $this->state();
        $this->browser->request('GET', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseIsSuccessful();
        $catalogue = $this->data();
        self::assertSame('wcp360.candidate.application-recipes/2', $catalogue['schema']);
        self::assertCount(7, $catalogue['recipes']);
        $recipes = array_column($catalogue['recipes'], null, 'id');
        self::assertSame('wcp360.candidate.application-plan/2', $recipes['nginx_apache_php_fpm']['plan_schema']);
        self::assertSame(['nginx','apache','php_fpm','htaccess'], $recipes['nginx_apache_php_fpm']['artifacts']);
        self::assertTrue($recipes['nginx_apache_php_fpm']['htaccess_supported']);
        foreach (['nginx_php_fpm','nginx_frankenphp','apache_php_fpm','apache_frankenphp','caddy_php_fpm','caddy_frankenphp'] as $legacy) {
            self::assertSame('wcp360.candidate.application-plan/1', $recipes[$legacy]['plan_schema']);
        }
        self::assertSame(['php','front_controller','wordpress'], array_column($this->data()['profiles'], 'id'));
        self::assertFalse($this->data()['deployment_available']);
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        $this->browser->request('HEAD', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseIsSuccessful();
        self::assertSame('', $this->browser->getResponse()->getContent());
        $crawler = $this->browser->request('GET', $this->url('/workbench/settings'));
        self::assertResponseIsSuccessful();
        self::assertCount(7, $crawler->filter('#application-recipe option'));
        self::assertSame('nginx_php_fpm', $crawler->filter('#application-recipe option')->first()->attr('value'));
        self::assertSame($before, $this->state());
    }

    public function testBrowserAndApiRenderEveryCombinationWithoutSelectingRealResources(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        foreach (['nginx_php_fpm','nginx_frankenphp','apache_php_fpm','apache_frankenphp','caddy_php_fpm','caddy_frankenphp'] as $recipe) {
            foreach (['php','front_controller','wordpress'] as $application) {
                $this->api(['hostname'=>'app.test','recipe'=>$recipe,'application'=>$application], $token);
                self::assertResponseIsSuccessful();
                $data = $this->data();
                self::assertSame('wcp360.candidate.application-preview/1', $data['schema']);
                self::assertSame($application, $data['application']);
                self::assertFalse($data['worker_mode']);
                self::assertFalse($data['host_changes']);
                self::assertMatchesRegularExpression('/^[0-9a-f-]{14}7[0-9a-f-]{21}$/D', $data['preview_site_id']);
                self::assertStringNotContainsString(WorkbenchFixture::PASSWORD, $this->browser->getResponse()->getContent());
            }
        }
        $this->browser->request('POST', $this->url(self::FORM), ['_token'=>$token,'hostname'=>'browser.test',
            'recipe'=>'apache_php_fpm','application'=>'wordpress','require_htaccess'=>'1']);
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(3, '[data-application-artifact]');
        self::assertSelectorExists('[data-application-artifact="htaccess"]');
        self::assertSelectorTextContains('[data-application-htaccess]', 'natif');
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('private'));
        self::assertSame($before, $this->state());
    }

    public function testHybridPreviewUsesApacheForAllContentAcrossThreeProfilesWithoutWritesOrActivation(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        $identities = [];
        foreach (['php','front_controller','wordpress'] as $application) {
            $fields = ['hostname'=>'hybrid.test','recipe'=>'nginx_apache_php_fpm',
                'application'=>$application,'require_htaccess'=>true];
            $this->api($fields, $token);
            self::assertResponseIsSuccessful();
            $data = $this->data();
            self::assertSame('wcp360.candidate.application-preview/2', $data['schema']);
            self::assertSame('nginx_apache', $data['web_server']);
            self::assertSame('php_fpm', $data['application_server']);
            self::assertSame($application, $data['application']);
            self::assertTrue($data['htaccess_supported']);
            self::assertSame(['nginx','apache','php_fpm','htaccess'], array_keys($data['files']));
            self::assertSame('single_site_loopback', $data['scope']);
            self::assertSame('not_reserved', $data['listener_allocation']);
            self::assertSame('disabled', $data['dynamic_page_cache']);
            foreach (['host_changes','persisted','deployment_available','performance_measured','worker_mode'] as $flag) {
                self::assertFalse($data[$flag]);
            }
            self::assertMatchesRegularExpression('/^[0-9a-f-]{14}7[0-9a-f-]{21}$/D', $data['preview_site_id']);
            self::assertNotContains($data['preview_site_id'], $identities);
            $identities[] = $data['preview_site_id'];
            self::assertStringContainsString('127.0.0.1:8082', $data['files']['nginx']['content']);
            self::assertStringContainsString('127.0.0.1:8083', $data['files']['apache']['content']);
            self::assertSame(['not_run','not_run','not_run','not_run'],
                array_column($data['syntax_checks']['checks'], 'status'));
            self::assertFalse($data['syntax_checks']['activation_authorized']);
            self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));

            $this->browser->request('POST', $this->url(self::FORM), ['_token'=>$token,
                'hostname'=>'hybrid-browser.test','recipe'=>'nginx_apache_php_fpm',
                'application'=>$application,'require_htaccess'=>'1','check_syntax'=>'1']);
            self::assertResponseIsSuccessful();
            self::assertSelectorCount(4, '[data-application-artifact]');
            foreach (['nginx','apache','php_fpm','htaccess'] as $artifact) {
                self::assertSelectorExists('[data-application-artifact="'.$artifact.'"]');
            }
            self::assertSelectorTextContains('[data-application-topology]', 'Apache');
            self::assertSelectorTextContains('[data-application-topology]', '127.0.0.1:8082');
            self::assertSelectorTextContains('[data-application-topology]', '127.0.0.1:8083');
            self::assertSelectorTextContains('[data-application-htaccess]', 'natif');
            self::assertSelectorCount(4, '[data-syntax-artifact]');
            self::assertSelectorExists('[data-syntax-artifact="apache"][data-syntax-status="not_configured"]');
            self::assertSelectorExists('[data-syntax-artifact="php_fpm"][data-syntax-status="not_run"]');
            self::assertSelectorExists('[data-syntax-artifact="htaccess"][data-syntax-status="not_run"]');
            self::assertStringContainsString('&lt;Directory', (string) $this->browser->getResponse()->getContent());
            self::assertStringNotContainsString('<Directory ', (string) $this->browser->getResponse()->getContent());
            self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('private'));
            $browserId = $this->browser->getCrawler()->filter('[data-application-id]')->text();
            self::assertNotContains($browserId, $identities);
            $identities[] = $browserId;
        }
        self::assertSame($before, $this->state());
    }

    public function testAnonymousAndHostingUserCannotReadOrGenerateRootRecipes(): void
    {
        $before = $this->state();
        $this->browser->request('GET', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseStatusCodeSame(401);
        foreach (['nginx_php_fpm','nginx_apache_php_fpm'] as $recipe) {
            $this->api(['hostname'=>'app.test','recipe'=>$recipe,'check_syntax'=>true], 'forged');
            self::assertResponseStatusCodeSame(401);
        }
        self::assertSame($before, $this->state());
        $this->bootBrowser('user');
        $this->login('alice');
        $before = $this->state();
        $this->browser->request('GET', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseStatusCodeSame(403);
        foreach (['nginx_php_fpm','nginx_apache_php_fpm'] as $recipe) {
            $this->api(['hostname'=>'app.test','recipe'=>$recipe,'check_syntax'=>true], 'forged');
            self::assertResponseStatusCodeSame(403);
        }
        self::assertSame($before, $this->state());
    }

    public function testInvalidSelectorsHtaccessRequirementsAndResourceArgumentsNeverFallBack(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        foreach (['nginx_php_fpm','nginx_apache_php_fpm'] as $recipe) {
            $valid = ['hostname'=>'app.test','recipe'=>$recipe,'application'=>'php'];
            foreach ([
                [], array_replace($valid,['hostname'=>'example.com']), array_replace($valid,['recipe'=>'unknown']),
                array_replace($valid,['application'=>'worker']), array_replace($valid,['application'=>null]),
                array_replace($valid,['require_htaccess'=>1]), array_replace($valid,['require_htaccess'=>null]),
                $valid+['site_id'=>$this->fixture['tenants']['alice']], $valid+['actor'=>$this->fixture['users']['admin']],
                $valid+['path'=>'/private-application-input'], $valid+['port'=>443], $valid+['apply'=>true],
                $valid+['upstream_url'=>'http://127.0.0.1:8080/private-application-input'],
                $valid+['backend_url'=>'http://169.254.169.254/'],
                $valid+['check_syntax'=>true,'config'=>'server { }'], $valid+['check_syntax'=>true,'binary'=>'/bin/sh'],
            ] as $fields) {
                $this->api($fields, $token);
                self::assertResponseStatusCodeSame(400);
                self::assertStringNotContainsString('private-application-input', $this->browser->getResponse()->getContent());
            }
            $this->api($valid, $token, [], '?apply=1');
            self::assertResponseStatusCodeSame(400);
        }
        $this->api(['hostname'=>'app.test','recipe'=>'nginx_php_fpm','require_htaccess'=>true], $token);
        self::assertResponseStatusCodeSame(400);
        $this->browser->request('GET', $this->url('/workbench/v1/application/recipes?apply=1'));
        self::assertResponseStatusCodeSame(400);
        self::assertSame($before, $this->state());
    }

    public function testPreviewRequiresOwnCsrfExactOriginAndBoundedJson(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        foreach (['nginx_php_fpm','nginx_apache_php_fpm'] as $recipe) {
            $valid = ['hostname'=>'app.test','recipe'=>$recipe];
            foreach (['forged',''] as $bad) {
                $this->api($valid, $bad);
                self::assertResponseStatusCodeSame(403);
            }
            $this->api($valid, $token, ['HTTP_ORIGIN'=>'http://other.localhost']);
            self::assertResponseStatusCodeSame(403);
        }
        foreach (['{','null',str_repeat(' ',4097)] as $body) {
            $this->browser->request('POST', $this->url(self::API), [], [],
                ['CONTENT_TYPE'=>'application/json','HTTP_X_CSRF_TOKEN'=>$token], $body);
            self::assertResponseStatusCodeSame(400);
        }
        $this->api(['hostname'=>'app.test','recipe'=>'nginx_apache_php_fpm'], $token, ['CONTENT_TYPE'=>'text/plain']);
        self::assertResponseStatusCodeSame(400);
        $this->browser->request('GET', $this->url(self::FORM));
        self::assertResponseStatusCodeSame(405);
        self::assertSame($before, $this->state());
    }

    public function testStaleRootProofBlocksPreviewWithoutMakingMfaMandatory(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_password_proof');
        self::assertIsArray($proof);
        $proof['at'] = time()-301;
        $session->set('_wcp_password_proof', $proof);
        $session->save();
        foreach (['nginx_php_fpm','nginx_apache_php_fpm'] as $recipe) {
            $this->api(['hostname'=>'app.test','recipe'=>$recipe], $token);
            self::assertResponseStatusCodeSame(403);
        }
        $this->browser->request('GET', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseIsSuccessful();
        self::assertSame($before, $this->state());

        $crawler = $this->browser->request('GET', $this->url('/account/reauthenticate'));
        self::assertResponseIsSuccessful();
        $this->browser->submit($crawler->selectButton('Confirmer mon mot de passe')->form(['_password'=>WorkbenchFixture::PASSWORD]));
        for ($i=0; $i<3 && $this->browser->getResponse()->isRedirect(); ++$i) $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
        $token = $this->token();
        $before = $this->state();
        $this->api(['hostname'=>'app.test','recipe'=>'nginx_apache_php_fpm','require_htaccess'=>true], $token);
        self::assertResponseIsSuccessful();
        self::assertSame('wcp360.candidate.application-preview/2', $this->data()['schema']);
        self::assertSame($before, $this->state());
    }

    public function testOptedInBrowserAndApiChecksRemainPrivatePartialAndDoNotChangeState(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        $fields = ['hostname'=>'checked.test','recipe'=>'nginx_php_fpm','application'=>'wordpress'];
        $this->api($fields, $token);
        self::assertResponseIsSuccessful();
        self::assertFalse($this->data()['syntax_checks']['requested']);
        self::assertSame(['not_run','not_run'], array_column($this->data()['syntax_checks']['checks'], 'status'));
        $this->api($fields+['check_syntax'=>true], $token);
        self::assertResponseIsSuccessful();
        $data = $this->data();
        self::assertTrue($data['syntax_checks']['requested']);
        foreach (['coverage_complete','activation_authorized','runtime_validated'] as $flag) self::assertFalse($data['syntax_checks'][$flag]);
        $nginx = self::getContainer()->get(\Wcp360\Core\Module\Hosting\Infrastructure\NativeSyntaxTools::class)->binary('nginx');
        self::assertSame([$nginx === null ? 'not_configured' : 'passed', 'not_run'], array_column($data['syntax_checks']['checks'], 'status'));
        foreach ($data['syntax_checks']['checks'] as $check) self::assertSame(hash('sha256', $data['files'][$check['artifact']]['content']), $check['sha256']);
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));

        $this->browser->request('POST', $this->url(self::FORM), ['_token'=>$token,'hostname'=>'checked.test',
            'recipe'=>'apache_php_fpm','application'=>'wordpress','check_syntax'=>'1']);
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('[data-application-syntax]');
        self::assertSelectorCount(3, '[data-syntax-artifact]');
        self::assertSelectorExists('[data-syntax-artifact="apache"][data-syntax-status="not_configured"]');
        self::assertSelectorExists('[data-syntax-artifact="php_fpm"][data-syntax-status="not_run"]');
        self::assertSelectorExists('[data-syntax-artifact="htaccess"][data-syntax-status="not_run"]');
        self::assertSame($before, $this->state());
    }

    public function testNativeCheckOptInRejectsAmbiguousTypesAndCannotBypassOriginOrFreshProof(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        $fields = ['hostname'=>'checked.test','recipe'=>'nginx_php_fpm'];
        foreach ([null, 1, 0, [], 'true'] as $value) {
            $this->api($fields+['check_syntax'=>$value], $token);
            self::assertResponseStatusCodeSame(400);
        }
        $this->api($fields+['check_syntax'=>true], $token, ['HTTP_ORIGIN'=>'http://foreign.localhost']);
        self::assertResponseStatusCodeSame(403);
        $this->api($fields+['check_syntax'=>true], 'forged');
        self::assertResponseStatusCodeSame(403);
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_password_proof');
        self::assertIsArray($proof);
        $proof['at'] = time()-301;
        $session->set('_wcp_password_proof', $proof);
        $session->save();
        $this->api($fields+['check_syntax'=>true], $token);
        self::assertResponseStatusCodeSame(403);
        self::assertSame($before, $this->state());
    }

    public function testEnabledMfaStillRequiresCompletionForApplicationPreviews(): void
    {
        $mfa = self::getContainer()->get(CandidateMfa::class);
        $setup = $mfa->startEnrollment($this->fixture['users']['admin']);
        $result = $mfa->check($this->fixture['users']['admin'], TOTP::createFromSecret($setup['secret'])->now());
        self::assertNotNull($result);
        $this->login();
        $this->browser->request('POST', $this->url(self::FORM), ['_token'=>'pending',
            'hostname'=>'app.test','recipe'=>'nginx_apache_php_fpm','require_htaccess'=>'1']);
        self::assertResponseRedirects('/mfa');
        foreach (['nginx_php_fpm','nginx_apache_php_fpm'] as $recipe) {
            $this->api(['hostname'=>'app.test','recipe'=>$recipe,'check_syntax'=>true], 'pending');
            self::assertResponseStatusCodeSame(403);
        }
        $crawler = $this->browser->request('GET', $this->url('/mfa'));
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code'=>$result['recovery_codes'][0]]));
        self::assertResponseRedirects('/');
        $token = $this->token();
        $before = $this->state();
        foreach (['caddy_frankenphp','nginx_apache_php_fpm'] as $recipe) {
            $this->api(['hostname'=>'app.test','recipe'=>$recipe,'application'=>'wordpress','check_syntax'=>true], $token);
            self::assertResponseIsSuccessful();
        }
        self::assertSame('wcp360.candidate.application-preview/2', $this->data()['schema']);
        self::assertSame($before, $this->state());
    }
}
