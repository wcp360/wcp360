<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class PlanDownloadJourneyTest extends WebTestCase
{
    private KernelBrowser $browser;
    private WorkbenchStore $store;
    private array $fixture;
    private string $job;

    protected function setUp(): void
    {
        $this->browser = self::createClient([], ['HTTP_HOST' => 'user.localhost', 'HTTP_ORIGIN' => 'http://user.localhost']);
        $this->browser->disableReboot();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->job = self::getContainer()->get(SubmitHostingPlan::class)->submit($this->fixture['users']['alice'], [
            'tenant_id' => $this->fixture['tenants']['alice'], 'hostname' => 'prepared-http.test',
            'idempotency_key' => 'prepared-http-key-2026',
        ])['job_id'];
        self::getContainer()->get(RunHostingPlan::class)->runOne();
    }

    private function login(string $name = 'alice'): void
    {
        $crawler = $this->browser->request('GET', 'http://user.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => $name, '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
        $this->browser->followRedirect();
    }

    private function url(string $artifact = 'nginx'): string
    {
        return 'http://user.localhost/workbench/jobs/'.$this->job.'/files/'.$artifact;
    }

    public function testAnonymousDownloadKeepsTheExistingBrowserLoginBoundary(): void
    {
        $this->browser->request('GET', $this->url());
        self::assertResponseRedirects('/login');
        self::assertFalse($this->browser->getResponse()->headers->has('Content-Disposition'));
    }

    public function testStructuredPreviewAndNativeDownloadReturnExactBoundedText(): void
    {
        $this->login();
        $original = json_decode($this->store->db->fetchOne('SELECT result FROM wcp_candidate_jobs WHERE id=?', [$this->job]), true, flags: JSON_THROW_ON_ERROR);
        $crawler = $this->browser->request('GET', 'http://user.localhost/workbench/jobs/'.$this->job);
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(2, '[data-plan-artifact]');
        self::assertSelectorExists('[data-plan-artifact="nginx"] a[data-turbo="false"]');
        self::assertSelectorExists('a[href="/"]');
        self::assertStringNotContainsString('idempotency_key', $this->browser->getResponse()->getContent());
        $this->browser->click($crawler->filter('[data-plan-artifact="nginx"] a')->link());
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'text/plain; charset=utf-8');
        self::assertResponseHeaderSame('X-Content-Type-Options', 'nosniff');
        self::assertResponseHeaderSame('Content-Length', (string) strlen($original['nginx']));
        self::assertSame($original['nginx'], $this->browser->getResponse()->getContent());
        self::assertStringContainsString('attachment;', $this->browser->getResponse()->headers->get('Content-Disposition'));
        self::assertStringContainsString('wcp360-'.$this->job.'-nginx.conf', $this->browser->getResponse()->headers->get('Content-Disposition'));
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
        $this->browser->request('HEAD', $this->url());
        self::assertResponseIsSuccessful();
        self::assertSame('', $this->browser->getResponse()->getContent());
        self::assertResponseHeaderSame('Content-Length', (string) strlen($original['nginx']));
    }

    public function testWrongArtifactsMalformedIdsAndUnsafeMethodsHaveNoDownloadFallback(): void
    {
        $this->login();
        foreach (['caddy', 'php_fpm', '..%2Fpasswd'] as $artifact) {
            $this->browser->request('GET', $this->url($artifact));
            self::assertResponseStatusCodeSame(404);
            self::assertFalse($this->browser->getResponse()->headers->has('Content-Disposition'));
        }
        $this->browser->request('GET', 'http://user.localhost/workbench/jobs/private-invalid-id/files/nginx');
        self::assertResponseStatusCodeSame(404);
        $this->browser->request('POST', $this->url());
        self::assertResponseStatusCodeSame(405);
    }

    public function testCurrentStateIsRecheckedAndNoPartialOrConditionalCacheIsServed(): void
    {
        $this->login();
        $this->browser->request('GET', $this->url(), [], [], ['HTTP_RANGE' => 'bytes=0-1', 'HTTP_IF_NONE_MATCH' => '"private-cache-tag"']);
        self::assertResponseStatusCodeSame(200);
        self::assertFalse($this->browser->getResponse()->headers->has('Content-Range'));
        $this->store->db->update('wcp_candidate_jobs', ['state' => 'UNKNOWN_OUTCOME'], ['id' => $this->job]);
        $this->browser->request('GET', $this->url());
        self::assertResponseStatusCodeSame(409);
        self::assertFalse($this->browser->getResponse()->headers->has('Content-Disposition'));
        self::assertStringNotContainsString('server_name prepared-http.test;', $this->browser->getResponse()->getContent());
    }

    public function testCorruptStoredOutputNeverBecomesARawPreviewOrAnAttachment(): void
    {
        $this->login();
        $this->store->db->update('wcp_candidate_jobs', ['result' => '{"private-corrupt-payload":"private-secret-marker"}'], ['id' => $this->job]);
        foreach ([$this->url(), 'http://user.localhost/workbench/jobs/'.$this->job] as $url) {
            $this->browser->request('GET', $url);
            self::assertResponseStatusCodeSame(503);
            self::assertFalse($this->browser->getResponse()->headers->has('Content-Disposition'));
            foreach (['private-corrupt-payload', 'private-secret-marker', 'JsonException', 'wcp_candidate_jobs'] as $private) {
                self::assertStringNotContainsString($private, $this->browser->getResponse()->getContent());
            }
        }
    }

    public function testTextIsEscapedInPreviewAndRemainsNonHtmlInDownload(): void
    {
        $this->login();
        $content = "<script>alert('private-preview-marker')</script>";
        $this->store->db->update('wcp_candidate_jobs', ['result' => json_encode([
            'schema' => 'wcp360.candidate.plan/1', 'nginx' => $content, 'php_fpm' => '[synthetic-pool]',
        ], JSON_THROW_ON_ERROR)], ['id' => $this->job]);
        $this->browser->request('GET', 'http://user.localhost/workbench/jobs/'.$this->job);
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('&lt;script&gt;', $this->browser->getResponse()->getContent());
        self::assertStringNotContainsString($content, $this->browser->getResponse()->getContent());
        $this->browser->request('GET', $this->url());
        self::assertResponseIsSuccessful();
        self::assertSame($content, $this->browser->getResponse()->getContent());
        self::assertResponseHeaderSame('Content-Type', 'text/plain; charset=utf-8');
        self::assertResponseHeaderSame('X-Content-Type-Options', 'nosniff');
        self::assertStringStartsWith('attachment;', $this->browser->getResponse()->headers->get('Content-Disposition'));
    }

    public function testOtherTenantAndRevokedGrantCannotDownloadAnExistingPlan(): void
    {
        $this->login('bob');
        $this->browser->request('GET', $this->url());
        self::assertResponseStatusCodeSame(403);
        self::assertFalse($this->browser->getResponse()->headers->has('Content-Disposition'));
        $this->browser->restart();
        $this->login();
        $this->browser->request('GET', $this->url());
        self::assertResponseIsSuccessful();
        self::getContainer()->get(CandidatePermissionAdministration::class)->setDenied(
            $this->fixture['users']['admin'], $this->fixture['users']['alice'], $this->fixture['tenants']['alice'], true);
        $this->browser->request('GET', $this->url());
        self::assertResponseStatusCodeSame(403);
        self::assertFalse($this->browser->getResponse()->headers->has('Content-Disposition'));
        self::assertStringNotContainsString('server_name prepared-http.test;', $this->browser->getResponse()->getContent());
    }

    public function testAdminRoleCannotUseUserDownloadsOnTheAdminSurface(): void
    {
        self::ensureKernelShutdown();
        $saved = [];
        foreach (['WCP_CONTEXT' => 'admin', 'WCP_PUBLIC_HOST' => 'admin.localhost'] as $name => $value) {
            $saved[$name] = [$_ENV[$name] ?? null, $_SERVER[$name] ?? null];
            $_ENV[$name] = $_SERVER[$name] = $value;
        }
        try {
            $this->browser = self::createClient([], ['HTTP_HOST' => 'admin.localhost', 'HTTP_ORIGIN' => 'http://admin.localhost']);
            $this->browser->disableReboot();
            $this->store = self::getContainer()->get(WorkbenchStore::class);
            WorkbenchFixture::reset($this->store->db);
            $crawler = $this->browser->request('GET', 'http://admin.localhost/login');
            $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => 'admin', '_password' => WorkbenchFixture::PASSWORD]));
            $this->browser->request('GET', 'http://admin.localhost/workbench/jobs/'.$this->job.'/files/nginx');
            self::assertResponseStatusCodeSame(403);
            self::assertFalse($this->browser->getResponse()->headers->has('Content-Disposition'));
        } finally {
            self::ensureKernelShutdown();
            foreach ($saved as $name => [$env, $server]) {
                if ($env === null) unset($_ENV[$name]); else $_ENV[$name] = $env;
                if ($server === null) unset($_SERVER[$name]); else $_SERVER[$name] = $server;
            }
        }
    }
}
