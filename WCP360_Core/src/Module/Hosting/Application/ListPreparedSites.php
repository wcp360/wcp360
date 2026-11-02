<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Application;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\Hosting\Domain\SiteListQuery;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;

/**
 * Fixed read projection of prepared sites. It makes no deployment-health assertion.
 *
 * @phpstan-type SiteRow array{id:string,hostname:string,state:string,application_server:string,revision:int}
 * @phpstan-type SitePage array{items:list<SiteRow>,pagination:array{limit:int,next:?string,previous:?string}}
 */
final readonly class ListPreparedSites
{
    public function __construct(private Connection $db, private CandidateAuthorization $authorization) {}

    /** @return SitePage */
    public function page(string $actor, SiteListQuery $query): array
    {
        $this->authorization->requireTenantPermission($actor, $query->tenant);
        $base = ' FROM wcp_candidate_sites s JOIN ('.$this->authorization->tenantScopeSql().') permitted'
            .' ON permitted.id=s.tenant_id WHERE s.tenant_id=?';
        $params = [$actor, CandidateAuthorization::HOSTING_PLAN, $query->tenant];
        if ($query->state !== '') { $base .= ' AND s.state=?'; $params[] = $query->state; }
        if ($query->search !== '') { $base .= ' AND s.hostname LIKE ?'; $params[] = '%'.$query->search.'%'; }
        if ($query->server !== '') { $base .= ' AND s.application_server=?'; $params[] = $query->server; }
        $bound = '';
        $readParams = $params;
        $cursor = $query->after ?? $query->before;
        if ($cursor !== null) {
            $pivot = $this->db->fetchAssociative('SELECT s.id,s.hostname'.$base.' AND s.id=?', [...$params, $cursor]);
            if ($pivot === false) throw new BadRequestHttpException('Cursor unavailable in the current result. Start from the first page.');
            [$bound, $values] = $this->bound((string) $pivot['hostname'], (string) $pivot['id'], $query->after !== null);
            $readParams = [...$params, ...$values];
        }
        $order = $query->before === null ? ' ASC' : ' DESC';
        $rows = $this->db->fetchAllAssociative('SELECT s.id,s.hostname,s.state,s.application_server,s.revision'.$base.$bound
            .' ORDER BY '.$this->hostnameColumn().$order.',s.id'.$order.' LIMIT '.$query->limit, $readParams);
        if ($query->before !== null) $rows = array_reverse($rows);
        $items = array_map(static fn (array $row): array => [
            'id' => (string) $row['id'], 'hostname' => (string) $row['hostname'], 'state' => (string) $row['state'],
            'application_server' => (string) $row['application_server'], 'revision' => (int) $row['revision'],
        ], $rows);
        $next = null;
        $previous = null;
        if ($items !== []) {
            $first = $items[0];
            $last = $items[count($items) - 1];
            if ($this->exists($base, $params, $last, true)) $next = $last['id'];
            if ($this->exists($base, $params, $first, false)) $previous = $first['id'];
        }
        $this->authorization->requireTenantPermission($actor, $query->tenant);

        return ['items' => $items, 'pagination' => ['limit' => $query->limit, 'next' => $next, 'previous' => $previous]];
    }

    /** @param list<mixed> $params
     * @param SiteRow $pivot
     */
    private function exists(string $base, array $params, array $pivot, bool $later): bool
    {
        [$bound, $values] = $this->bound($pivot['hostname'], $pivot['id'], $later);

        return (bool) $this->db->fetchOne('SELECT 1'.$base.$bound.' LIMIT 1', [...$params, ...$values]);
    }

    /** Use the same deterministic ASCII order for the page and both cursor comparisons. */
    private function hostnameColumn(): string
    {
        return $this->db->getDatabasePlatform() instanceof PostgreSQLPlatform ? 's.hostname COLLATE "C"' : 's.hostname COLLATE BINARY';
    }

    /** @return array{string,list<string>} */
    private function bound(string $hostname, string $id, bool $later): array
    {
        $comparison = $later ? '>' : '<';

        $column = $this->hostnameColumn();

        return [' AND ('.$column.' '.$comparison.' ? OR ('.$column.' = ? AND s.id '.$comparison.' ?))', [$hostname, $hostname, $id]];
    }
}
