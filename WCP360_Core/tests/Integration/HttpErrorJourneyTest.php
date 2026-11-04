<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Http\RequestReference;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class HttpErrorJourneyTest extends WebTestCase
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
    }

    private function login(): string
    {
        $crawler = $this->browser->request('GET', 'http://user.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'alice', '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
        $crawler = $this->browser->followRedirect();

        return $crawler->filter('form[action="/workbench/plans"] input[name="_token"]')->attr('value');
    }

    private function post(array $body, string $csrf): void
    {
        $this->browser->jsonRequest('POST', 'http://user.localhost/workbench/v1/plans', $body, ['HTTP_X_CSRF_TOKEN' => $csrf]);
    }

    private function body(string $hostname = 'transport-reference.test'): array
    {
        return ['tenant_id' => $this->fixture['tenants']['alice'], 'hostname' => $hostname, 'idempotency_key' => 'http-reference-key-2026'];
    }

    private function failure(int $status, string $code): array
    {
        self::assertResponseStatusCodeSame($status);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        $body = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(['error', 'request_id'], array_keys($body));
        self::assertSame(['code', 'message'], array_keys($body['error']));
        self::assertSame($code, $body['error']['code']);
        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/D', $body['request_id']);
        self::assertSame($body['request_id'], $this->browser->getResponse()->headers->get(RequestReference::HEADER));
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));

        return $body;
    }

    public function testAnonymousApiGetsJson401WhileBrowserStillUsesLogin(): void
    {
        $this->browser->jsonRequest('POST', 'http://user.localhost/workbench/v1/plans', $this->body());
        $this->failure(401, 'AUTHENTICATION_REQUIRED');
        self::assertFalse($this->browser->getResponse()->headers->has('Location'));
        $this->browser->request('GET', 'http://user.localhost/');
        self::assertResponseRedirects('/login');
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_jobs'));
    }

    public function testSuccessAndReplayStayUnchangedWhileConflictingIntentHasATypedError(): void
    {
        $csrf = $this->login();
        $this->post($this->body(), $csrf);
        self::assertResponseStatusCodeSame(202);
        $first = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(['job_id', 'replayed'], array_keys($first));
        self::assertFalse($first['replayed']);
        $reference = $this->browser->getResponse()->headers->get(RequestReference::HEADER);
        $this->post($this->body(), $csrf);
        self::assertResponseStatusCodeSame(202);
        self::assertTrue(json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR)['replayed']);
        self::assertNotSame($reference, $this->browser->getResponse()->headers->get(RequestReference::HEADER));
        $this->post($this->body('other-transport.test'), $csrf);
        $this->failure(409, 'IDEMPOTENCY_CONFLICT');
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_jobs'));
    }

    public function testAuthorizationAndValidationRefusalsCannotEchoInputOrChangeState(): void
    {
        $csrf = $this->login();
        $this->post($this->body() + ['private-input-marker' => '<script>private-input-marker</script>'], $csrf);
        $this->failure(400, 'VALIDATION_FAILED');
        self::assertStringNotContainsString('private-input-marker', $this->browser->getResponse()->getContent());
        $this->post(array_replace($this->body(), ['tenant_id' => $this->fixture['tenants']['bob']]), $csrf);
        $this->failure(403, 'AUTHORIZATION_DENIED');
        $this->post($this->body(), 'private-forged-csrf');
        $this->failure(403, 'AUTHORIZATION_DENIED');
        $this->browser->jsonRequest('POST', 'http://user.localhost/workbench/v1/plans', $this->body(), ['HTTP_X_CSRF_TOKEN' => $csrf, 'HTTP_ORIGIN' => 'http://outside.example']);
        $this->failure(403, 'AUTHORIZATION_DENIED');
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_jobs'));
    }

    public function testRoutingAndHostErrorsHaveSafeFormatsAndPreserveAllowHeader(): void
    {
        $this->browser->request('GET', 'http://user.localhost/workbench/v1/plans');
        $this->failure(405, 'VALIDATION_FAILED');
        self::assertStringContainsString('POST', $this->browser->getResponse()->headers->get('Allow'));
        $this->browser->request('GET', 'http://user.localhost/workbench/v1/private-unknown-marker');
        $this->failure(404, 'RESOURCE_NOT_FOUND');
        self::assertStringNotContainsString('private-unknown-marker', $this->browser->getResponse()->getContent());
        $this->browser->request('POST', 'http://admin.localhost/workbench/v1/plans', ['password' => 'private-credential-marker']);
        $this->failure(403, 'AUTHORIZATION_DENIED');
        self::assertStringNotContainsString('private-credential-marker', $this->browser->getResponse()->getContent());
    }

    public function testRejectedHostErrorPageNonceMatchesFinalCsp(): void
    {
        // Wrong-surface rejection happens in the request listener before its
        // ordinary nonce assignment; Twig still needs the nonce before render.
        $this->browser->request('GET', 'http://admin.localhost/private-error-marker');
        self::assertResponseStatusCodeSame(403);
        self::assertSelectorExists('meta[name="csp-nonce"]');
        $nonce = $this->browser->getCrawler()->filter('meta[name="csp-nonce"]')->attr('content');
        self::assertIsString($nonce);
        self::assertMatchesRegularExpression('/^[A-Za-z0-9+\\/]{24}$/D', $nonce);
        $csp = $this->browser->getResponse()->headers->get('Content-Security-Policy');
        self::assertIsString($csp);
        self::assertStringContainsString("'nonce-".$nonce."'", $csp);
        self::assertSame('DENY', $this->browser->getResponse()->headers->get('X-Frame-Options'));
        self::assertStringNotContainsString('private-error-marker', $this->browser->getResponse()->getContent());
    }

    public function testBrowserErrorsUseOfficialLayoutAndIgnoreInjectedTraceHeaders(): void
    {
        $this->browser->request('GET', 'http://user.localhost/private-page-marker?token=private-secret-marker', [], [], [
            'HTTP_ACCEPT' => 'application/json', 'HTTP_X_WCP360_REQUEST_ID' => 'private-secret-marker',
            'HTTP_X_REQUEST_ID' => 'private-secret-marker', 'HTTP_TRACEPARENT' => 'private-secret-marker',
        ]);
        self::assertResponseStatusCodeSame(404);
        self::assertSelectorExists('.auth-brand img');
        self::assertSelectorTextSame('h1', 'Page introuvable');
        $reference = trim($this->browser->getCrawler()->filter('[data-request-reference]')->text());
        self::assertSame($reference, $this->browser->getResponse()->headers->get(RequestReference::HEADER));
        self::assertStringNotContainsString('private-secret-marker', $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString('private-page-marker', $this->browser->getResponse()->getContent());
    }

    public function testRevokedSessionApiResponseCannotRedirectOrReturnTenantData(): void
    {
        $this->login();
        $this->store->db->executeStatement('UPDATE wcp_candidate_user_sessions SET revoked_at=last_seen_at WHERE user_id=?', [$this->fixture['users']['alice']]);
        $this->browser->request('GET', 'http://user.localhost/workbench/v1/summary/'.$this->fixture['tenants']['alice']);
        $this->failure(401, 'AUTHENTICATION_REQUIRED');
        self::assertFalse($this->browser->getResponse()->headers->has('Location'));
        self::assertStringNotContainsString($this->fixture['tenants']['alice'], $this->browser->getResponse()->getContent());
    }

    public function testAuditFailureRollsBackAndReturnsGenericJsonWithoutDriverDetails(): void
    {
        $csrf = $this->login();
        $sqlite = $this->store->db->getDatabasePlatform() instanceof SQLitePlatform;
        if ($sqlite) {
            $this->store->db->executeStatement("CREATE TRIGGER reject_http_audit BEFORE INSERT ON wcp_candidate_audit BEGIN SELECT RAISE(ABORT,'private-audit-marker'); END");
        } else {
            $this->store->db->executeStatement("ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_http_audit CHECK (kind <> 'candidate.hosting.plan.queued')");
        }
        try {
            $this->post($this->body(), $csrf);
            $this->failure(500, 'INTERNAL_ERROR');
            foreach (['private-audit-marker', 'wcp_candidate_audit', 'SQLSTATE', 'PDOException'] as $secret) {
                self::assertStringNotContainsString($secret, $this->browser->getResponse()->getContent());
            }
            self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_jobs'));
            self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_sites'));
        } finally {
            $this->store->db->executeStatement($sqlite ? 'DROP TRIGGER reject_http_audit' : 'ALTER TABLE wcp_candidate_audit DROP CONSTRAINT reject_http_audit');
        }
    }
}
