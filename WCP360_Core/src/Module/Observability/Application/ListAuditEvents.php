<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Observability\Application;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Observability\Domain\AuditListQuery;

/**
 * Candidate administrator metadata only; no payload/log access or read-side business audit.
 * @phpstan-type AuditRow array{id:string,actor_id:?string,tenant_id:?string,kind:string,resource_id:?string,created_at:string,request_id:?string}
 * @phpstan-type AuditPage array{items:list<AuditRow>,pagination:array{limit:int,next:?string,previous:?string}}
 */
final readonly class ListAuditEvents
{
    public function __construct(private Connection $db, private CandidateAuthorization $authorization) {}

    /** @return AuditPage */
    public function page(string $actor, AuditListQuery $query): array
    {
        $this->authorization->requireAdmin($actor);
        if ($this->db->getDatabasePlatform() instanceof PostgreSQLPlatform
            && !in_array($this->db->fetchOne("SELECT pg_catalog.has_table_privilege(current_user, 'public.wcp_candidate_audit', 'SELECT')"), [true, 1, '1', 't'], true)) {
            throw new ServiceUnavailableHttpException(null, 'Candidate audit reads are not enabled.');
        }
        $base = ' FROM wcp_candidate_audit a WHERE EXISTS ('.$this->authorization->adminScopeSql().')';
        $params = [$actor];
        foreach (['kind' => 'kind', 'actor' => 'actor_id', 'tenant' => 'tenant_id', 'resource' => 'resource_id', 'request' => 'request_id'] as $key => $column) {
            if ($query->filters[$key] !== '') { $base .= ' AND a.'.$column.'=?'; $params[] = $query->filters[$key]; }
        }
        if ($query->filters['from'] !== '') { $base .= ' AND a.created_at>=?'; $params[] = $query->filters['from']; }
        if ($query->filters['until'] !== '') { $base .= ' AND a.created_at<=?'; $params[] = $query->filters['until']; }
        $bound = '';
        $values = [];
        $cursor = $query->after ?? $query->before;
        if ($cursor !== null) {
            $pivot = $this->db->fetchAssociative('SELECT a.id,a.created_at'.$base.' AND a.id=?', [...$params, $cursor]);
            if ($pivot === false) throw new BadRequestHttpException('Cursor unavailable in the current result. Start from the first page.');
            [$bound, $values] = $this->bound((string) $pivot['created_at'], (string) $pivot['id'], $query->after !== null);
        }
        $order = $query->before === null ? ' DESC' : ' ASC';
        $rows = $this->db->fetchAllAssociative('SELECT a.id,a.actor_id,a.tenant_id,a.kind,a.resource_id,a.created_at,a.request_id'.$base.$bound
            .' ORDER BY a.created_at'.$order.',a.id'.$order.' LIMIT '.$query->limit, [...$params, ...$values]);
        if ($query->before !== null) $rows = array_reverse($rows);
        $items = array_map(static fn (array $row): array => [
            'id' => (string) $row['id'], 'actor_id' => $row['actor_id'] === null ? null : (string) $row['actor_id'],
            'tenant_id' => $row['tenant_id'] === null ? null : (string) $row['tenant_id'], 'kind' => (string) $row['kind'],
            'resource_id' => $row['resource_id'] === null ? null : (string) $row['resource_id'], 'created_at' => (string) $row['created_at'],
            'request_id' => $row['request_id'] === null ? null : (string) $row['request_id'],
        ], $rows);
        $next = null;
        $previous = null;
        if ($items !== []) {
            $first = $items[0];
            $last = $items[count($items) - 1];
            if ($this->exists($base, $params, $last, true)) $next = $last['id'];
            if ($this->exists($base, $params, $first, false)) $previous = $first['id'];
        }
        $this->authorization->requireAdmin($actor);

        return ['items' => $items, 'pagination' => ['limit' => $query->limit, 'next' => $next, 'previous' => $previous]];
    }

    /** @param list<string> $params
     * @param AuditRow $pivot
     */
    private function exists(string $base, array $params, array $pivot, bool $older): bool
    {
        [$bound, $values] = $this->bound($pivot['created_at'], $pivot['id'], $older);

        return (bool) $this->db->fetchOne('SELECT 1'.$base.$bound.' LIMIT 1', [...$params, ...$values]);
    }

    /** @return array{string,list<string>} */
    private function bound(string $created, string $id, bool $older): array
    {
        $comparison = $older ? '<' : '>';

        return [' AND (a.created_at '.$comparison.' ? OR (a.created_at = ? AND a.id '.$comparison.' ?))', [$created, $created, $id]];
    }
}
