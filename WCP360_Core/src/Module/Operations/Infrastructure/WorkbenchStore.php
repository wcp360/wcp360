<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Operations\Infrastructure;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
final readonly class WorkbenchStore
{
    public function __construct(public Connection $db, private string $projectDir)
    {
        if ($db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform) $db->executeStatement('PRAGMA foreign_keys = ON');
    }
    public static function id(): string { return Uuid::v7()->toRfc4122(); }
    public static function now(): string { return gmdate('Y-m-d\TH:i:s\Z'); }
    public function requirePermission(string $actor, string $tenant): void
    {
        $allowed = $this->db->fetchOne("SELECT 1 FROM wcp_candidate_memberships m JOIN wcp_candidate_users u ON u.id=m.user_id WHERE m.user_id=? AND m.tenant_id=? AND m.permission='candidate.hosting.plan' AND u.context='user' AND u.active=1", [$actor,$tenant]);
        if (!$allowed) throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Tenant permission required.');
    }
    public function audit(?string $actor, ?string $tenant, string $kind, ?string $resource): void
    {
        $this->db->insert('wcp_candidate_audit', ['id'=>self::id(),'actor_id'=>$actor,'tenant_id'=>$tenant,'kind'=>$kind,'resource_id'=>$resource,'created_at'=>self::now()]);
    }
    public function tenants(string $actor): array
    {
        return $this->db->fetchAllAssociative('SELECT t.* FROM wcp_candidate_tenants t JOIN wcp_candidate_memberships m ON m.tenant_id=t.id WHERE m.user_id=? ORDER BY t.name,t.id',[$actor]);
    }
    public function jobs(string $actor, string $tenant): array
    {
        $this->requirePermission($actor,$tenant);
        return $this->db->fetchAllAssociative('SELECT j.id,j.state,j.created_at,s.hostname FROM wcp_candidate_jobs j JOIN wcp_candidate_sites s ON s.id=j.site_id AND s.tenant_id=j.tenant_id WHERE j.tenant_id=? ORDER BY j.created_at DESC,j.id DESC LIMIT 100',[$tenant]);
    }
    public function job(string $actor,string $id): array
    {
        $row=$this->db->fetchAssociative('SELECT * FROM wcp_candidate_jobs WHERE id=?',[$id]);
        if (!$row) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        $this->requirePermission($actor,$row['tenant_id']);
        return $row;
    }
}
