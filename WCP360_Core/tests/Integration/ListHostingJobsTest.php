<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Operations\Application\ListHostingJobs;
use Wcp360\Core\Module\Operations\Domain\JobListQuery;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\HostingJobsFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class ListHostingJobsTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private ListHostingJobs $jobs;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->jobs = new ListHostingJobs($this->store->db, new CandidateAuthorization($this->store->db));
    }

    private function page(array $filters = [], string $name = 'alice'): array
    {
        return $this->jobs->page($this->fixture['users'][$name], JobListQuery::fromArray(array_replace(
            ['tenant' => $this->fixture['tenants']['alice']], $filters)));
    }

    public function testBidirectionalPagesHaveNoOverlapEvenWithIdenticalTimestampsAndNewRows(): void
    {
        $ids = HostingJobsFixture::seed($this->store->db, $this->fixture, 60);
        $expected = array_reverse($ids);
        $first = $this->page();
        self::assertSame(array_slice($expected, 0, 25), array_column($first['items'], 'id'));
        self::assertNull($first['pagination']['previous']);
        HostingJobsFixture::seed($this->store->db, $this->fixture, 1, 'alice', 1000);
        $second = $this->page(['after' => $first['pagination']['next']]);
        self::assertSame(array_slice($expected, 25, 25), array_column($second['items'], 'id'));
        $last = $this->page(['after' => $second['pagination']['next']]);
        self::assertSame(array_slice($expected, 50), array_column($last['items'], 'id'));
        self::assertNull($last['pagination']['next']);
        $back = $this->page(['before' => $last['pagination']['previous']]);
        self::assertSame(array_column($second['items'], 'id'), array_column($back['items'], 'id'));
        self::assertCount(60, array_unique([...array_column($first['items'], 'id'), ...array_column($second['items'], 'id'), ...array_column($last['items'], 'id')]));
    }

    public function testStateAndHostnameFiltersReturnOnlyMinimalListData(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 60);
        HostingJobsFixture::seed($this->store->db, $this->fixture, 10, 'bob', 10000);
        $page = $this->page(['state' => 'SUCCEEDED', 'q' => 'job-alice-1']);
        self::assertCount(5, $page['items']);
        foreach ($page['items'] as $item) {
            self::assertSame('SUCCEEDED', $item['state']);
            self::assertStringContainsString('job-alice-1', $item['hostname']);
            self::assertSame(['id', 'state', 'created_at', 'started_at', 'completed_at', 'hostname', 'application_server'], array_keys($item));
        }
        foreach (['private_result_marker', 'fingerprint', 'idempotency_key', 'actor_id', 'job-bob'] as $private) {
            self::assertStringNotContainsString($private, json_encode($page, JSON_THROW_ON_ERROR));
        }
    }

    public function testForeignTenantAndForeignOrChangedFilterCursorsCannotExposeRows(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 30);
        $bob = HostingJobsFixture::seed($this->store->db, $this->fixture, 1, 'bob', 10000);
        try { $this->page(['tenant' => $this->fixture['tenants']['bob']]); self::fail('Foreign tenant was listed.'); }
        catch (AccessDeniedHttpException) {}
        foreach ([['after' => $bob[0]], ['after' => '00000000-0000-7000-8000-000000000030', 'state' => 'QUEUED']] as $filters) {
            try { $this->page($filters); self::fail('An out-of-scope cursor was accepted.'); }
            catch (BadRequestHttpException) {}
        }
        self::assertCount(25, $this->page()['items']);
    }

    public function testRevocationInactiveAccountAndAdministratorIdentityDenyReads(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 1);
        $this->store->db->insert('wcp_candidate_permission_denials', ['user_id' => $this->fixture['users']['alice'],
            'tenant_id' => $this->fixture['tenants']['alice'], 'permission' => CandidateAuthorization::HOSTING_PLAN,
            'created_by' => $this->fixture['users']['admin'], 'created_at' => WorkbenchStore::now()]);
        try { $this->page(); self::fail('Revoked authority was retained.'); } catch (AccessDeniedHttpException) {}
        $this->store->db->delete('wcp_candidate_permission_denials', ['user_id' => $this->fixture['users']['alice']]);
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['alice']]);
        foreach (['alice', 'admin', 'bob'] as $name) {
            try { $this->page(name: $name); self::fail('An unauthorized identity was accepted.'); } catch (AccessDeniedHttpException) {}
        }
    }

    public function testCompleteInventoryExceedsTheLegacyDashboardLimitWithoutChangingItsProjection(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 120);
        $legacy = $this->store->jobs($this->fixture['users']['alice'], $this->fixture['tenants']['alice']);
        self::assertCount(100, $legacy);
        self::assertSame(['id', 'state', 'created_at', 'hostname', 'application_server'], array_keys($legacy[0]));
        $first = $this->page(['limit' => '100']);
        $last = $this->page(['limit' => '100', 'after' => $first['pagination']['next']]);
        self::assertCount(100, $first['items']);
        self::assertCount(20, $last['items']);
        self::assertNull($last['pagination']['next']);
    }

    public function testReadDoesNotWriteJobStateOrAuditAndMissingPivotHasASafeRefusal(): void
    {
        $ids = HostingJobsFixture::seed($this->store->db, $this->fixture, 1);
        $before = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs');
        $audit = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit');
        self::assertCount(1, $this->page()['items']);
        self::assertSame($before, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs'));
        self::assertSame($audit, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit'));
        $this->store->db->delete('wcp_candidate_jobs', ['id' => $ids[0]]);
        $this->expectException(BadRequestHttpException::class);
        $this->page(['after' => $ids[0]]);
    }
}
