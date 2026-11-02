<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Operations\Infrastructure\HostingPlanQueue;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\HostingJobsFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class HostingPlanQueueTest extends KernelTestCase
{
    private const RESULT = '{"schema":"synthetic-queue-test-only"}';
    private WorkbenchStore $store;
    private HostingPlanQueue $queue;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->queue = new HostingPlanQueue($this->store);
    }

    private function submit(): string
    {
        return self::getContainer()->get(SubmitHostingPlan::class)->submit($this->fixture['users']['alice'], [
            'tenant_id' => $this->fixture['tenants']['alice'], 'hostname' => 'queue-transition.test',
            'idempotency_key' => 'queue-transition-key-2026',
        ], str_repeat('b', 32))['job_id'];
    }

    private function row(string $id): array
    {
        return $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_jobs WHERE id=?', [$id]);
    }

    private function events(string $id): array
    {
        return $this->store->db->fetchAllAssociative('SELECT kind,request_id FROM wcp_candidate_audit WHERE resource_id=? ORDER BY id', [$id]);
    }

    public function testClaimCommitsExactlyOneStartAndReturnsOnlyRequiredMetadata(): void
    {
        $id = $this->submit();
        $claim = $this->queue->claim();
        self::assertSame(['id', 'tenant_id', 'site_id', 'actor_id', 'request_id'], array_keys($claim));
        self::assertSame($id, $claim['id']);
        self::assertSame('RUNNING', $this->row($id)['state']);
        self::assertNotNull($this->row($id)['started_at']);
        self::assertFalse($this->store->db->isTransactionActive());
        self::assertNull($this->queue->claim());
        self::assertSame(['candidate.hosting.plan.queued', 'candidate.hosting.plan.started'], array_column($this->events($id), 'kind'));
    }

    public function testSuccessfulFinishIsAtomicAndCannotPublishOrAuditTwice(): void
    {
        $id = $this->submit();
        $this->queue->claim();
        self::assertTrue($this->queue->finish($id, self::RESULT));
        self::assertSame('SUCCEEDED', $this->row($id)['state']);
        self::assertSame(self::RESULT, $this->row($id)['result']);
        self::assertNotNull($this->row($id)['completed_at']);
        $site = $this->store->db->fetchAssociative('SELECT state,revision FROM wcp_candidate_sites');
        self::assertSame('PLAN_READY', $site['state']);
        self::assertSame(2, (int) $site['revision']);
        $audit = $this->events($id);
        self::assertFalse($this->queue->finish($id, null));
        self::assertSame($audit, $this->events($id));
        self::assertSame($site, $this->store->db->fetchAssociative('SELECT state,revision FROM wcp_candidate_sites'));
        self::assertSame([str_repeat('b',32), str_repeat('b',32), str_repeat('b',32)], array_column($audit, 'request_id'));
    }

    public function testMissingOrUnclaimedJobCannotBeCompleted(): void
    {
        $id = $this->submit();
        $before = $this->row($id);
        self::assertFalse($this->queue->finish(WorkbenchStore::id(), self::RESULT));
        self::assertFalse($this->queue->finish($id, self::RESULT));
        self::assertSame($before, $this->row($id));
        self::assertCount(1, $this->events($id));
    }

    public function testRevocationObservedAtPublicationDiscardsResultAndCommitsFailure(): void
    {
        $id = $this->submit();
        $this->queue->claim();
        self::getContainer()->get(CandidatePermissionAdministration::class)->setDenied(
            $this->fixture['users']['admin'], $this->fixture['users']['alice'], $this->fixture['tenants']['alice'], true);
        self::assertTrue($this->queue->finish($id, self::RESULT));
        self::assertSame('FAILED', $this->row($id)['state']);
        self::assertNull($this->row($id)['result']);
        self::assertNotNull($this->row($id)['completed_at']);
        self::assertSame('PLAN_FAILED', $this->store->db->fetchOne('SELECT state FROM wcp_candidate_sites'));
        self::assertSame('candidate.hosting.plan.failed', $this->events($id)[2]['kind']);
    }

    public function testInactiveActorCannotPublishARenderedPlan(): void
    {
        $id = $this->submit();
        $this->queue->claim();
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['alice']]);
        self::assertTrue($this->queue->finish($id, self::RESULT));
        self::assertSame('FAILED', $this->row($id)['state']);
        self::assertNull($this->row($id)['result']);
    }

    public function testRenderingFailureProducesOneFailedOutcome(): void
    {
        $id = $this->submit();
        $this->queue->claim();
        self::assertTrue($this->queue->finish($id, null));
        self::assertSame('FAILED', $this->row($id)['state']);
        self::assertSame('candidate.hosting.plan.failed', $this->events($id)[2]['kind']);
        self::assertFalse($this->queue->finish($id, self::RESULT));
    }

    public function testClaimAuditFailureLeavesTheJobQueued(): void
    {
        $id = $this->submit();
        $this->reject('candidate.hosting.plan.started');
        try {
            try { $this->queue->claim(); self::fail('Unrecorded claim accepted.'); } catch (Exception) {}
            self::assertSame('QUEUED', $this->row($id)['state']);
            self::assertNull($this->row($id)['started_at']);
            self::assertCount(1, $this->events($id));
        } finally {
            $this->removeRejection();
        }
    }

    public function testFinishAuditFailureRollsBackJobAndSiteBeforeExplicitRetry(): void
    {
        $id = $this->submit();
        $this->queue->claim();
        $before = $this->row($id);
        $site = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_sites');
        $this->reject('candidate.hosting.plan.succeeded');
        try {
            try { $this->queue->finish($id, self::RESULT); self::fail('Unrecorded outcome accepted.'); } catch (Exception) {}
            self::assertSame($before, $this->row($id));
            self::assertSame($site, $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_sites'));
            self::assertCount(2, $this->events($id));
        } finally {
            $this->removeRejection();
        }
        self::assertTrue($this->queue->finish($id, self::RESULT));
        self::assertCount(3, $this->events($id));
    }

    public function testInterruptionIsBoundedCountsActualChangesAndNeverRequeues(): void
    {
        $ids = HostingJobsFixture::seed($this->store->db, $this->fixture, 205);
        $this->store->db->executeStatement("UPDATE wcp_candidate_jobs SET state='RUNNING',result=NULL");
        $sites = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_sites ORDER BY id');
        self::assertSame(100, $this->queue->markInterrupted());
        self::assertSame(100, $this->queue->markInterrupted());
        self::assertSame(5, $this->queue->markInterrupted());
        self::assertSame(0, $this->queue->markInterrupted());
        self::assertSame(205, (int) $this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_jobs WHERE state='UNKNOWN_OUTCOME'"));
        self::assertSame(205, (int) $this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.hosting.plan.interrupted'"));
        self::assertSame($sites, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_sites ORDER BY id'));
        self::assertFalse($this->queue->finish($ids[0], self::RESULT));
        self::assertCount(1, $this->events($ids[0]));
        self::assertNull($this->queue->claim());
    }

    public function testInterruptionAuditFailureRollsBackTheEntireBatch(): void
    {
        HostingJobsFixture::seed($this->store->db, $this->fixture, 2);
        $this->store->db->executeStatement("UPDATE wcp_candidate_jobs SET state='RUNNING'");
        $this->reject('candidate.hosting.plan.interrupted');
        try {
            try { $this->queue->markInterrupted(); self::fail('Unrecorded recovery accepted.'); } catch (Exception) {}
            self::assertSame(2, (int) $this->store->db->fetchOne("SELECT count(*) FROM wcp_candidate_jobs WHERE state='RUNNING'"));
            self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
        } finally {
            $this->removeRejection();
        }
    }

    public function testPrivateSqliteSupportsTheSameTransitionsWithoutPostgresLockSyntax(): void
    {
        $db = \Doctrine\DBAL\DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        try {
            $fixture = WorkbenchFixture::reset($db);
            $ids = HostingJobsFixture::seed($db, $fixture, 2);
            $db->executeStatement("UPDATE wcp_candidate_jobs SET state='QUEUED',result=NULL");
            $queue = new HostingPlanQueue(new WorkbenchStore($db));
            self::assertSame($ids[0], $queue->claim()['id']);
            self::assertTrue($queue->finish($ids[0], self::RESULT));
            self::assertSame($ids[1], $queue->claim()['id']);
            self::assertSame(1, $queue->markInterrupted());
            self::assertSame(0, $queue->markInterrupted());
            self::assertFalse($queue->finish($ids[1], self::RESULT));
            self::assertSame(['SUCCEEDED', 'UNKNOWN_OUTCOME'], $db->fetchFirstColumn('SELECT state FROM wcp_candidate_jobs ORDER BY id'));
            self::assertSame(4, (int) $db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
        } finally {
            $db->close();
        }
    }

    public static function transitions(): iterable
    {
        yield 'claim' => ['claim'];
        yield 'finish' => ['finish'];
        yield 'recovery' => ['markInterrupted'];
    }

    #[DataProvider('transitions')]
    public function testCallerTransactionCannotBecomeAnUncommittedWorkerClaim(string $method): void
    {
        $id = $this->submit();
        $this->store->db->beginTransaction();
        try {
            try {
                if ($method === 'finish') $this->queue->finish($id, self::RESULT); else $this->queue->$method();
                self::fail('Caller transaction adopted.');
            } catch (\LogicException $error) {
                self::assertStringContainsString('own database transaction', $error->getMessage());
            }
            self::assertTrue($this->store->db->isTransactionActive());
        } finally {
            $this->store->db->rollBack();
        }
        self::assertSame('QUEUED', $this->row($id)['state']);
        self::assertCount(1, $this->events($id));
    }

    private function reject(string $kind): void
    {
        if (!in_array($kind, ['candidate.hosting.plan.started', 'candidate.hosting.plan.succeeded', 'candidate.hosting.plan.interrupted'], true)) {
            throw new \InvalidArgumentException('Unsupported fixture event.');
        }
        $db = $this->store->db;
        $db->executeStatement($db->getDatabasePlatform() instanceof SQLitePlatform
            ? "CREATE TRIGGER reject_queue BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind = '".$kind."' BEGIN SELECT RAISE(ABORT,'audit unavailable'); END"
            : "ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_queue CHECK (kind <> '".$kind."')");
    }

    private function removeRejection(): void
    {
        $db = $this->store->db;
        $db->executeStatement($db->getDatabasePlatform() instanceof SQLitePlatform
            ? 'DROP TRIGGER reject_queue' : 'ALTER TABLE wcp_candidate_audit DROP CONSTRAINT reject_queue');
    }
}
