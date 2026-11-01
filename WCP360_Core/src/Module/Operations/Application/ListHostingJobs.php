<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Operations\Application;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Operations\Domain\JobListQuery;

/**
 * Read-only hosting-plan inventory, not a general scheduler or global audit view.
 *
 * @phpstan-type JobRow array{id:string,state:string,created_at:string,started_at:?string,completed_at:?string,hostname:string,application_server:string}
 * @phpstan-type JobPage array{items:list<JobRow>,pagination:array{limit:int,next:?string,previous:?string}}
 */
final readonly class ListHostingJobs
{
    private const FIELDS = 'j.id,j.state,j.created_at,j.started_at,j.completed_at,s.hostname,s.application_server';

    public function __construct(private Connection $db, private CandidateAuthorization $authorization) {}

    /** @return JobPage */
    public function page(string $actor, JobListQuery $query): array
    {
        $this->authorization->requireTenantPermission($actor, $query->tenant);
        // Every row/pivot/bound query includes the same current authorization rule,
        // so a cursor cannot select a resource in another tenant or a revoked grant.
        $base = ' FROM wcp_candidate_jobs j JOIN wcp_candidate_sites s ON s.id=j.site_id AND s.tenant_id=j.tenant_id'
            .' JOIN ('.$this->authorization->tenantScopeSql().') permitted ON permitted.id=j.tenant_id WHERE j.tenant_id=?';
        $params = [$actor, CandidateAuthorization::HOSTING_PLAN, $query->tenant];
        if ($query->state !== '') { $base .= ' AND j.state=?'; $params[] = $query->state; }
        if ($query->search !== '') { $base .= ' AND s.hostname LIKE ?'; $params[] = '%'.$query->search.'%'; }
        $bound = '';
        $readParams = $params;
        $cursor = $query->after ?? $query->before;
        if ($cursor !== null) {
            $pivot = $this->db->fetchAssociative('SELECT j.id,j.created_at'.$base.' AND j.id=?', [...$params, $cursor]);
            if ($pivot === false) throw new BadRequestHttpException('Cursor unavailable in the current result. Start from the first page.');
            [$bound, $values] = $this->bound((string) $pivot['created_at'], (string) $pivot['id'], $query->after !== null);
            $readParams = [...$params, ...$values];
        }
        $order = $query->before === null ? ' DESC' : ' ASC';
        $rows = $this->db->fetchAllAssociative('SELECT '.self::FIELDS.$base.$bound
            .' ORDER BY j.created_at'.$order.',j.id'.$order.' LIMIT '.$query->limit, $readParams);
        if ($query->before !== null) $rows = array_reverse($rows);
        $items = array_map(static fn (array $row): array => [
            'id' => (string) $row['id'], 'state' => (string) $row['state'], 'created_at' => (string) $row['created_at'],
            'started_at' => $row['started_at'] === null ? null : (string) $row['started_at'],
            'completed_at' => $row['completed_at'] === null ? null : (string) $row['completed_at'],
            'hostname' => (string) $row['hostname'], 'application_server' => (string) $row['application_server'],
        ], $rows);
        $next = null;
        $previous = null;
        if ($items !== []) {
            $first = $items[0];
            $last = $items[count($items) - 1];
            if ($this->exists($base, $params, $last, true)) $next = $last['id'];
            if ($this->exists($base, $params, $first, false)) $previous = $first['id'];
        }
        // A revocation observed before returning never becomes an empty authorized page.
        $this->authorization->requireTenantPermission($actor, $query->tenant);

        return ['items' => $items, 'pagination' => ['limit' => $query->limit, 'next' => $next, 'previous' => $previous]];
    }

    /** @param list<mixed> $params
     * @param JobRow $pivot
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

        return [' AND (j.created_at '.$comparison.' ? OR (j.created_at = ? AND j.id '.$comparison.' ?))', [$created, $created, $id]];
    }
}
