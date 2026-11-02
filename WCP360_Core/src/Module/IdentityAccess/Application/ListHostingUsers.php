<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Wcp360\Core\Module\IdentityAccess\Domain\UserListQuery;

/**
 * Hosting login identities only; no Unix account/tenant/customer lifecycle is inferred.
 * @phpstan-type UserRow array{id:string,username:string,email:string,active:bool}
 * @phpstan-type UserPage array{items:list<UserRow>,pagination:array{limit:int,next:?string,previous:?string}}
 */
final readonly class ListHostingUsers
{
    public function __construct(private Connection $db, private CandidateAuthorization $authorization) {}

    /** @return UserPage */
    public function page(string $admin, UserListQuery $query): array
    {
        $this->authorization->requireAdmin($admin);
        $base = " FROM wcp_candidate_users u WHERE u.context='user' AND EXISTS (".$this->authorization->adminScopeSql().')';
        $params = [$admin];
        if ($query->state !== '') { $base .= ' AND u.active=?'; $params[] = $query->state === 'active' ? 1 : 0; }
        if ($query->search !== '') {
            // Underscores are literal username characters, never LIKE wildcards.
            $base .= " AND ".$this->usernameColumn()." LIKE ? ESCAPE '!'";
            $params[] = '%'.str_replace('_', '!_', $query->search).'%';
        }
        $bound = '';
        $values = [];
        $cursor = $query->after ?? $query->before;
        if ($cursor !== null) {
            $pivot = $this->db->fetchAssociative('SELECT u.id,u.username'.$base.' AND u.id=?', [...$params, $cursor]);
            if ($pivot === false) throw new BadRequestHttpException('Cursor unavailable in the current result. Start from the first page.');
            [$bound, $values] = $this->bound((string) $pivot['username'], (string) $pivot['id'], $query->after !== null);
        }
        $order = $query->before === null ? ' ASC' : ' DESC';
        $rows = $this->db->fetchAllAssociative('SELECT u.id,u.username,u.email,u.active'.$base.$bound
            .' ORDER BY '.$this->usernameColumn().$order.',u.id'.$order.' LIMIT '.$query->limit, [...$params, ...$values]);
        if ($query->before !== null) $rows = array_reverse($rows);
        $items = array_map(static fn (array $row): array => [
            'id' => (string) $row['id'], 'username' => (string) $row['username'],
            'email' => (string) $row['email'], 'active' => (bool) $row['active'],
        ], $rows);
        $next = null;
        $previous = null;
        if ($items !== []) {
            $first = $items[0];
            $last = $items[count($items) - 1];
            if ($this->exists($base, $params, $last, true)) $next = $last['id'];
            if ($this->exists($base, $params, $first, false)) $previous = $first['id'];
        }
        $this->authorization->requireAdmin($admin);

        return ['items' => $items, 'pagination' => ['limit' => $query->limit, 'next' => $next, 'previous' => $previous]];
    }

    /** @param list<string|int> $params
     * @param UserRow $pivot
     */
    private function exists(string $base, array $params, array $pivot, bool $later): bool
    {
        [$bound, $values] = $this->bound($pivot['username'], $pivot['id'], $later);

        return (bool) $this->db->fetchOne('SELECT 1'.$base.$bound.' LIMIT 1', [...$params, ...$values]);
    }

    private function usernameColumn(): string
    {
        return $this->db->getDatabasePlatform() instanceof PostgreSQLPlatform ? 'u.username COLLATE "C"' : 'u.username COLLATE BINARY';
    }

    /** @return array{string,list<string>} */
    private function bound(string $username, string $id, bool $later): array
    {
        $comparison = $later ? '>' : '<';
        $column = $this->usernameColumn();

        return [' AND ('.$column.' '.$comparison.' ? OR ('.$column.' = ? AND u.id '.$comparison.' ?))', [$username, $username, $id]];
    }
}
