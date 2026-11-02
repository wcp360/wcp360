<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Platform\Application\ServiceObservation;
use Wcp360\Core\Module\Platform\Application\ServiceOverview;
use Wcp360\Core\Module\Platform\Domain\ServiceObservationSnapshot;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ServiceJourneyTest extends WebTestCase
{
    private const PAGE = '/workbench/services';
    private const API = '/workbench/v1/services';
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private array $saved = [];
    private string $context = 'admin';
    /** @var \Closure():ServiceObservationSnapshot */
    private \Closure $reader;
    private int $collections = 0;

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

    private function bootBrowser(string $context = 'admin'): void
    {
        self::ensureKernelShutdown();
        $this->context = $context;
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $context;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $context.'.localhost';
        $this->browser = self::createClient([], ['HTTP_HOST'=>$context.'.localhost','HTTP_ORIGIN'=>'http://'.$context.'.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $name = $context === 'admin' ? 'admin' : 'alice';
        $this->store->db->update('wcp_candidate_users', ['username'=>'root'], ['id'=>$this->fixture['users'][$name]]);
        $this->source(static fn (): ServiceObservationSnapshot => self::observed());
        $port = new class(function (): ServiceObservationSnapshot {
            ++$this->collections;
            return ($this->reader)();
        }) implements ServiceObservation {
            /** @param \Closure():ServiceObservationSnapshot $read */
            public function __construct(private \Closure $read) {}
            public function read(): ServiceObservationSnapshot { return ($this->read)(); }
        };
        self::getContainer()->set(ServiceOverview::class, new ServiceOverview(new CandidateAuthorization($this->store->db), $port));
    }

    /** @param \Closure():ServiceObservationSnapshot $read */
    private function source(\Closure $read): void
    {
        $this->reader = $read;
        $this->collections = 0;
    }

    private static function observed(): ServiceObservationSnapshot
    {
        return ServiceObservationSnapshot::fromSystemdShow(
            "Id=nginx.service\nLoadState=loaded\nActiveState=active\nSubState=running\nUnitFileState=enabled\n\n".
            "Id=apache2.service\nLoadState=not-found\nActiveState=inactive\nSubState=dead\nUnitFileState=\n\n".
            "Id=postgresql.service\nLoadState=loaded\nActiveState=active\nSubState=exited\nUnitFileState=enabled\n"
        );
    }

    private function url(string $path): string { return 'http://'.$this->context.'.localhost'.$path; }

    private function login(): void
    {
        $crawler = $this->browser->request('GET', $this->url('/login'));
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username'=>'root','_password'=>WorkbenchFixture::PASSWORD]));
        for ($i=0; $i<3 && $this->browser->getResponse()->isRedirect(); ++$i) $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
    }

    private function data(): array { return json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR); }

    private function state(): array
    {
        $state = [];
        foreach (['sites','jobs','activations','audit','runtime_settings'] as $table) {
            $state[$table] = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_'.$table.' ORDER BY id');
        }
        return $state;
    }

    public function testAnonymousRequestsNeverCollectServiceObservations(): void
    {
        $before = $this->state();
        $this->browser->request('GET', $this->url(self::PAGE));
        self::assertResponseRedirects('/login');
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseStatusCodeSame(401);
        self::assertSame('AUTHENTICATION_REQUIRED', $this->data()['error']['code']);
        self::assertSame(0, $this->collections);
        self::assertSame($before, $this->state());
    }

    public function testRootCanUseTheSharedNavigationAndRefreshWithOptionalMfa(): void
    {
        $this->login();
        $before = $this->state();
        $crawler = $this->browser->request('GET', $this->url('/workbench/server'));
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('a[href="'.self::PAGE.'"]');
        $this->browser->click($crawler->selectLink('Services du serveur')->link());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('h1', 'Services du serveur');
        self::assertSelectorExists('a[aria-current="page"][href="'.self::PAGE.'"]');
        self::assertSelectorExists('#wcp-navigation-search a[href="'.self::PAGE.'"]');
        self::assertSelectorExists('[data-services-observed-at]');
        self::assertSelectorCount(9, '[data-service-id]');
        self::assertSelectorTextContains('[data-service-unit="postgresql.service"] [data-unit-active]', 'Actif');
        self::assertSelectorTextContains('[data-service-unit="postgresql.service"] [data-unit-sub]', 'Terminé');
        self::assertSelectorTextContains('[data-service-unit="apache2.service"] [data-unit-load]', 'Unité non trouvée');
        self::assertSelectorTextContains('[data-services-health]', 'ne prouve pas');
        self::assertSelectorNotExists('form[action="'.self::PAGE.'"]');
        $this->browser->click($this->browser->getCrawler()->selectLink('Actualiser')->link());
        self::assertResponseIsSuccessful();
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('private'));
        self::assertSame($before, $this->state());
    }

    public function testApiProjectsOnlyFixedServiceStateAndHeadHasNoBody(): void
    {
        $this->login();
        $before = $this->state();
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseIsSuccessful();
        $data = $this->data();
        self::assertSame(['schema','observed_at','scope','status','coverage_complete','control_available','health_verified','services'], array_keys($data));
        self::assertSame('wcp360.candidate.service-overview/1', $data['schema']);
        self::assertSame('current_process_namespace', $data['scope']);
        self::assertSame('observed', $data['status']);
        self::assertMatchesRegularExpression('/\A[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}Z\z/D', $data['observed_at']);
        foreach (['coverage_complete','control_available','health_verified'] as $flag) self::assertFalse($data[$flag]);
        self::assertSame(['nginx','apache','php_fpm','caddy','frankenphp','postgresql','mysql','redis','docker'],
            array_column($data['services'], 'id'));
        foreach ($data['services'] as $service) {
            self::assertSame(['id','label','owner','observation','units'], array_keys($service));
            foreach ($service['units'] as $unit) {
                self::assertSame(['id','load_state','active_state','sub_state','unit_file_state'], array_keys($unit));
            }
        }
        $services = array_column($data['services'], null, 'id');
        self::assertSame('observed', $services['nginx']['observation']);
        self::assertNull($services['apache']['units'][0]['unit_file_state']);
        self::assertSame('active', $services['postgresql']['units'][0]['active_state']);
        self::assertSame('exited', $services['postgresql']['units'][0]['sub_state']);
        self::assertSame('not_observed', $services['docker']['observation']);
        self::assertSame([], $services['docker']['units']);
        foreach (['ExecStart','Environment','StatusText','MainPID','FragmentPath','database_url',WorkbenchFixture::PASSWORD] as $private) {
            self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
        }
        foreach ([self::PAGE,self::API] as $path) {
            $this->browser->request('HEAD', $this->url($path));
            self::assertResponseIsSuccessful();
            self::assertSame('', $this->browser->getResponse()->getContent());
            self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
            self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('private'));
        }
        self::assertSame($before, $this->state());
    }

    public function testUnavailableCollectionNeverInventsInactiveServices(): void
    {
        $this->source(static fn (): ServiceObservationSnapshot => ServiceObservationSnapshot::notConfigured());
        $this->login();
        $before = $this->state();
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseIsSuccessful();
        self::assertSame('not_configured', $this->data()['status']);
        foreach ($this->data()['services'] as $service) {
            self::assertSame('unavailable', $service['observation']);
            self::assertSame([], $service['units']);
        }
        $this->browser->request('GET', $this->url(self::PAGE));
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(9, '[data-service-unavailable]');
        self::assertSelectorNotExists('[data-service-unit]');
        self::assertSelectorNotExists('[data-unit-active]');
        self::assertSelectorTextContains('[data-services-status]', 'Aucun état arrêté ou actif');
        self::assertSame($before, $this->state());
    }

    public function testQueryAndMethodOverridesNeverReachTheObservationPort(): void
    {
        $this->login();
        $before = $this->state();
        foreach ([self::PAGE,self::API] as $path) {
            foreach ([
                ['unit'=>'private-service-marker.service'], ['unit'=>['nginx.service']],
                ['property'=>'Environment'], ['command'=>'private-service-marker'], ['action'=>'restart'],
                ['path'=>'/private-service-marker'], ['host'=>'root@private-service-marker'],
                ['binary'=>'/bin/sh'], ['apply'=>'1'],
            ] as $query) {
                $this->browser->request('GET', $this->url($path.'?'.http_build_query($query)));
                self::assertResponseStatusCodeSame(400);
                self::assertStringNotContainsString('private-service-marker', $this->browser->getResponse()->getContent());
            }
            foreach (['POST','PUT','PATCH','DELETE'] as $method) {
                $this->browser->request($method, $this->url($path), ['action'=>'restart','unit'=>'nginx.service']);
                self::assertResponseStatusCodeSame(405);
            }
        }
        self::assertSame(0, $this->collections);
        self::assertSame($before, $this->state());
    }

    public function testAHostingUserNamedRootHasNoServiceNavigationOrAuthority(): void
    {
        $this->bootBrowser('user');
        $this->login();
        $before = $this->state();
        self::assertSelectorNotExists('a[href="'.self::PAGE.'"]');
        foreach ([self::PAGE,self::API] as $path) {
            $this->browser->request('GET', $this->url($path));
            self::assertResponseStatusCodeSame(403);
            self::assertStringNotContainsString('nginx.service', $this->browser->getResponse()->getContent());
        }
        self::assertSame(0, $this->collections);
        self::assertSame($before, $this->state());
    }

    public function testInactiveOrChangedAdministratorContextCannotKeepReadingServices(): void
    {
        foreach ([['active'=>0], ['context'=>'user']] as $change) {
            $this->bootBrowser();
            $this->login();
            $this->store->db->update('wcp_candidate_users', $change, ['id'=>$this->fixture['users']['admin']]);
            $before = $this->state();
            $this->browser->request('GET', $this->url(self::API));
            self::assertResponseStatusCodeSame(401);
            self::assertSame(0, $this->collections);
            self::assertSame($before, $this->state());
        }
    }

    public function testAnExpiredWriteProofDoesNotAddAReadReauthenticationGate(): void
    {
        $this->login();
        $session = $this->browser->getRequest()->getSession();
        $proof = $session->get('_wcp_password_proof');
        self::assertIsArray($proof);
        $proof['at'] = time()-301;
        $session->set('_wcp_password_proof', $proof);
        $session->save();
        $before = $this->state();
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseIsSuccessful();
        self::assertSame('observed', $this->data()['status']);
        self::assertSame($before, $this->state());
    }

    public function testEnabledMfaBlocksBrowserAndApiUntilItsFactorCompletes(): void
    {
        $mfa = self::getContainer()->get(CandidateMfa::class);
        $setup = $mfa->startEnrollment($this->fixture['users']['admin']);
        $factor = $mfa->check($this->fixture['users']['admin'], TOTP::createFromSecret($setup['secret'])->now());
        self::assertNotNull($factor);
        $this->login();
        self::assertSame('/mfa', $this->browser->getRequest()->getPathInfo());
        $this->browser->request('GET', $this->url(self::PAGE));
        self::assertResponseRedirects();
        self::assertContains($this->browser->getResponse()->headers->get('Location'), ['/mfa','http://admin.localhost/mfa']);
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseStatusCodeSame(403);
        self::assertSame('MFA_REQUIRED', $this->data()['error']['code']);
        self::assertSame(0, $this->collections);
        $crawler = $this->browser->request('GET', $this->url('/mfa'));
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code'=>$factor['recovery_codes'][0]]));
        self::assertResponseRedirects('/');
        $before = $this->state();
        $this->browser->request('GET', $this->url(self::PAGE));
        self::assertResponseIsSuccessful();
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseIsSuccessful();
        self::assertSame('observed', $this->data()['status']);
        self::assertSame($before, $this->state());
    }

    public function testFutureAsciiStatesRemainOpaqueWithoutPromotingHealth(): void
    {
        $this->source(static fn (): ServiceObservationSnapshot => ServiceObservationSnapshot::fromSystemdShow(
            "Id=nginx.service\nLoadState=loaded\nActiveState=future_active_state\nSubState=future_sub_state\nUnitFileState=\n"
        ));
        $this->login();
        $before = $this->state();
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseIsSuccessful();
        self::assertSame('observed', $this->data()['status']);
        self::assertFalse($this->data()['health_verified']);
        $services = array_column($this->data()['services'], null, 'id');
        self::assertSame('future_active_state', $services['nginx']['units'][0]['active_state']);
        $this->browser->request('GET', $this->url(self::PAGE));
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('[data-service-unit="nginx.service"] [data-unit-active]', 'future_active_state');
        self::assertSelectorTextContains('[data-service-unit="nginx.service"] [data-unit-sub]', 'future_sub_state');
        self::assertSelectorTextContains('[data-service-unit="nginx.service"] [data-unit-file]', 'Non disponible');
        self::assertSame($before, $this->state());
    }

    public function testUnsafeSourceAndProviderFailuresStayGenericWithoutPublishingDiagnostics(): void
    {
        $this->login();
        $before = $this->state();
        foreach ([
            static fn (): ServiceObservationSnapshot => ServiceObservationSnapshot::failed(),
            static fn (): ServiceObservationSnapshot => ServiceObservationSnapshot::fromSystemdShow(
                "Id=nginx.service\nLoadState=loaded\nActiveState=active\nSubState=<script>private-service-marker</script>\nUnitFileState=enabled\n"
            ),
            static function (): ServiceObservationSnapshot { throw new \RuntimeException('private-service-marker /etc/private-unit Environment=private-value'); },
        ] as $read) {
            $this->source($read);
            $this->browser->request('GET', $this->url(self::API));
            self::assertResponseIsSuccessful();
            self::assertSame('failed', $this->data()['status']);
            foreach ($this->data()['services'] as $service) self::assertSame([], $service['units']);
            self::assertStringNotContainsString('private-service-marker', $this->browser->getResponse()->getContent());
            $this->browser->request('GET', $this->url(self::PAGE));
            self::assertResponseIsSuccessful();
            self::assertSelectorTextContains('[data-services-status]', 'n’a pas abouti');
            self::assertSelectorNotExists('[data-service-unit]');
            self::assertStringNotContainsString('private-service-marker', $this->browser->getResponse()->getContent());
            self::assertStringNotContainsString('<script>private-service-marker</script>', $this->browser->getResponse()->getContent());
        }
        self::assertSame($before, $this->state());
    }

    public function testRevocationDuringCollectionPreventsPublishingAnyServiceObservation(): void
    {
        $this->login();
        $this->source(function (): ServiceObservationSnapshot {
            $this->store->db->update('wcp_candidate_users', ['active'=>0], ['id'=>$this->fixture['users']['admin']]);
            return self::observed();
        });
        $before = $this->state();
        $this->browser->request('GET', $this->url(self::API));
        self::assertResponseStatusCodeSame(403);
        self::assertSame(1, $this->collections);
        self::assertStringNotContainsString('nginx.service', $this->browser->getResponse()->getContent());
        self::assertSame($before, $this->state());
    }
}
