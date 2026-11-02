<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Wcp360\Core\Module\Hosting\Infrastructure\SiteRuntimeProfileSchema;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class SiteRuntimeProfileSchemaTest extends KernelTestCase
{
    /** @return iterable<string,array{bool}> */
    public static function engines(): iterable
    {
        yield 'private SQLite' => [true];
        yield 'configured disposable PostgreSQL' => [false];
    }

    #[DataProvider('engines')]
    public function testBackfillPreservesLegacyRowsRelationsAndAppendOnlyAudit(bool $sqlite): void
    {
        $db = $this->database($sqlite);
        try {
            $this->legacyRows($db);
            $before = $this->snapshot($db);
            $foreignKeys = $this->foreignKeys($db);
            SiteRuntimeProfileSchema::upgrade($db);

            self::assertSame($before, $this->snapshot($db));
            self::assertSame($foreignKeys, $this->foreignKeys($db));
            $rows = $db->fetchAllAssociative('SELECT application_server,web_stack,stack_revision FROM wcp_candidate_sites ORDER BY application_server');
            self::assertCount(2, $rows);
            self::assertSame('advanced', $rows[0]['web_stack']);
            self::assertSame('classic', $rows[1]['web_stack']);
            foreach ($rows as $row) self::assertSame(1, (int) $row['stack_revision']);

            $this->refused($db, static fn () => $db->executeStatement("UPDATE wcp_candidate_audit SET kind='tampered'"));
            $this->refused($db, static fn () => $db->executeStatement('DELETE FROM wcp_candidate_audit'));
            self::assertSame($before, $this->snapshot($db));
            if ($sqlite) self::assertSame(1, (int) $db->fetchOne('PRAGMA foreign_keys'));
        } finally {
            if ($sqlite) $db->close();
        }
    }

    #[DataProvider('engines')]
    public function testInsertAndUpdateRefuseUnknownMissingMismatchedOrMalformedMetadata(bool $sqlite): void
    {
        $db = $this->database($sqlite);
        try {
            $this->legacyRows($db);
            SiteRuntimeProfileSchema::upgrade($db);
            $base = $db->fetchAssociative("SELECT * FROM wcp_candidate_sites WHERE application_server='php_fpm'");
            self::assertIsArray($base);
            $before = $this->snapshot($db);
            $metadataBefore = $db->fetchAllAssociative('SELECT id,web_stack,stack_revision FROM wcp_candidate_sites ORDER BY id');
            $invalid = [
                'null profile' => ['web_stack' => null],
                'wrong engine pair' => ['web_stack' => 'advanced'],
                'modern is not a persisted profile' => ['web_stack' => 'modern'],
                'legacy is not a persisted profile' => ['web_stack' => 'legacy'],
                'unknown profile' => ['web_stack' => 'unknown'],
                'uppercase profile' => ['web_stack' => 'CLASSIC'],
                'nontext profile' => ['web_stack' => 7],
                'null revision' => ['stack_revision' => null],
                'zero revision' => ['stack_revision' => 0],
                'future revision' => ['stack_revision' => 2],
                'noninteger revision' => ['stack_revision' => 'not-an-integer'],
                'fractional revision' => ['stack_revision' => '1.5'],
                'changed engine without profile' => ['application_server' => 'frankenphp_worker', 'worker_acknowledged' => 1],
            ];
            foreach ($invalid as $label => $changes) {
                $new = array_replace($base, $changes, ['id' => WorkbenchStore::id(), 'hostname' => 'refused-'.bin2hex(random_bytes(6)).'.test']);
                $this->refused($db, static fn () => $db->insert('wcp_candidate_sites', $new), $label.' insert');
                $this->refused($db, static fn () => $db->update('wcp_candidate_sites', $changes, ['id' => $base['id']]), $label.' update');
                self::assertSame($before, $this->snapshot($db));
                self::assertSame($metadataBefore, $db->fetchAllAssociative('SELECT id,web_stack,stack_revision FROM wcp_candidate_sites ORDER BY id'));
            }
            $missing = array_replace($base, ['id' => WorkbenchStore::id(), 'hostname' => 'missing-profile.test']);
            unset($missing['web_stack']);
            $this->refused($db, static fn () => $db->insert('wcp_candidate_sites', $missing), 'missing profile');
            self::assertSame($before, $this->snapshot($db));

            // The revision intentionally has a fixed default, whereas web_stack never does.
            $withDefaultRevision = array_replace($base, ['id' => WorkbenchStore::id(), 'hostname' => 'default-revision.test']);
            unset($withDefaultRevision['stack_revision']);
            $db->insert('wcp_candidate_sites', $withDefaultRevision);
            self::assertSame(1, (int) $db->fetchOne('SELECT stack_revision FROM wcp_candidate_sites WHERE id=?', [$withDefaultRevision['id']]));
        } finally {
            if ($sqlite) $db->close();
        }
    }

    #[DataProvider('engines')]
    public function testForeignKeysStillRefuseForeignReferencesAndReferencedSiteDeletion(bool $sqlite): void
    {
        $db = $this->database($sqlite);
        try {
            $fixture = $this->legacyRows($db);
            $beforeKeys = $this->foreignKeys($db);
            SiteRuntimeProfileSchema::upgrade($db);
            $before = $this->snapshot($db);
            $site = $db->fetchAssociative("SELECT * FROM wcp_candidate_sites WHERE application_server='php_fpm'");
            self::assertIsArray($site);
            $orphan = array_replace($site, ['id' => WorkbenchStore::id(), 'tenant_id' => WorkbenchStore::id(), 'hostname' => 'orphan.test']);
            $this->refused($db, static fn () => $db->insert('wcp_candidate_sites', $orphan));
            $this->refused($db, static fn () => $db->delete('wcp_candidate_sites', ['id' => $site['id']]));
            $this->refused($db, static fn () => $db->executeStatement('UPDATE wcp_candidate_jobs SET tenant_id=? WHERE site_id=?', [$fixture['tenants']['bob'], $site['id']]));
            self::assertSame($beforeKeys, $this->foreignKeys($db));
            self::assertSame($before, $this->snapshot($db));
            if ($sqlite) self::assertSame(1, (int) $db->fetchOne('PRAGMA foreign_keys'));
        } finally {
            if ($sqlite) $db->close();
        }
    }

    #[DataProvider('engines')]
    public function testDuplicateUpgradeRefusesWithoutChangingExistingMetadataOrData(bool $sqlite): void
    {
        $db = $this->database($sqlite);
        try {
            $this->legacyRows($db);
            SiteRuntimeProfileSchema::upgrade($db);
            $before = $this->snapshot($db);
            $columns = array_keys($db->createSchemaManager()->listTableColumns('wcp_candidate_sites'));
            $profiles = $db->fetchAllAssociative('SELECT id,web_stack,stack_revision FROM wcp_candidate_sites ORDER BY id');
            $this->refused($db, static fn () => SiteRuntimeProfileSchema::upgrade($db));
            self::assertFalse($db->isTransactionActive());
            self::assertSame($before, $this->snapshot($db));
            self::assertSame($columns, array_keys($db->createSchemaManager()->listTableColumns('wcp_candidate_sites')));
            self::assertSame($profiles, $db->fetchAllAssociative('SELECT id,web_stack,stack_revision FROM wcp_candidate_sites ORDER BY id'));
        } finally {
            if ($sqlite) $db->close();
        }
    }

    #[DataProvider('engines')]
    public function testLateDdlFailureRollsBackAddedColumnsAndBackfillAtomically(bool $sqlite): void
    {
        $db = $this->database($sqlite);
        try {
            $this->legacyRows($db);
            // A deliberate fixture-only late name collision fails after ALTER and backfill.
            if ($sqlite) {
                $db->executeStatement('CREATE TRIGGER wcp_candidate_site_runtime_insert BEFORE UPDATE ON wcp_candidate_sites BEGIN SELECT 1; END');
            } else {
                $db->executeStatement('ALTER TABLE wcp_candidate_sites ADD CONSTRAINT wcp_candidate_site_runtime_profile CHECK (revision >= 1)');
            }
            $before = $this->snapshot($db);
            $beforeKeys = $this->foreignKeys($db);
            $columns = array_keys($db->createSchemaManager()->listTableColumns('wcp_candidate_sites'));
            $this->refused($db, static fn () => SiteRuntimeProfileSchema::upgrade($db));
            self::assertFalse($db->isTransactionActive());
            self::assertSame($before, $this->snapshot($db));
            self::assertSame($beforeKeys, $this->foreignKeys($db));
            self::assertSame($columns, array_keys($db->createSchemaManager()->listTableColumns('wcp_candidate_sites')));
            self::assertArrayNotHasKey('web_stack', $db->createSchemaManager()->listTableColumns('wcp_candidate_sites'));
            self::assertArrayNotHasKey('stack_revision', $db->createSchemaManager()->listTableColumns('wcp_candidate_sites'));
            $this->refused($db, static fn () => $db->executeStatement("UPDATE wcp_candidate_audit SET kind='tampered'"));
            self::assertSame($before, $this->snapshot($db));
        } finally {
            if ($sqlite) $db->close();
        }
    }

    public function testUnknownDatabasePlatformIsRefusedBeforeAnySchemaWrite(): void
    {
        $db = $this->createMock(Connection::class);
        $db->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $db->expects(self::never())->method('executeStatement');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unsupported candidate site runtime profile database.');
        SiteRuntimeProfileSchema::statements($db);
    }

    private function database(bool $sqlite): Connection
    {
        self::bootKernel();
        if ($sqlite) return DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $db = self::getContainer()->get(Connection::class);
        if (!$db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('Actual PostgreSQL migration checks require the Linux CI database.');
        }
        if (!getenv('WCP_TEST_DATABASE_URL') || $db->fetchOne('SELECT current_database()') !== 'wcp360_test') {
            throw new \RuntimeException('Schema fixtures require the explicit disposable wcp360_test database.');
        }
        return $db;
    }

    /** @return array<string,mixed> */
    private function legacyRows(Connection $db): array
    {
        $fixture = WorkbenchFixture::reset($db, siteProfiles: false);
        foreach (['php_fpm' => ['classic.test', 0, 3], 'frankenphp_worker' => ['advanced.test', 1, 2]] as $engine => [$host, $acknowledged, $revision]) {
            $site = WorkbenchStore::id();
            $job = WorkbenchStore::id();
            $db->insert('wcp_candidate_sites', [
                'id' => $site, 'tenant_id' => $fixture['tenants']['alice'], 'hostname' => $host,
                'state' => 'PLAN_READY', 'revision' => $revision, 'application_server' => $engine,
                'worker_acknowledged' => $acknowledged,
            ]);
            $result = $engine === 'php_fpm'
                ? ['schema' => 'wcp360.candidate.plan/1', 'nginx' => 'legacy-nginx', 'php_fpm' => 'legacy-pool']
                : ['schema' => 'wcp360.candidate.plan/2', 'nginx' => 'legacy-edge', 'application_server' => $engine, 'caddy' => 'legacy-caddy', 'systemd_unit' => 'legacy-unit'];
            $db->insert('wcp_candidate_jobs', [
                'id' => $job, 'tenant_id' => $fixture['tenants']['alice'], 'site_id' => $site,
                'actor_id' => $fixture['users']['alice'], 'idempotency_key' => 'legacy-'.$engine.'-job-key',
                'fingerprint' => str_repeat('a', 64), 'state' => 'SUCCEEDED',
                'result' => json_encode($result, JSON_THROW_ON_ERROR), 'created_at' => '2026-11-02T00:00:00Z',
                'started_at' => '2026-11-02T00:00:01Z', 'completed_at' => '2026-11-02T00:00:02Z',
            ]);
            (new WorkbenchStore($db))->audit($fixture['users']['alice'], $fixture['tenants']['alice'], 'candidate.hosting.plan.queued', $job);
        }
        return $fixture;
    }

    /** @return array<string,list<array<string,mixed>>> */
    private function snapshot(Connection $db): array
    {
        $queries = [
            'sites' => 'SELECT id,tenant_id,hostname,state,revision,application_server,worker_acknowledged FROM wcp_candidate_sites ORDER BY id',
            'jobs' => 'SELECT * FROM wcp_candidate_jobs ORDER BY id',
            'audit' => 'SELECT * FROM wcp_candidate_audit ORDER BY id',
            'memberships' => 'SELECT * FROM wcp_candidate_memberships ORDER BY user_id,tenant_id,permission',
            'tenants' => 'SELECT * FROM wcp_candidate_tenants ORDER BY id',
            'accounts' => 'SELECT * FROM wcp_candidate_accounts ORDER BY id',
            'users' => 'SELECT * FROM wcp_candidate_users ORDER BY id',
        ];
        $rows = [];
        foreach ($queries as $name => $query) $rows[$name] = $db->fetchAllAssociative($query);
        return $rows;
    }

    /** @return array<string,list<array{list<string>,string,list<string>}>> */
    private function foreignKeys(Connection $db): array
    {
        $result = [];
        foreach (['wcp_candidate_sites', 'wcp_candidate_jobs', 'wcp_candidate_memberships'] as $table) {
            $keys = [];
            foreach ($db->createSchemaManager()->listTableForeignKeys($table) as $foreignKey) {
                $keys[] = [$foreignKey->getLocalColumns(), $foreignKey->getForeignTableName(), $foreignKey->getForeignColumns()];
            }
            sort($keys);
            $result[$table] = $keys;
        }
        return $result;
    }

    private function refused(Connection $db, callable $operation, string $label = ''): void
    {
        try {
            $operation();
            self::fail('Database accepted an invalid site profile operation: '.$label);
        } catch (Exception) {
            self::assertFalse($db->isTransactionActive(), 'Refusal must leave this disposable fixture usable.');
        }
    }
}
