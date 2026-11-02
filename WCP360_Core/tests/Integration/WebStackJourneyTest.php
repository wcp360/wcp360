<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class WebStackJourneyTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private array $saved = [];

    protected function setUp(): void
    {
        foreach (['WCP_CONTEXT','WCP_PUBLIC_HOST','WCP_ALLOW_FRANKENPHP_PLANS'] as $name) {
            $this->saved[$name] = [$_ENV[$name] ?? null, $_SERVER[$name] ?? null];
        }
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

    private function bootBrowser(string $context, bool $worker = false): void
    {
        self::ensureKernelShutdown();
        $_ENV['WCP_CONTEXT'] = $_SERVER['WCP_CONTEXT'] = $context;
        $_ENV['WCP_PUBLIC_HOST'] = $_SERVER['WCP_PUBLIC_HOST'] = $context.'.localhost';
        $_ENV['WCP_ALLOW_FRANKENPHP_PLANS'] = $_SERVER['WCP_ALLOW_FRANKENPHP_PLANS'] = $worker ? '1' : '0';
        $this->browser = self::createClient([], ['HTTP_HOST' => $context.'.localhost', 'HTTP_ORIGIN' => 'http://'.$context.'.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function login(string $name = 'admin'): void
    {
        $host = $_ENV['WCP_CONTEXT'].'.localhost';
        $crawler = $this->browser->request('GET', 'http://'.$host.'/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => $name, '_password' => WorkbenchFixture::PASSWORD]));
        for ($i = 0; $i < 3 && $this->browser->getResponse()->isRedirect(); ++$i) $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
    }

    public function testRootSeesFourStacksWithoutAddingUnsupportedEngineValues(): void
    {
        $this->login();
        $this->browser->request('GET', 'http://admin.localhost/workbench/settings');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(4, '[data-web-stack]');
        self::assertSelectorTextContains('[data-web-stack="modern"]', 'À développer');
        self::assertSelectorTextContains('[data-web-stack="advanced"]', 'préparation des plans à autoriser');
        self::assertSelectorExists('option[value="php_fpm"]');
        self::assertSelectorExists('option[value="frankenphp_worker"][disabled]');
        self::assertSelectorNotExists('option[value="modern"]');
        self::assertSelectorNotExists('option[value="legacy"]');
    }

    public function testRootCatalogueApiIsReadOnlyPrivateAndUsesTheActualPlanningOptIn(): void
    {
        $this->login();
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/web-stacks');
        self::assertResponseIsSuccessful();
        $data = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame('wcp360.candidate.web-stacks/1', $data['schema']);
        self::assertSame(['classic','modern','legacy','advanced'], array_column($data['choices'], 'id'));
        self::assertFalse($data['choices'][3]['selectable_for_plan']);
        self::assertFalse($data['native_installation_available']);
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        foreach (['password_hash','database_url',WorkbenchFixture::PASSWORD] as $private) {
            self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
        }
        $this->browser->request('HEAD', 'http://admin.localhost/workbench/v1/web-stacks');
        self::assertResponseIsSuccessful();
        self::assertSame('', $this->browser->getResponse()->getContent());
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));

        $this->bootBrowser('admin', true);
        $this->login();
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/web-stacks');
        $enabled = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertTrue($enabled['choices'][3]['selectable_for_plan']);
        self::assertFalse($enabled['choices'][1]['selectable_for_plan']);
    }

    public function testAnonymousUserAndRevokedRootCannotUseRootCatalogueApi(): void
    {
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/web-stacks');
        self::assertResponseStatusCodeSame(401);
        $this->login();
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['admin']]);
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/web-stacks');
        self::assertResponseStatusCodeSame(401);
        $this->bootBrowser('user');
        $this->login('alice');
        $this->browser->request('GET', 'http://user.localhost/workbench/v1/web-stacks');
        self::assertResponseStatusCodeSame(403);
    }

    public function testQueryCannotEnableOrInstallAStackAndMethodsAreRestricted(): void
    {
        $this->login();
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        foreach (['worker_plans=1','stack=modern','command=private-stack-input'] as $query) {
            $this->browser->request('GET', 'http://admin.localhost/workbench/v1/web-stacks?'.$query);
            self::assertResponseStatusCodeSame(400);
            self::assertStringNotContainsString('private-stack-input', $this->browser->getResponse()->getContent());
        }
        $this->browser->request('POST', 'http://admin.localhost/workbench/v1/web-stacks');
        self::assertResponseStatusCodeSame(405);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testHostingFormSharesCatalogueButUnknownModernOrApacheValuesCannotCreatePlans(): void
    {
        $this->bootBrowser('user');
        $this->login('alice');
        $crawler = $this->browser->request('GET', 'http://user.localhost/');
        self::assertSelectorCount(4, '[data-web-stack]');
        self::assertSelectorTextContains('option[value="php_fpm"]', 'Classic');
        self::assertSelectorTextContains('option[value="frankenphp_worker"]', 'Mix avancé');
        $fields = $crawler->selectButton('Générer le plan')->form(['hostname' => 'stack.test'])->getValues();
        foreach (['modern','legacy','apache'] as $value) {
            $this->browser->request('POST', 'http://user.localhost/workbench/plans', array_replace($fields, ['application_server' => $value]));
            self::assertResponseStatusCodeSame(400);
        }
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_sites'));
    }

    public function testEnabledMfaProtectsTheRootCatalogueUntilSecondFactorCompletes(): void
    {
        $mfa = self::getContainer()->get(CandidateMfa::class);
        $setup = $mfa->startEnrollment($this->fixture['users']['admin']);
        $result = $mfa->check($this->fixture['users']['admin'], TOTP::createFromSecret($setup['secret'])->now());
        self::assertNotNull($result);
        $this->login();
        $this->browser->request('GET', 'http://admin.localhost/workbench/settings');
        self::assertResponseRedirects();
        self::assertContains($this->browser->getResponse()->headers->get('Location'), ['/mfa','http://admin.localhost/mfa']);
        $crawler = $this->browser->request('GET', 'http://admin.localhost/mfa');
        $this->browser->submit($crawler->selectButton('Valider la MFA')->form(['_auth_code' => $result['recovery_codes'][0]]));
        self::assertResponseRedirects('/');
        $this->browser->request('GET', 'http://admin.localhost/workbench/v1/web-stacks');
        self::assertResponseIsSuccessful();
        self::assertTrue($mfa->status($this->fixture['users']['admin'])['enabled']);
    }
}
