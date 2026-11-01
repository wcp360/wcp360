<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wcp360\Core\Module\Operations\Infrastructure\PlanCorrelationSchema;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\HostingJobsFixture;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class PlanCorrelationSchemaTest extends KernelTestCase
{
    public static function engines(): iterable
    {
        yield 'private SQLite' => [true];
        yield 'configured CI database' => [false];
    }

    #[DataProvider('engines')]
    public function testUpgradePreservesLegacyRowsAndAppendOnlyAuditWhileConstrainingNewMetadata(bool $sqlite): void
    {
        self::bootKernel();
        $db = $sqlite ? DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true])
            : self::getContainer()->get(Connection::class);
        try {
            $fixture = WorkbenchFixture::reset($db);
            $job = HostingJobsFixture::seed($db, $fixture, 1)[0];
            (new WorkbenchStore($db))->audit($fixture['users']['alice'], $fixture['tenants']['alice'], 'candidate.hosting.plan.queued', $job);
            $jobs = $db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id');
            $audit = $db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
            // Recreate the immediately preceding schema, retaining synthetic old rows and immutable-audit triggers.
            $db->executeStatement('DROP INDEX wcp_candidate_audit_request');
            $db->executeStatement('ALTER TABLE wcp_candidate_jobs DROP COLUMN request_id');
            $db->executeStatement('ALTER TABLE wcp_candidate_audit DROP COLUMN request_id');
            foreach (PlanCorrelationSchema::statements($db) as $sql) $db->executeStatement($sql);
            self::assertSame($jobs, $db->fetchAllAssociative('SELECT * FROM wcp_candidate_jobs ORDER BY id'));
            self::assertSame($audit, $db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
            self::assertNull($db->fetchOne('SELECT request_id FROM wcp_candidate_jobs'));
            foreach (['', str_repeat('A', 32), str_repeat('0', 31), str_repeat('g', 32)] as $invalid) {
                try {
                    $db->insert('wcp_candidate_audit', ['id' => WorkbenchStore::id(), 'actor_id' => null,
                        'tenant_id' => null, 'kind' => 'candidate.test.reference', 'resource_id' => null,
                        'created_at' => WorkbenchStore::now(), 'request_id' => $invalid]);
                    self::fail('Malformed audit reference accepted by the database.');
                } catch (Exception) {}
                self::assertSame($audit, $db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
            }
            try { $db->executeStatement("UPDATE wcp_candidate_audit SET kind='tampered'"); self::fail('Audit immutability lost during upgrade.'); }
            catch (Exception) {}
            self::assertSame($audit, $db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
        } finally {
            if ($sqlite) $db->close();
        }
    }
}
