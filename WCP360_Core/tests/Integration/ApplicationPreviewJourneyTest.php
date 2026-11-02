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

    public function testRootCanInspectSixRecipesAndThreeProfilesWithPrivateHead(): void
    {
        $this->login();
        $before = $this->state();
        $this->browser->request('GET', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseIsSuccessful();
        self::assertCount(6, $this->data()['recipes']);
        self::assertSame(['php','front_controller','wordpress'], array_column($this->data()['profiles'], 'id'));
        self::assertFalse($this->data()['deployment_available']);
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        $this->browser->request('HEAD', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseIsSuccessful();
        self::assertSame('', $this->browser->getResponse()->getContent());
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

    public function testAnonymousAndHostingUserCannotReadOrGenerateRootRecipes(): void
    {
        $this->browser->request('GET', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseStatusCodeSame(401);
        $this->api(['hostname'=>'app.test','recipe'=>'nginx_php_fpm','check_syntax'=>true], 'forged');
        self::assertResponseStatusCodeSame(401);
        $this->bootBrowser('user');
        $this->login('alice');
        $this->browser->request('GET', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseStatusCodeSame(403);
        $this->api(['hostname'=>'app.test','recipe'=>'nginx_php_fpm','check_syntax'=>true], 'forged');
        self::assertResponseStatusCodeSame(403);
    }

    public function testInvalidSelectorsHtaccessRequirementsAndResourceArgumentsNeverFallBack(): void
    {
        $this->login();
        $token = $this->token();
        $before = $this->state();
        $valid = ['hostname'=>'app.test','recipe'=>'nginx_php_fpm','application'=>'php'];
        foreach ([
            [], array_replace($valid,['hostname'=>'example.com']), array_replace($valid,['recipe'=>'unknown']),
            array_replace($valid,['application'=>'worker']), array_replace($valid,['application'=>null]),
            array_replace($valid,['require_htaccess'=>true]), array_replace($valid,['require_htaccess'=>1]),
            $valid+['site_id'=>$this->fixture['tenants']['alice']], $valid+['actor'=>$this->fixture['users']['admin']],
            $valid+['path'=>'/private-application-input'], $valid+['port'=>443], $valid+['apply'=>true],
            $valid+['check_syntax'=>true,'config'=>'server { }'], $valid+['check_syntax'=>true,'binary'=>'/bin/sh'],
        ] as $fields) {
            $this->api($fields, $token);
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-application-input', $this->browser->getResponse()->getContent());
        }
        $this->api($valid, $token, [], '?apply=1');
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
        $valid = ['hostname'=>'app.test','recipe'=>'nginx_php_fpm'];
        foreach (['forged',''] as $bad) {
            $this->api($valid, $bad);
            self::assertResponseStatusCodeSame(403);
        }
        $this->api($valid, $token, ['HTTP_ORIGIN'=>'http://other.localhost']);
        self::assertResponseStatusCodeSame(403);
        foreach (['{','null',str_repeat(' ',4097)] as $body) {
            $this->browser->request('POST', $this->url(self::API), [], [],
                ['CONTENT_TYPE'=>'application/json','HTTP_X_CSRF_TOKEN'=>$token], $body);
            self::assertResponseStatusCodeSame(400);
        }
        $this->browser->request('GET', $this->url(self::FORM));
        self::assertResponseStatusCodeSame(405);
        self::assertSame($before, $this->state());
    }

    public function testStaleRootProofBlocksPreviewWithoutMakingMfaMandatory(): void
    {
        $this->login();
        $token = $this->token();
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_password_proof');
        self::assertIsArray($proof);
        $proof['at'] = time()-301;
        $session->set('_wcp_password_proof', $proof);
        $session->save();
        $this->api(['hostname'=>'app.test','recipe'=>'nginx_php_fpm'], $token);
        self::assertResponseStatusCodeSame(403);
        $this->browser->request('GET', $this->url('/workbench/v1/application/recipes'));
        self::assertResponseIsSuccessful();
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
        $this->api(['hostname'=>'app.test','recipe'=>'nginx_php_fpm','check_syntax'=>true], 'pending');
        self::assertResponseStatusCodeSame(403);
        $crawler = $this->browser->request('GET', $this->url('/mfa'));
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code'=>$result['recovery_codes'][0]]));
        self::assertResponseRedirects('/');
        $token = $this->token();
        $this->api(['hostname'=>'app.test','recipe'=>'caddy_frankenphp','application'=>'wordpress','check_syntax'=>true], $token);
        self::assertResponseIsSuccessful();
    }
}
