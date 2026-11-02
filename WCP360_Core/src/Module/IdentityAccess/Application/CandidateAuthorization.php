<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Uid\Uuid;

/** Current candidate authority only; this does not implement the canonical permission registry. */
final readonly class CandidateAuthorization
{
    public const HOSTING_PLAN = 'candidate.hosting.plan';

    public function __construct(private Connection $db) {}

    public function allowsTenantPermission(string $actor, string $tenant, string $permission = self::HOSTING_PLAN): bool
    {
        if ($permission !== self::HOSTING_PLAN || !Uuid::isValid($actor) || !Uuid::isValid($tenant)) {
            return false;
        }

        return (bool) $this->db->fetchOne($this->eligibleGrants().' AND t.id = ?', [$actor, self::HOSTING_PLAN, $tenant]);
    }

    public function requireTenantPermission(string $actor, string $tenant, string $permission = self::HOSTING_PLAN): void
    {
        if (!$this->allowsTenantPermission($actor, $tenant, $permission)) {
            throw new AccessDeniedHttpException('Tenant permission required.');
        }
    }

    public function requireAdmin(string $actor, bool $lock = false): void
    {
        if (!Uuid::isValid($actor)) {
            throw new AccessDeniedHttpException('Current admin authority required.');
        }

        if ($lock && $this->db->getDatabasePlatform() instanceof SQLitePlatform) {
            // Reserve the SQLite writer before reading authority in the caller's transaction.
            $this->db->executeStatement("UPDATE wcp_candidate_users SET active = active WHERE id = ? AND context = 'admin' AND active = 1", [$actor]);
        }
        $suffix = $lock && $this->db->getDatabasePlatform() instanceof PostgreSQLPlatform ? ' FOR UPDATE' : '';
        if (!$this->db->fetchOne($this->adminScopeSql().$suffix, [$actor])) {
            throw new AccessDeniedHttpException('Current admin authority required.');
        }
    }

    /** Fixed current-administrator scope for internal read repositories; actor remains bound. */
    public function adminScopeSql(): string
    {
        return "SELECT id FROM wcp_candidate_users WHERE id = ? AND context = 'admin' AND active = 1";
    }

    /** @return list<array<string, mixed>> */
    public function tenantsForActor(string $actor): array
    {
        if (!Uuid::isValid($actor)) {
            return [];
        }

        return $this->db->fetchAllAssociative($this->eligibleGrants().' ORDER BY t.name, t.id', [$actor, self::HOSTING_PLAN]);
    }

    /**
     * Internal fixed selection query, bound by actor ID and HOSTING_PLAN parameters.
     * Read repositories compose this scope; no caller-supplied SQL or permission names.
     */
    public function tenantScopeSql(): string { return $this->eligibleGrants(); }

    /** One current-state rule is shared by visibility and execution checks. */
    private function eligibleGrants(): string
    {
        return "SELECT t.* FROM wcp_candidate_tenants t
            JOIN wcp_candidate_memberships m ON m.tenant_id = t.id
            JOIN wcp_candidate_users u ON u.id = m.user_id
            WHERE m.user_id = ? AND m.permission = ? AND u.context = 'user' AND u.active = 1
            AND NOT EXISTS (
                SELECT 1 FROM wcp_candidate_permission_denials d
                WHERE d.user_id = m.user_id AND d.tenant_id = m.tenant_id AND d.permission = m.permission
            )";
    }
}
