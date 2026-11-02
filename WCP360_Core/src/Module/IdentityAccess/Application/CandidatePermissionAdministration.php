<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

/** Administrator-controlled blocks for the existing development permission; never a grant API. */
final readonly class CandidatePermissionAdministration
{
    private const PAGE_SIZE = 100;

    public function __construct(private WorkbenchStore $store) {}

    /** @return array{items:list<array{user_id:string,email:string,active:bool,tenant_id:string,tenant_name:string,denied:bool}>,page:int,has_next:bool,selected_user:array{id:string,username:string}|null} */
    public function listing(string $admin, int $page = 1, ?string $user = null): array
    {
        $this->store->requireAdmin($admin);
        if ($page < 1 || $page > 10000) throw new \InvalidArgumentException('Invalid permission page.');
        $scope = (new CandidateAuthorization($this->store->db))->adminScopeSql();
        $selected = null;
        if ($user !== null) {
            $user = \Wcp360\Core\Shared\Http\CursorPageQuery::uuid($user);
            $row = $this->store->db->fetchAssociative("SELECT id,username FROM wcp_candidate_users WHERE id=? AND context='user' AND EXISTS (".$scope.')', [$user, $admin]);
            if ($row === false) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
            $selected = ['id' => (string) $row['id'], 'username' => (string) $row['username']];
        }
        $offset = ($page - 1) * self::PAGE_SIZE;
        $filter = $user === null ? '' : ' AND u.id=?';
        $params = [CandidateAuthorization::HOSTING_PLAN, $admin];
        if ($user !== null) $params[] = $user;
        $rows = $this->store->db->fetchAllAssociative(
            "SELECT u.id AS user_id, u.email, u.active, t.id AS tenant_id, t.name AS tenant_name,
                CASE WHEN d.user_id IS NULL THEN 0 ELSE 1 END AS denied
            FROM wcp_candidate_memberships m
            JOIN wcp_candidate_users u ON u.id = m.user_id AND u.context = 'user'
            JOIN wcp_candidate_tenants t ON t.id = m.tenant_id
            LEFT JOIN wcp_candidate_permission_denials d
                ON d.user_id = m.user_id AND d.tenant_id = m.tenant_id AND d.permission = m.permission
            WHERE m.permission = ? AND EXISTS (".$scope.')'.$filter."
            ORDER BY u.email, u.id, t.name, t.id
            LIMIT 101 OFFSET ".$offset, $params,
        );
        $hasNext = count($rows) > self::PAGE_SIZE;
        if ($hasNext) array_pop($rows);
        $items = array_map(static fn (array $row): array => [
            'user_id' => (string) $row['user_id'], 'email' => (string) $row['email'],
            'active' => (bool) $row['active'], 'tenant_id' => (string) $row['tenant_id'],
            'tenant_name' => (string) $row['tenant_name'], 'denied' => (bool) $row['denied'],
        ], $rows);
        $this->store->requireAdmin($admin);

        return ['items' => $items, 'page' => $page, 'has_next' => $hasNext, 'selected_user' => $selected];
    }

    /** Removing a block does not create or restore a membership. */
    public function setDenied(string $admin, string $actor, string $tenant, bool $denied): bool
    {
        if (!Uuid::isValid($admin) || !Uuid::isValid($actor) || !Uuid::isValid($tenant)) {
            throw new AccessDeniedHttpException('Candidate access change unavailable.');
        }

        return $this->store->db->transactional(function () use ($admin, $actor, $tenant, $denied): bool {
            $db = $this->store->db;
            $this->store->requireAdmin($admin, true);
            $lock = $db->getDatabasePlatform() instanceof PostgreSQLPlatform ? ' FOR UPDATE' : '';
            if (!$db->fetchOne("SELECT id FROM wcp_candidate_users WHERE id = ? AND context = 'user'".$lock, [$actor])) {
                throw new AccessDeniedHttpException('Candidate access change unavailable.');
            }
            if (!$db->fetchOne('SELECT id FROM wcp_candidate_tenants WHERE id = ?'.$lock, [$tenant])) {
                throw new AccessDeniedHttpException('Candidate access change unavailable.');
            }

            $keys = ['user_id' => $actor, 'tenant_id' => $tenant, 'permission' => CandidateAuthorization::HOSTING_PLAN];
            $exists = (bool) $db->fetchOne(
                'SELECT 1 FROM wcp_candidate_permission_denials WHERE user_id = ? AND tenant_id = ? AND permission = ?',
                [$actor, $tenant, CandidateAuthorization::HOSTING_PLAN],
            );
            if ($exists === $denied) {
                return false;
            }
            if ($denied) {
                $db->insert('wcp_candidate_permission_denials', $keys + ['created_by' => $admin, 'created_at' => WorkbenchStore::now()]);
            } else {
                $db->delete('wcp_candidate_permission_denials', $keys);
            }
            $this->store->audit($admin, $tenant, $denied ? 'candidate.access.denied' : 'candidate.access.restored', $actor);

            return true;
        });
    }
}
