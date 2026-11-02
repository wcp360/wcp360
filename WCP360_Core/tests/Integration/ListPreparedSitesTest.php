<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\DriverManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\Hosting\Application\ListPreparedSites;
use Wcp360\Core\Module\Hosting\Domain\SiteListQuery;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\HostingJobsFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ListPreparedSitesTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private ListPreparedSites $sites;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->sites = new ListPreparedSites($this->store->db, new CandidateAuthorization($this->store->db));
    }

    private function page(array $filters = [], string $actor = 'alice'): array
    {
        return $this->sites->page($this->fixture['users'][$actor],
            SiteListQuery::fromArray(array_replace(['tenant' => $this->fixture['tenants']['alice']], $filters)));
    }

    public function testAlphabeticalTraversalExceeds100RowsWithForwardAndBackwardCursors(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 120);
        $first = $this->page();
        $second = $this->page(['after' => $first['pagination']['next']]);
        $third = $this->page(['after' => $second['pagination']['next']]);
        self::assertSame(array_column($second['items'], 'id'), array_column($this->page(['before' => $third['pagination']['previous']])['items'], 'id'));
        $items = $first['items'];
        $cursor = $first['pagination']['next'];
        for ($pages = 0; $cursor !== null && $pages < 6; ++$pages) {
            $page = $this->page(['after' => $cursor]);
            $items = [...$items, ...$page['items']];
            $cursor = $page['pagination']['next'];
        }
        self::assertNull($cursor, 'Traversal must finish within the bounded fixture pages.');
        self::assertCount(120, $items);
        self::assertCount(120, array_unique(array_column($items, 'id')));
        $hosts = array_column($items, 'hostname');
        $sorted = $hosts;
        sort($sorted, SORT_STRING);
        self::assertSame($sorted, $hosts);
        self::assertNull($first['pagination']['previous']);
        $hundred = $this->page(['limit' => '100']);
        self::assertCount(100, $hundred['items']);
        $last = $this->page(['limit' => '100', 'after' => $hundred['pagination']['next']]);
        self::assertCount(20, $last['items']);
        self::assertNull($last['pagination']['next']);
    }

    public function testInsertionBeforeTheCursorDoesNotRepeatAlreadySeenSites(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 60);
        $first = $this->page();
        HostingJobsFixture::seed($this->store->db, $this->fixture, 1, 'alice', 1000);
        $this->store->db->executeStatement("UPDATE wcp_candidate_sites SET hostname='aaa-new.test' WHERE hostname='job-alice-1001.test'");
        $second = $this->page(['after' => $first['pagination']['next']]);
        self::assertSame([], array_intersect(array_column($first['items'], 'id'), array_column($second['items'], 'id')));
        self::assertNotContains('aaa-new.test', array_column($second['items'], 'hostname'));
        self::assertSame('aaa-new.test', $this->page()['items'][0]['hostname']);
    }

    public function testCombinedPreparationAndRuntimeFiltersUseOnlyMinimalReadOnlyProjection(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 6);
        HostingJobsFixture::seed($this->store->db, $this->fixture, 1, 'bob', 10000);
        $this->store->db->executeStatement("UPDATE wcp_candidate_sites SET application_server='frankenphp_worker',worker_acknowledged=1,state='PLAN_FAILED' WHERE hostname='job-alice-2.test'");
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_sites ORDER BY id');
        $jobs = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id');
        $audit = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        $page = $this->page(['q' => 'JOB-ALICE-2', 'state' => 'PLAN_FAILED', 'server' => 'frankenphp_worker']);
        self::assertCount(1, $page['items']);
        self::assertSame(['id', 'hostname', 'state', 'application_server', 'revision'], array_keys($page['items'][0]));
        self::assertSame('frankenphp_worker', $page['items'][0]['application_server']);
        self::assertSame(1, $page['items'][0]['revision']);
        foreach (['worker_acknowledged', 'tenant_id', 'fingerprint', 'result', 'request_id', 'job-bob'] as $private) {
            self::assertStringNotContainsString($private, json_encode($page, JSON_THROW_ON_ERROR));
        }
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_sites ORDER BY id'));
        self::assertSame($jobs, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id'));
        self::assertSame($audit, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
    }

    public function testForeignTenantAndUnavailablePivotsHaveSafeRefusals(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 30);
        HostingJobsFixture::seed($this->store->db, $this->fixture, 1, 'bob', 10000);
        $foreign = $this->store->db->fetchOne("SELECT id FROM wcp_candidate_sites WHERE hostname='job-bob-10001.test'");
        $first = $this->page()['items'][0];
        try { $this->page(['tenant' => $this->fixture['tenants']['bob']]); self::fail('Foreign tenant listed.'); }
        catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        foreach ([['after' => $foreign], ['after' => WorkbenchStore::id()],
            ['after' => $first['id'], 'state' => $first['state'] === 'PLAN_READY' ? 'PLAN_QUEUED' : 'PLAN_READY']] as $filters) {
            try { $this->page($filters); self::fail('Unavailable pivot accepted.'); }
            catch (BadRequestHttpException $error) { self::assertSame('Cursor unavailable in the current result. Start from the first page.', $error->getMessage()); }
        }
        self::assertCount(25, $this->page()['items']);
    }

    public function testRevocationInactiveOwnerAndWrongIdentitiesDenyInventory(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 1);
        self::getContainer()->get(CandidatePermissionAdministration::class)->setDenied(
            $this->fixture['users']['admin'], $this->fixture['users']['alice'], $this->fixture['tenants']['alice'], true);
        try { $this->page(); self::fail('Revoked grant retained.'); }
        catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        $this->store->db->delete('wcp_candidate_permission_denials', ['user_id' => $this->fixture['users']['alice']]);
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['alice']]);
        foreach (['alice', 'bob', 'admin'] as $name) {
            try { $this->page(actor: $name); self::fail('Unauthorized identity listed sites.'); }
            catch (AccessDeniedHttpException $error) { self::assertSame(403, $error->getStatusCode()); }
        }
    }

    public function testEmptyFilterHasNoInventedCursors(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 1);
        self::assertSame(['items' => [], 'pagination' => ['limit' => 25, 'next' => null, 'previous' => null]], $this->page(['q' => 'no-match']));
    }

    public function testPrivateSqliteUsesTheSameAuthorizedInventory(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        try {
            $fixture = WorkbenchFixture::reset($db);
            HostingJobsFixture::seed($db, $fixture, 30);
            $sites = new ListPreparedSites($db, new CandidateAuthorization($db));
            $query = ['tenant' => $fixture['tenants']['alice']];
            $first = $sites->page($fixture['users']['alice'], SiteListQuery::fromArray($query));
            self::assertCount(25, $first['items']);
            self::assertNotNull($first['pagination']['next']);
            $last = $sites->page($fixture['users']['alice'], SiteListQuery::fromArray($query + ['after' => $first['pagination']['next']]));
            self::assertCount(5, $last['items']);
            self::assertNull($last['pagination']['next']);
        } finally {
            $db->close();
        }
    }
}
