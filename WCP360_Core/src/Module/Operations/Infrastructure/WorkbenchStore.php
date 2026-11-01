<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Operations\Infrastructure;
use Doctrine\DBAL\Connection;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
final readonly class WorkbenchStore
{
    private CandidateAuthorization $authorization;
    public function __construct(public Connection $db)
    {
        if ($db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform) $db->executeStatement('PRAGMA foreign_keys = ON');
        $this->authorization = new CandidateAuthorization($db);
    }
    public static function id(): string { return Uuid::v7()->toRfc4122(); }
    public static function now(): string { return gmdate('Y-m-d\TH:i:s\Z'); }
    public function requirePermission(string $actor, string $tenant): void
    {
        $this->authorization->requireTenantPermission($actor, $tenant);
    }
    public function requireActivationGrant(string $actor,string $tenant):void
    {
        $this->requirePermission($actor,$tenant);
        if(!$this->db->fetchOne('SELECT 1 FROM wcp_candidate_activation_grants WHERE user_id=? AND tenant_id=?',[$actor,$tenant]))throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Explicit isolated-activation grant required.');
    }
    public function requireAdmin(string $actor, bool $lock=false): void
    {
        $this->authorization->requireAdmin($actor, $lock);
    }
    public function audit(?string $actor, ?string $tenant, string $kind, ?string $resource, ?string $requestId = null): void
    {
        \Wcp360\Core\Shared\Correlation\RequestId::assertValid($requestId);
        $this->db->insert('wcp_candidate_audit', ['id'=>self::id(),'actor_id'=>$actor,'tenant_id'=>$tenant,'kind'=>$kind,'resource_id'=>$resource,'created_at'=>self::now(),'request_id'=>$requestId]);
    }
    /** @return list<array<string, mixed>> */
    public function tenants(string $actor): array
    {
        return $this->authorization->tenantsForActor($actor);
    }
    /** @return list<array<string, mixed>> */
    public function jobs(string $actor, string $tenant): array
    {
        $page = (new \Wcp360\Core\Module\Operations\Application\ListHostingJobs($this->db, $this->authorization))
            ->page($actor, \Wcp360\Core\Module\Operations\Domain\JobListQuery::fromArray(['tenant' => $tenant, 'limit' => '100']));
        // Preserve the existing dashboard/plugin projection while sharing the query.
        return array_map(static fn (array $row): array => array_intersect_key($row,
            array_flip(['id', 'state', 'created_at', 'hostname', 'application_server'])), $page['items']);
    }
    /** @return array<string, mixed> */
    public function job(string $actor,string $id): array
    {
        $row=$this->db->fetchAssociative('SELECT j.*,s.application_server FROM wcp_candidate_jobs j JOIN wcp_candidate_sites s ON s.id=j.site_id AND s.tenant_id=j.tenant_id WHERE j.id=?',[$id]);
        if (!$row) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        $this->requirePermission($actor,$row['tenant_id']);
        return $row;
    }
}
