<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Http\RequestReference;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class PlanCorrelationJourneyTest extends WebTestCase
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

    private function login(string $name = 'alice'): string
    {
        $crawler = $this->browser->request('GET', 'http://user.localhost/login');
        $this->browser->submit($crawler->selectButton('Se connecter')->form(['_username' => $name, '_password' => WorkbenchFixture::PASSWORD]));
        self::assertResponseRedirects('/');
        $crawler = $this->browser->followRedirect();

        return $crawler->filter('form[action="/workbench/plans"] input[name="_token"]')->attr('value');
    }

    private function body(): array
    {
        return ['tenant_id' => $this->fixture['tenants']['alice'], 'hostname' => 'origin-reference.test', 'idempotency_key' => 'origin-request-key-2026'];
    }

    private function origin(string $job): ?string
    {
        $value = $this->store->db->fetchOne('SELECT request_id FROM wcp_candidate_jobs WHERE id=?', [$job]);

        return $value === null ? null : (string) $value;
    }

    public function testBrowserCreationStoresTheServerHeaderAndDetailKeepsItAcrossRequests(): void
    {
        $csrf = $this->login();
        $this->browser->request('POST', 'http://user.localhost/workbench/plans', ['_token' => $csrf, ...$this->body()], [], [
            'HTTP_X_WCP360_REQUEST_ID' => str_repeat('a', 32), 'HTTP_TRACEPARENT' => 'private-forged-trace',
        ]);
        self::assertResponseStatusCodeSame(303);
        $job = (string) $this->store->db->fetchOne('SELECT id FROM wcp_candidate_jobs');
        $header = $this->browser->getResponse()->headers->get(RequestReference::HEADER);
        self::assertNotSame(str_repeat('a', 32), $header);
        self::assertSame($header, $this->origin($job));
        self::assertSame($header, $this->store->db->fetchOne('SELECT request_id FROM wcp_candidate_audit WHERE resource_id=?', [$job]));
        $this->browser->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorTextSame('[data-origin-request-id]', $header);
        self::assertNotSame($header, $this->browser->getResponse()->headers->get(RequestReference::HEADER));
        self::assertStringNotContainsString('private-forged-trace', $this->browser->getResponse()->getContent());
        self::assertTrue($this->browser->getResponse()->headers->hasCacheControlDirective('no-store'));
    }

    public function testApiReplayHasAFreshHeaderButPreservesFirstOriginAndResponseShape(): void
    {
        $csrf = $this->login();
        $this->browser->jsonRequest('POST', 'http://user.localhost/workbench/v1/plans', $this->body(), ['HTTP_X_CSRF_TOKEN' => $csrf]);
        self::assertResponseStatusCodeSame(202);
        $first = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame(['job_id', 'replayed'], array_keys($first));
        $origin = $this->browser->getResponse()->headers->get(RequestReference::HEADER);
        $this->browser->jsonRequest('POST', 'http://user.localhost/workbench/v1/plans', $this->body(), [
            'HTTP_X_CSRF_TOKEN' => $csrf, 'HTTP_X_WCP360_REQUEST_ID' => $origin,
        ]);
        self::assertResponseStatusCodeSame(202);
        self::assertNotSame($origin, $this->browser->getResponse()->headers->get(RequestReference::HEADER));
        self::assertSame(['job_id' => $first['job_id'], 'replayed' => true], json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR));
        self::assertSame($origin, $this->origin($first['job_id']));
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit WHERE resource_id=?', [$first['job_id']]));
        $this->browser->request('GET', 'http://user.localhost/workbench/v1/jobs?tenant='.$this->fixture['tenants']['alice']);
        self::assertResponseIsSuccessful();
        self::assertStringNotContainsString('request_id', $this->browser->getResponse()->getContent());
    }

    public function testBodyMetadataCannotOverrideTheOriginOrCreateWork(): void
    {
        $csrf = $this->login();
        $this->browser->jsonRequest('POST', 'http://user.localhost/workbench/v1/plans', $this->body() + ['request_id' => 'private-forged-origin'], ['HTTP_X_CSRF_TOKEN' => $csrf]);
        self::assertResponseStatusCodeSame(400);
        self::assertStringNotContainsString('private-forged-origin', $this->browser->getResponse()->getContent());
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_jobs'));
    }

    public function testKnowingTheReferenceDoesNotAllowForeignJobAccess(): void
    {
        $csrf = $this->login();
        $this->browser->jsonRequest('POST', 'http://user.localhost/workbench/v1/plans', $this->body(), ['HTTP_X_CSRF_TOKEN' => $csrf]);
        self::assertResponseStatusCodeSame(202);
        $body = json_decode($this->browser->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $reference = $this->origin($body['job_id']);
        $this->browser->restart();
        $this->login('bob');
        $this->browser->request('GET', 'http://user.localhost/workbench/jobs/'.$body['job_id'], [], [], ['HTTP_X_WCP360_REQUEST_ID' => $reference]);
        self::assertResponseStatusCodeSame(403);
        self::assertSelectorNotExists('[data-origin-request-id]');
        self::assertStringNotContainsString($reference, $this->browser->getResponse()->getContent());
    }

    public function testLegacyTaskDoesNotInventAnOrigin(): void
    {
        $job = \Wcp360\Core\Tests\Fixtures\HostingJobsFixture::seed($this->store->db, $this->fixture, 1)[0];
        $this->login();
        $this->browser->request('GET', 'http://user.localhost/workbench/jobs/'.$job);
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('[data-origin-request-id]');
        self::assertSelectorTextContains('dl', 'Non disponible pour cette tâche antérieure');
        self::assertNull($this->origin($job));
    }
}
