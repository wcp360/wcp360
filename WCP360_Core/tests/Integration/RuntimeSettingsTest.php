<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Platform\Application\ApplicationServerSettings;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class RuntimeSettingsTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
    }

    public function testRootProjectionContainsOnlyTheDefaultRevisionAndPlanningCapability(): void
    {
        $report = $this->settings()->inspect($this->fixture['users']['admin']);
        self::assertSame(['schema' => 'wcp360.candidate.runtime-settings/1',
            'settings' => ['application_server' => 'php_fpm', 'revision' => 1],
            'capabilities' => ['frankenphp_worker_plans' => false]], $report);
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        self::assertTrue($this->settings(true)->inspect($this->fixture['users']['admin'])['capabilities']['frankenphp_worker_plans']);
    }

    public function testUserMissingAndInactiveRootCannotInspectSettings(): void
    {
        foreach ([$this->fixture['users']['alice'], WorkbenchStore::id(), 'invalid'] as $actor) {
            try { $this->settings()->inspect($actor); self::fail('Unauthorized settings read.'); }
            catch (AccessDeniedHttpException) {}
        }
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $this->fixture['users']['admin']]);
        $this->expectException(AccessDeniedHttpException::class);
        $this->settings()->inspect($this->fixture['users']['admin']);
    }

    public function testSuccessfulChangePersistsTheServerGeneratedReferenceAndRejectsInvalidReferences(): void
    {
        $reference = str_repeat('d', 32);
        $this->settings()->change($this->fixture['users']['admin'], 'php_fpm', 1, $reference);
        self::assertSame(2, $this->settings()->current()['revision']);
        self::assertSame($reference, $this->store->db->fetchOne("SELECT request_id FROM wcp_candidate_audit WHERE kind='candidate.runtime.default.changed'"));
        try { $this->settings()->change($this->fixture['users']['admin'], 'php_fpm', 2, 'untrusted-ref'); self::fail('Invalid reference accepted.'); }
        catch (\InvalidArgumentException) {}
        self::assertSame(2, $this->settings()->current()['revision']);
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
    }

    public function testMandatoryAuditFailureRollsBackTheSettingAndItsRevision(): void
    {
        $sqlite = $this->store->db->getDatabasePlatform() instanceof SQLitePlatform;
        $this->store->db->executeStatement($sqlite
            ? "CREATE TRIGGER reject_runtime_audit BEFORE INSERT ON wcp_candidate_audit WHEN NEW.kind='candidate.runtime.default.changed' BEGIN SELECT RAISE(ABORT,'private-runtime-marker'); END"
            : "ALTER TABLE wcp_candidate_audit ADD CONSTRAINT reject_runtime_audit CHECK (kind <> 'candidate.runtime.default.changed')");
        try {
            try { $this->settings(true)->change($this->fixture['users']['admin'], 'frankenphp_worker', 1); self::fail('Unaudited runtime setting committed.'); }
            catch (\Doctrine\DBAL\Exception) {}
            self::assertSame(['application_server' => 'php_fpm', 'revision' => 1], $this->settings()->current());
            self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit'));
        } finally {
            $this->store->db->executeStatement($sqlite ? 'DROP TRIGGER reject_runtime_audit'
                : 'ALTER TABLE wcp_candidate_audit DROP CONSTRAINT reject_runtime_audit');
        }
    }

    public function testSqliteRevocationAfterReadPreventsPublication(): void
    {
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true, 'wrapperClass' => RuntimeSettingsInterleavingConnection::class]);
        self::assertInstanceOf(RuntimeSettingsInterleavingConnection::class, $db);
        try {
            $store = new WorkbenchStore($db);
            $fixture = WorkbenchFixture::reset($db);
            $settings = new ApplicationServerSettings($store, new ApplicationServerPolicy());
            self::assertSame(1, $settings->inspect($fixture['users']['admin'])['settings']['revision']);
            $db->afterSettingsRead = static function (Connection $db) use ($fixture): void {
                $db->update('wcp_candidate_users', ['active' => 0], ['id' => $fixture['users']['admin']]);
            };
            $this->expectException(AccessDeniedHttpException::class);
            $settings->inspect($fixture['users']['admin']);
        } finally { $db->close(); }
    }

    private function settings(bool $worker = false): ApplicationServerSettings
    {
        return new ApplicationServerSettings($this->store, new ApplicationServerPolicy($worker));
    }
}

final class RuntimeSettingsInterleavingConnection extends Connection
{
    public ?\Closure $afterSettingsRead = null;

    public function fetchAssociative(string $query, array $params = [], array $types = []): array|false
    {
        $row = parent::fetchAssociative($query, $params, $types);
        if ($this->afterSettingsRead !== null && str_starts_with($query, 'SELECT application_server,revision FROM wcp_candidate_runtime_settings')) {
            $callback = $this->afterSettingsRead;
            $this->afterSettingsRead = null;
            $callback($this);
        }
        return $row;
    }
}
