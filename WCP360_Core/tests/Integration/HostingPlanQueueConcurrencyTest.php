<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wcp360\Core\Module\Operations\Infrastructure\HostingPlanQueue;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\HostingJobsFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

/** Concurrent real PostgreSQL transactions on disposable CI data; no process timing or sleep races. */
final class HostingPlanQueueConcurrencyTest extends KernelTestCase
{
    private Connection $db;
    private Connection $other;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->db = self::getContainer()->get(Connection::class);
        if (!$this->db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('Real row-lock checks run in PostgreSQL CI.');
        }
        if (!getenv('WCP_TEST_DATABASE_URL') || $this->db->fetchOne('SELECT current_database()') !== 'wcp360_test') {
            throw new \RuntimeException('Dedicated disposable PostgreSQL test database required.');
        }
        $this->fixture = WorkbenchFixture::reset($this->db);
        $this->other = DriverManager::getConnection($this->db->getParams());
        $this->other->executeStatement("SET lock_timeout='500ms'");
        $this->other->executeStatement("SET statement_timeout='2s'");
        self::assertNotSame($this->db->fetchOne('SELECT pg_backend_pid()'), $this->other->fetchOne('SELECT pg_backend_pid()'));
    }

    protected function tearDown(): void
    {
        if (isset($this->db) && $this->db->isTransactionActive()) $this->db->rollBack();
        if (isset($this->other)) $this->other->close();
        parent::tearDown();
    }

    public function testAnotherWorkerSkipsALockedClaimThenClaimsItOnceAfterRelease(): void
    {
        $ids = HostingJobsFixture::seed($this->db, $this->fixture, 2);
        $this->db->executeStatement("UPDATE wcp_candidate_jobs SET state='QUEUED',result=NULL");
        $this->db->beginTransaction();
        $this->db->fetchOne('SELECT id FROM wcp_candidate_jobs WHERE id=? FOR UPDATE', [$ids[0]]);
        $queue = new HostingPlanQueue(new WorkbenchStore($this->other));
        self::assertSame($ids[1], $queue->claim()['id']);
        self::assertNull($queue->claim());
        self::assertSame(0, (int) $this->other->fetchOne('SELECT count(*) FROM wcp_candidate_audit WHERE resource_id=?', [$ids[0]]));
        $this->db->commit();
        self::assertSame($ids[0], $queue->claim()['id']);
        self::assertNull($queue->claim());
        self::assertSame(2, (int) $this->other->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.hosting.plan.started'"));
    }

    public function testRecoverySkipsLockedRowsAndStalePublicationCannotOverwriteItsOutcome(): void
    {
        $ids = HostingJobsFixture::seed($this->db, $this->fixture, 2);
        $this->db->executeStatement("UPDATE wcp_candidate_jobs SET state='RUNNING',result=NULL");
        $this->db->beginTransaction();
        $this->db->fetchOne('SELECT id FROM wcp_candidate_jobs WHERE id=? FOR UPDATE', [$ids[0]]);
        $queue = new HostingPlanQueue(new WorkbenchStore($this->other));
        self::assertSame(1, $queue->markInterrupted());
        self::assertSame(0, $queue->markInterrupted());
        self::assertFalse($queue->finish($ids[1], '{"synthetic":"late-result"}'));
        self::assertSame('UNKNOWN_OUTCOME', $this->other->fetchOne('SELECT state FROM wcp_candidate_jobs WHERE id=?', [$ids[1]]));
        $this->db->commit();
        self::assertSame(1, $queue->markInterrupted());
        self::assertSame(0, $queue->markInterrupted());
        self::assertSame(2, (int) $this->other->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.hosting.plan.interrupted'"));
        self::assertSame(0, (int) $this->other->fetchOne("SELECT count(*) FROM wcp_candidate_audit WHERE kind='candidate.hosting.plan.succeeded'"));
    }

    public function testCompletionBeforeRecoveryLeavesOnlyTheAcceptedTerminalAudit(): void
    {
        $id = HostingJobsFixture::seed($this->db, $this->fixture, 1)[0];
        $first = new HostingPlanQueue(new WorkbenchStore($this->db));
        $second = new HostingPlanQueue(new WorkbenchStore($this->other));
        self::assertSame($id, $first->claim()['id']);
        self::assertTrue($first->finish($id, '{"synthetic":"completed"}'));
        self::assertSame(0, $second->markInterrupted());
        self::assertFalse($second->finish($id, null));
        self::assertSame(['candidate.hosting.plan.started', 'candidate.hosting.plan.succeeded'], $this->other->fetchFirstColumn(
            'SELECT kind FROM wcp_candidate_audit WHERE resource_id=? ORDER BY id', [$id]));
        self::assertSame('SUCCEEDED', $this->other->fetchOne('SELECT state FROM wcp_candidate_jobs WHERE id=?', [$id]));
    }
}
