<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;

final class CandidateAuthorizationTest extends KernelTestCase
{
    private WorkbenchStore $store;
    private CandidateAuthorization $authorization;
    private CandidatePermissionAdministration $administration;
    private array $fixture;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->store = self::getContainer()->get(WorkbenchStore::class);
        $this->fixture = WorkbenchFixture::reset($this->store->db);
        $this->authorization = new CandidateAuthorization($this->store->db);
        $this->administration = new CandidatePermissionAdministration($this->store);
    }

    public function testOnlyTheExactSupportedCandidatePermissionCanAuthorize(): void
    {
        $actor = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        self::assertTrue($this->authorization->allowsTenantPermission($actor, $tenant));
        foreach (['', '*', 'candidate.*', 'candidate.hosting.plan.*', 'candidate.hosting.plan ', "candidate.hosting.plan\n", 'CANDIDATE.HOSTING.PLAN', 'applications.create', 'applications.read', 'iam.users.manage', 'settings.system.manage', 'servers.reboot', 'domains.record.update', 'dns.change.apply', 'dns.record.updated', 'nginx.reload', 'candidate.summary.read'] as $permission) {
            self::assertFalse($this->authorization->allowsTenantPermission($actor, $tenant, $permission), $permission);
            $this->denied(fn () => $this->authorization->requireTenantPermission($actor, $tenant, $permission));
        }
        self::assertSame(0, $this->auditCount());
    }

    public function testTenantVisibilityAndExecutionShareCurrentAuthority(): void
    {
        $actor = $this->fixture['users']['alice'];
        $own = $this->fixture['tenants']['alice'];
        $other = $this->fixture['tenants']['bob'];
        self::assertSame([$own], array_column($this->authorization->tenantsForActor($actor), 'id'));
        self::assertFalse($this->authorization->allowsTenantPermission($actor, $other));
        $this->denied(fn () => $this->store->requirePermission($actor, $other));

        self::assertTrue($this->administration->setDenied($this->fixture['users']['admin'], $actor, $own, true));
        self::assertFalse($this->authorization->allowsTenantPermission($actor, $own));
        self::assertSame([], $this->authorization->tenantsForActor($actor));
        self::assertSame([], $this->store->tenants($actor));
        $this->denied(fn () => $this->store->requirePermission($actor, $own));

        self::assertTrue($this->administration->setDenied($this->fixture['users']['admin'], $actor, $own, false));
        self::assertTrue($this->authorization->allowsTenantPermission($actor, $own));
        self::assertSame([$own], array_column($this->store->tenants($actor), 'id'));
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $actor]);
        self::assertFalse($this->authorization->allowsTenantPermission($actor, $own));
        self::assertSame([], $this->store->tenants($actor));
    }

    public function testAdministratorContextNeverProvidesAmbientTenantAuthority(): void
    {
        $admin = $this->fixture['users']['admin'];
        $tenant = $this->fixture['tenants']['alice'];
        $this->store->db->insert('wcp_candidate_memberships', ['user_id' => $admin, 'tenant_id' => $tenant, 'permission' => CandidateAuthorization::HOSTING_PLAN]);
        self::assertFalse($this->authorization->allowsTenantPermission($admin, $tenant));
        self::assertSame([], $this->authorization->tenantsForActor($admin));
        $this->denied(fn () => $this->authorization->requireTenantPermission($admin, $tenant));
        $this->authorization->requireAdmin($admin);
        $this->denied(fn () => $this->authorization->requireAdmin($this->fixture['users']['alice']));

        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $admin]);
        $this->denied(fn () => $this->authorization->requireAdmin($admin, true));
        self::assertSame(0, $this->auditCount());
    }

    public function testMalformedAndUnknownIdentifiersFailClosedBeforeTypedDatabaseLookups(): void
    {
        $actor = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        foreach (['', 'not-a-uuid', "' OR 1=1 --", WorkbenchStore::id()] as $invalid) {
            self::assertFalse($this->authorization->allowsTenantPermission($invalid, $tenant));
            self::assertFalse($this->authorization->allowsTenantPermission($actor, $invalid));
            self::assertSame([], $this->authorization->tenantsForActor($invalid));
            $this->denied(fn () => $this->authorization->requireAdmin($invalid));
            $this->denied(fn () => $this->administration->setDenied($this->fixture['users']['admin'], $invalid, $tenant, true));
            $this->denied(fn () => $this->administration->setDenied($this->fixture['users']['admin'], $actor, $invalid, true));
        }
        $this->denied(fn () => $this->administration->setDenied($this->fixture['users']['admin'], $this->fixture['users']['admin'], $tenant, true));
        self::assertSame(0, $this->denialCount());
        self::assertSame(0, $this->auditCount());
    }

    public function testBlockChangesAreIdempotentAndAuditOnlyActualTransitions(): void
    {
        $admin = $this->fixture['users']['admin'];
        $actor = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        self::assertFalse($this->administration->setDenied($admin, $actor, $tenant, false));
        self::assertTrue($this->administration->setDenied($admin, $actor, $tenant, true));
        $initial = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_permission_denials');
        self::assertSame($admin, $initial['created_by']);
        self::assertSame(CandidateAuthorization::HOSTING_PLAN, $initial['permission']);
        self::assertFalse($this->administration->setDenied($admin, $actor, $tenant, true));
        self::assertSame($initial, $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_permission_denials'));
        self::assertSame(1, $this->denialCount());
        self::assertSame(1, $this->auditCount());
        self::assertTrue($this->administration->setDenied($admin, $actor, $tenant, false));
        self::assertFalse($this->administration->setDenied($admin, $actor, $tenant, false));
        self::assertSame(0, $this->denialCount());
        $audit = $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id');
        self::assertSame(['candidate.access.denied', 'candidate.access.restored'], array_column($audit, 'kind'));
        self::assertSame([$admin, $admin], array_column($audit, 'actor_id'));
        self::assertSame([$tenant, $tenant], array_column($audit, 'tenant_id'));
        self::assertSame([$actor, $actor], array_column($audit, 'resource_id'));
        try {
            $this->store->db->executeStatement('UPDATE wcp_candidate_audit SET kind = ? WHERE id = ?', ['changed', $audit[0]['id']]);
            self::fail('Candidate audit was mutable.');
        } catch (Exception) {
            self::assertSame($audit, $this->store->db->fetchAllAssociative('SELECT * FROM wcp_candidate_audit ORDER BY id'));
        }
    }

    public function testRemovingABlockNeverCreatesMembershipOrCrossTenantAccess(): void
    {
        $actor = $this->fixture['users']['alice'];
        $other = $this->fixture['tenants']['bob'];
        $admin = $this->fixture['users']['admin'];
        self::assertTrue($this->administration->setDenied($admin, $actor, $other, true));
        self::assertFalse($this->authorization->allowsTenantPermission($actor, $other));
        self::assertTrue($this->administration->setDenied($admin, $actor, $other, false));
        self::assertFalse($this->authorization->allowsTenantPermission($actor, $other));
        self::assertSame(0, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_memberships WHERE user_id = ? AND tenant_id = ?', [$actor, $other]));
        self::assertTrue($this->authorization->allowsTenantPermission($actor, $this->fixture['tenants']['alice']));
    }

    public function testBlocksSurviveMembershipRevocationAndRegrant(): void
    {
        $actor = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        $membership = ['user_id' => $actor, 'tenant_id' => $tenant, 'permission' => CandidateAuthorization::HOSTING_PLAN];
        $this->administration->setDenied($this->fixture['users']['admin'], $actor, $tenant, true);
        $this->store->db->delete('wcp_candidate_memberships', $membership);
        self::assertSame(1, $this->denialCount());
        self::assertFalse($this->authorization->allowsTenantPermission($actor, $tenant));
        $this->store->db->insert('wcp_candidate_memberships', $membership);
        self::assertFalse($this->authorization->allowsTenantPermission($actor, $tenant));
        self::assertSame([], $this->authorization->tenantsForActor($actor));
        self::assertSame(1, $this->auditCount());
    }

    public function testWorkerRejectsAnExplicitBlockAddedAfterEnqueue(): void
    {
        $actor = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        $job = self::getContainer()->get(SubmitHostingPlan::class)->submit($actor, [
            'tenant_id' => $tenant,
            'hostname' => 'denied-worker.test',
            'idempotency_key' => 'denied-worker-000001',
        ]);
        $this->administration->setDenied($this->fixture['users']['admin'], $actor, $tenant, true);
        self::assertSame($job['job_id'], self::getContainer()->get(RunHostingPlan::class)->runOne());
        $row = $this->store->db->fetchAssociative('SELECT state, result FROM wcp_candidate_jobs WHERE id = ?', [$job['job_id']]);
        self::assertSame('FAILED', $row['state']);
        self::assertNull($row['result']);
        self::assertSame('PLAN_FAILED', $this->store->db->fetchOne('SELECT state FROM wcp_candidate_sites'));
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_memberships WHERE user_id = ? AND tenant_id = ?', [$actor, $tenant]));
        $this->denied(fn () => $this->store->job($actor, $job['job_id']));
        $this->denied(fn () => self::getContainer()->get(SubmitHostingPlan::class)->submit($actor, [
            'tenant_id' => $tenant,
            'hostname' => 'denied-next.test',
            'idempotency_key' => 'denied-worker-000002',
        ]));
        self::assertSame(1, (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_jobs'));
    }

    public function testCurrentAdministratorAuthorityIsRequiredForListingAndEveryWrite(): void
    {
        $admin = $this->fixture['users']['admin'];
        $actor = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        $this->denied(fn () => $this->administration->listing($actor));
        $this->denied(fn () => $this->administration->setDenied($actor, $actor, $tenant, true));
        $this->administration->setDenied($admin, $actor, $tenant, true);
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $admin]);
        $this->denied(fn () => $this->administration->listing($admin));
        $this->denied(fn () => $this->administration->setDenied($admin, $actor, $tenant, false));
        self::assertSame(1, $this->denialCount());
        self::assertSame(1, $this->auditCount());
        self::assertFalse($this->authorization->allowsTenantPermission($actor, $tenant));
    }

    public function testInactiveUserCanBeBlockedButCannotRecoverAuthorityByReactivation(): void
    {
        $actor = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        $this->store->db->update('wcp_candidate_users', ['active' => 0], ['id' => $actor]);
        self::assertTrue($this->administration->setDenied($this->fixture['users']['admin'], $actor, $tenant, true));
        $list = $this->administration->listing($this->fixture['users']['admin']);
        $row = array_values(array_filter($list['items'], static fn (array $item): bool => $item['user_id'] === $actor))[0];
        self::assertFalse($row['active']);
        self::assertTrue($row['denied']);
        $this->store->db->update('wcp_candidate_users', ['active' => 1], ['id' => $actor]);
        self::assertFalse($this->authorization->allowsTenantPermission($actor, $tenant));
    }

    public function testListingUsesBoundedStablePaginationAndExcludesAdminMemberships(): void
    {
        $db = $this->store->db;
        $actor = $this->fixture['users']['alice'];
        $admin = $this->fixture['users']['admin'];
        $own = $this->fixture['tenants']['alice'];
        $account = $db->fetchOne('SELECT account_id FROM wcp_candidate_tenants WHERE id = ?', [$own]);
        for ($i = 0; $i < 100; ++$i) {
            $tenant = WorkbenchStore::id();
            $db->insert('wcp_candidate_tenants', ['id' => $tenant, 'account_id' => $account, 'name' => sprintf('additional-%03d', $i)]);
            $db->insert('wcp_candidate_memberships', ['user_id' => $actor, 'tenant_id' => $tenant, 'permission' => CandidateAuthorization::HOSTING_PLAN]);
        }
        $db->insert('wcp_candidate_memberships', ['user_id' => $admin, 'tenant_id' => $own, 'permission' => CandidateAuthorization::HOSTING_PLAN]);
        $first = $this->administration->listing($admin);
        $second = $this->administration->listing($admin, 2);
        self::assertCount(100, $first['items']);
        self::assertTrue($first['has_next']);
        self::assertSame(1, $first['page']);
        self::assertCount(2, $second['items']);
        self::assertFalse($second['has_next']);
        self::assertSame(2, $second['page']);
        self::assertSame($first, $this->administration->listing($admin));
        $items = [...$first['items'], ...$second['items']];
        self::assertCount(102, array_unique(array_map(static fn (array $row): string => $row['user_id'].'/'.$row['tenant_id'], $items)));
        self::assertNotContains($admin, array_column($items, 'user_id'));
        self::assertSame(['items' => [], 'page' => 3, 'has_next' => false], $this->administration->listing($admin, 3));
        foreach ([0, -1, 10001, PHP_INT_MAX] as $invalidPage) {
            try {
                $this->administration->listing($admin, $invalidPage);
                self::fail('Invalid page was accepted.');
            } catch (\InvalidArgumentException) {
                self::assertSame(0, $this->auditCount());
            }
        }
    }

    public function testBlockAndAuditRollbackTogetherWhenAuditCannotBeAppended(): void
    {
        $actor = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        $this->rejectAuditInserts();
        try {
            $this->administration->setDenied($this->fixture['users']['admin'], $actor, $tenant, true);
            self::fail('Unaudited block was committed.');
        } catch (Exception) {
            self::assertSame(0, $this->denialCount());
            self::assertSame(0, $this->auditCount());
            self::assertTrue($this->authorization->allowsTenantPermission($actor, $tenant));
        } finally {
            $this->allowAuditInserts();
        }
    }

    public function testBlockRemovalRollsBackWhenAuditCannotBeAppended(): void
    {
        $actor = $this->fixture['users']['alice'];
        $tenant = $this->fixture['tenants']['alice'];
        $this->administration->setDenied($this->fixture['users']['admin'], $actor, $tenant, true);
        $this->rejectAuditInserts();
        try {
            $this->administration->setDenied($this->fixture['users']['admin'], $actor, $tenant, false);
            self::fail('Unaudited restoration was committed.');
        } catch (Exception) {
            self::assertSame(1, $this->denialCount());
            self::assertSame(1, $this->auditCount());
            self::assertFalse($this->authorization->allowsTenantPermission($actor, $tenant));
        } finally {
            $this->allowAuditInserts();
        }
    }

    private function denied(callable $operation): void
    {
        try {
            $operation();
            self::fail('Unauthorized operation was accepted.');
        } catch (AccessDeniedHttpException $error) {
            self::assertSame(403, $error->getStatusCode());
        }
    }

    private function denialCount(): int
    {
        return (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_permission_denials');
    }

    private function auditCount(): int
    {
        return (int) $this->store->db->fetchOne('SELECT COUNT(*) FROM wcp_candidate_audit');
    }

    private function rejectAuditInserts(): void
    {
        $db = $this->store->db;
        if ($db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $db->executeStatement("CREATE OR REPLACE FUNCTION wcp_test_access_audit_unavailable() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN RAISE EXCEPTION ''test audit unavailable''; END;'");
            $db->executeStatement('CREATE TRIGGER wcp_test_access_audit_unavailable BEFORE INSERT ON wcp_candidate_audit FOR EACH ROW EXECUTE FUNCTION wcp_test_access_audit_unavailable()');
        } else {
            $db->executeStatement("CREATE TRIGGER wcp_test_access_audit_unavailable BEFORE INSERT ON wcp_candidate_audit BEGIN SELECT RAISE(ABORT, 'test audit unavailable'); END");
        }
    }

    private function allowAuditInserts(): void
    {
        $db = $this->store->db;
        if ($db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $db->executeStatement('DROP TRIGGER wcp_test_access_audit_unavailable ON wcp_candidate_audit');
            $db->executeStatement('DROP FUNCTION wcp_test_access_audit_unavailable()');
        } else {
            $db->executeStatement('DROP TRIGGER wcp_test_access_audit_unavailable');
        }
    }
}
