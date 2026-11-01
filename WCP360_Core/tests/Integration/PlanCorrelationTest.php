<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Correlation\RequestId;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class PlanCorrelationTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    private function submit(?string $reference = null, array $extra = []): array
    {
        return self::getContainer()->get(SubmitHostingPlan::class)->submit($this->fixture['users']['alice'], [
            'tenant_id' => $this->fixture['tenants']['alice'], 'hostname' => 'correlation.test',
            'idempotency_key' => 'correlation-key-2026', ...$extra,
        ], $reference);
    }

    private function events(string $job): array
    {
        return $this->store->db->fetchAllAssociative('SELECT kind,request_id FROM wcp_candidate_audit WHERE resource_id=? ORDER BY id', [$job]);
    }

    public function testFreshWorkerInstanceRetainsTheOriginAcrossEverySuccessfulTransition(): void
    {
        $reference = str_repeat('a', 32);
        $job = $this->submit($reference)['job_id'];
        self::assertSame($reference, $this->store->db->fetchOne('SELECT request_id FROM wcp_candidate_jobs WHERE id=?', [$job]));
        // Reconstruct the worker use case: correlation must come from durable state, not the HTTP request or service memory.
        $worker = new RunHostingPlan(new WorkbenchStore($this->store->db),
            self::getContainer()->get(\Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer::class),
            self::getContainer()->get(\Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy::class));
        self::assertSame($job, $worker->runOne());
        self::assertSame([
            ['kind' => 'candidate.hosting.plan.queued', 'request_id' => $reference],
            ['kind' => 'candidate.hosting.plan.started', 'request_id' => $reference],
            ['kind' => 'candidate.hosting.plan.succeeded', 'request_id' => $reference],
        ], $this->events($job));
        self::assertSame('SUCCEEDED', $this->store->db->fetchOne('SELECT state FROM wcp_candidate_jobs WHERE id=?', [$job]));
    }

    public function testReplayKeepsTheFirstReferenceWithoutDuplicateAuditOrChangedIntent(): void
    {
        $job = $this->submit(str_repeat('b', 32));
        $replay = $this->submit(str_repeat('c', 32));
        self::assertSame(['job_id' => $job['job_id'], 'replayed' => true], $replay);
        self::assertSame([['kind' => 'candidate.hosting.plan.queued', 'request_id' => str_repeat('b', 32)]], $this->events($job['job_id']));
        self::assertSame(str_repeat('b', 32), $this->store->db->fetchOne('SELECT request_id FROM wcp_candidate_jobs'));
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_jobs'));
    }

    public function testNonHttpSubmissionGeneratesItsOwnReference(): void
    {
        $job = $this->submit()['job_id'];
        $reference = $this->store->db->fetchOne('SELECT request_id FROM wcp_candidate_jobs WHERE id=?', [$job]);
        self::assertTrue(RequestId::valid($reference));
        self::assertSame($reference, $this->events($job)[0]['request_id']);
    }

    public function testCallerPayloadCannotSupplyTheReference(): void
    {
        try { $this->submit(extra: ['request_id' => str_repeat('d', 32)]); self::fail('Caller metadata accepted.'); }
        catch (\Symfony\Component\HttpKernel\Exception\BadRequestHttpException $error) { self::assertSame(400, $error->getStatusCode()); }
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_jobs'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
    }

    public function testInvalidInternalReferenceFailsBeforeAnyWrite(): void
    {
        try { $this->submit('private-invalid-reference'); self::fail('Invalid internal metadata accepted.'); }
        catch (\InvalidArgumentException $error) { self::assertStringNotContainsString('private-invalid-reference', $error->getMessage()); }
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_sites'));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT count(*) FROM wcp_candidate_audit'));
    }

    public function testRevocationFailureRetainsOriginWithoutPublishingAResult(): void
    {
        $reference = str_repeat('e', 32);
        $job = $this->submit($reference)['job_id'];
        $this->store->db->delete('wcp_candidate_memberships', ['user_id' => $this->fixture['users']['alice']]);
        self::getContainer()->get(RunHostingPlan::class)->runOne();
        self::assertSame(['candidate.hosting.plan.queued', 'candidate.hosting.plan.started', 'candidate.hosting.plan.failed'], array_column($this->events($job), 'kind'));
        self::assertSame([$reference, $reference, $reference], array_column($this->events($job), 'request_id'));
        self::assertNull($this->store->db->fetchOne('SELECT result FROM wcp_candidate_jobs WHERE id=?', [$job]));
    }

    public function testInterruptedTaskRetainsOriginAndIsNotReplayedOrAuditedTwice(): void
    {
        $reference = str_repeat('f', 32);
        $job = $this->submit($reference)['job_id'];
        $this->store->db->update('wcp_candidate_jobs', ['state' => 'RUNNING'], ['id' => $job]);
        $worker = self::getContainer()->get(RunHostingPlan::class);
        self::assertSame(1, $worker->markInterrupted());
        self::assertSame(0, $worker->markInterrupted());
        self::assertNull($worker->runOne());
        self::assertSame([
            ['kind' => 'candidate.hosting.plan.queued', 'request_id' => $reference],
            ['kind' => 'candidate.hosting.plan.interrupted', 'request_id' => $reference],
        ], $this->events($job));
        self::assertSame('UNKNOWN_OUTCOME', $this->store->db->fetchOne('SELECT state FROM wcp_candidate_jobs WHERE id=?', [$job]));
    }

    public function testCorrelatedAuditFailureRollsBackJobAndSite(): void
    {
        $db = $this->store->db;
        $sqlite = $db->getDatabasePlatform() instanceof SQLitePlatform;
        $db->executeStatement($sqlite
            ? "CREATE TRIGGER reject_correlation BEFORE INSERT ON wcp_candidate_audit WHEN NEW.request_id IS NOT NULL BEGIN SELECT RAISE(ABORT,'correlated audit unavailable'); END"
            : 'ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_correlation CHECK (request_id IS NULL)');
        try {
            try { $this->submit(str_repeat('a', 32)); self::fail('Unrecorded submission accepted.'); }
            catch (Exception) {}
            foreach (['jobs', 'sites', 'audit'] as $table) {
                self::assertSame(0, (int) $db->fetchOne('SELECT count(*) FROM wcp_candidate_'.$table));
            }
        } finally {
            $db->executeStatement($sqlite ? 'DROP TRIGGER reject_correlation' : 'ALTER TABLE wcp_candidate_audit DROP CONSTRAINT reject_correlation');
        }
    }

    public function testLegacyWorkerTransitionsDoNotInventAReference(): void
    {
        $job = \Wcp360\Core\Tests\Fixtures\HostingJobsFixture::seed($this->store->db, $this->fixture, 1)[0];
        $this->store->db->update('wcp_candidate_jobs', ['state' => 'QUEUED'], ['id' => $job]);
        self::assertSame($job, self::getContainer()->get(RunHostingPlan::class)->runOne());
        self::assertSame([null, null], array_column($this->events($job), 'request_id'));
        self::assertNull($this->store->db->fetchOne('SELECT request_id FROM wcp_candidate_jobs WHERE id=?', [$job]));
    }

    public function testClaimAuditFailureRollsBackStart(): void
    {
        $job = $this->submit(str_repeat('a', 32))['job_id'];
        $this->rejectKind('candidate.hosting.plan.started');
        try {
            try { self::getContainer()->get(RunHostingPlan::class)->runOne(); self::fail('Unrecorded start accepted.'); }
            catch (Exception) {}
            self::assertSame('QUEUED', $this->store->db->fetchOne('SELECT state FROM wcp_candidate_jobs WHERE id=?', [$job]));
            self::assertSame([['kind' => 'candidate.hosting.plan.queued', 'request_id' => str_repeat('a', 32)]], $this->events($job));
        } finally {
            $this->removeRejection();
        }
    }

    public function testOutcomeAuditFailurePreservesRunningStateForExplicitReconciliation(): void
    {
        $job = $this->submit(str_repeat('a', 32))['job_id'];
        $worker = self::getContainer()->get(RunHostingPlan::class);
        $this->rejectKind('candidate.hosting.plan.succeeded');
        try {
            try { $worker->runOne(); self::fail('Unrecorded outcome accepted.'); }
            catch (Exception) {}
            self::assertSame('RUNNING', $this->store->db->fetchOne('SELECT state FROM wcp_candidate_jobs WHERE id=?', [$job]));
            self::assertNull($this->store->db->fetchOne('SELECT result FROM wcp_candidate_jobs WHERE id=?', [$job]));
            self::assertSame(['candidate.hosting.plan.queued', 'candidate.hosting.plan.started'], array_column($this->events($job), 'kind'));
        } finally {
            $this->removeRejection();
        }
        self::assertSame(1, $worker->markInterrupted());
        self::assertNull($worker->runOne());
        self::assertSame(['candidate.hosting.plan.queued', 'candidate.hosting.plan.started', 'candidate.hosting.plan.interrupted'], array_column($this->events($job), 'kind'));
        self::assertSame([str_repeat('a', 32), str_repeat('a', 32), str_repeat('a', 32)], array_column($this->events($job), 'request_id'));
    }

    private function rejectKind(string $kind): void
    {
        // Fixed test-owned events only; never expose an arbitrary SQL or trigger interface to the application.
        if (!in_array($kind, ['candidate.hosting.plan.started', 'candidate.hosting.plan.succeeded'], true)) {
            throw new \InvalidArgumentException('Unsupported fixture event.');
        }
        $db = $this->store->db;
        $db->executeStatement($db->getDatabasePlatform() instanceof SQLitePlatform
            ? "CREATE TRIGGER reject_transition BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind = '".$kind."' BEGIN SELECT RAISE(ABORT,'audit unavailable'); END"
            : "ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_transition CHECK (kind <> '".$kind."')");
    }

    private function removeRejection(): void
    {
        $db = $this->store->db;
        $db->executeStatement($db->getDatabasePlatform() instanceof SQLitePlatform
            ? 'DROP TRIGGER reject_transition' : 'ALTER TABLE wcp_candidate_audit DROP CONSTRAINT reject_transition');
    }

    public function testDatabaseRejectsMalformedMetadataAndAuditCorrelationCannotBeRewritten(): void
    {
        $job = $this->submit(str_repeat('a', 32))['job_id'];
        foreach (['', str_repeat('A', 32), str_repeat('0', 31), str_repeat('a', 32)."\n"] as $invalid) {
            try { $this->store->db->update('wcp_candidate_jobs', ['request_id' => $invalid], ['id' => $job]); self::fail('Malformed database reference accepted.'); }
            catch (Exception) {}
            self::assertSame(str_repeat('a', 32), $this->store->db->fetchOne('SELECT request_id FROM wcp_candidate_jobs WHERE id=?', [$job]));
        }
        try { $this->store->db->executeStatement('UPDATE wcp_candidate_audit SET request_id=? WHERE resource_id=?', [str_repeat('b', 32), $job]); self::fail('Audit correlation rewritten.'); }
        catch (Exception) {}
        self::assertSame(str_repeat('a', 32), $this->events($job)[0]['request_id']);
    }
}
