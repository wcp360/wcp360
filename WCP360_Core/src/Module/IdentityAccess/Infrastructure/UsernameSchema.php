<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Wcp360\Core\Module\IdentityAccess\Domain\CandidateUsername;

/** Read-only migration planning; schema writes never happen during authentication. */
final class UsernameSchema
{
    /** Apply the same plan to disposable fixtures; Doctrine queues it through addSql().
     * @internal
     */
    public static function upgrade(Connection $db): void
    {
        $db->transactional(static function () use ($db): void {
            foreach (self::statements($db) as $statement) {
                $db->executeStatement($statement['sql'], $statement['parameters']);
            }
        });
    }

    /** @return list<array{sql:string, parameters:list<mixed>}> */
    public static function statements(Connection $db): array
    {
        $platform = $db->getDatabasePlatform();
        if (!$platform instanceof PostgreSQLPlatform && !$platform instanceof SQLitePlatform) {
            throw new \RuntimeException('Unsupported candidate username database.');
        }
        $manager = $db->createSchemaManager();
        $exists = $manager->tablesExist(['wcp_candidate_users']);
        if ($exists && array_key_exists('username', $manager->listTableColumns('wcp_candidate_users'))) {
            throw new \RuntimeException('Candidate username schema already exists; use migration tracking.');
        }
        // An empty fresh-install dry run has not executed earlier CREATE TABLE statements.
        $rows = $exists ? $db->fetchAllAssociative('SELECT id, email, context FROM wcp_candidate_users ORDER BY context, id') : [];
        $names = self::assign($rows);
        $plan = [['sql' => 'ALTER TABLE wcp_candidate_users ADD COLUMN username VARCHAR(32)', 'parameters' => []]];
        foreach ($names as $id => $name) {
            $plan[] = ['sql' => 'UPDATE wcp_candidate_users SET username = ? WHERE id = ?', 'parameters' => [$name, $id]];
        }
        $plan[] = ['sql' => 'CREATE UNIQUE INDEX wcp_candidate_users_username_context ON wcp_candidate_users(username, context)', 'parameters' => []];
        if ($platform instanceof PostgreSQLPlatform) {
            $plan[] = ['sql' => 'ALTER TABLE wcp_candidate_users ALTER COLUMN username SET NOT NULL', 'parameters' => []];
            $plan[] = ['sql' => 'ALTER TABLE wcp_candidate_users ADD CONSTRAINT wcp_candidate_users_username_format CHECK (username COLLATE "C" ~ \'^[a-z][a-z0-9_]{2,31}$\')', 'parameters' => []];
        } else {
            // Enforce the same input without rebuilding referenced tables or disabling foreign keys.
            $invalid = "typeof(NEW.username) <> 'text' OR length(NEW.username) < 3 OR length(NEW.username) > 32 OR instr(NEW.username, char(0)) > 0 OR substr(NEW.username, 1, 1) NOT GLOB '[a-z]' OR NEW.username GLOB '*[^a-z0-9_]*'";
            foreach (['insert' => 'INSERT', 'update' => 'UPDATE OF username'] as $name => $event) {
                $plan[] = ['sql' => "CREATE TRIGGER wcp_candidate_username_$name BEFORE $event ON wcp_candidate_users WHEN $invalid BEGIN SELECT RAISE(ABORT, 'canonical candidate username required'); END", 'parameters' => []];
            }
        }

        return $plan;
    }

    /** @param list<array<string, mixed>> $rows
     *  @return array<string, string>
     */
    private static function assign(array $rows): array
    {
        $bases = []; $counts = [];
        foreach ($rows as $row) {
            if (!is_string($row['id']) || $row['id'] === '' || !is_string($row['email'])
                || !is_string($row['context']) || !in_array($row['context'], ['user', 'admin'], true)) {
                throw new \RuntimeException('Malformed account in candidate username migration.');
            }
            $base = strtolower(explode('@', $row['email'], 2)[0]);
            $bases[$row['id']] = $base;
            if (CandidateUsername::valid($base)) {
                $key = $row['context']."\0".$base;
                $counts[$key] = ($counts[$key] ?? 0) + 1;
            }
        }
        $assigned = []; $reserved = [];
        foreach ($rows as $row) {
            $id = (string) $row['id']; $context = (string) $row['context'];
            $base = $bases[$id]; $key = $context."\0".$base;
            if (CandidateUsername::valid($base) && ($counts[$key] ?? 0) === 1) {
                $assigned[$id] = $base; $reserved[$key] = true;
            }
        }
        foreach ($rows as $row) {
            $id = (string) $row['id']; $context = (string) $row['context'];
            if (isset($assigned[$id])) continue;
            $attempt = 0;
            do {
                if ($attempt >= 1000) throw new \RuntimeException('Candidate username allocation exhausted.');
                $name = 'u_'.substr(hash('sha256', $context."\0".$id."\0".$attempt++), 0, 30);
                $key = $context."\0".$name;
            } while (isset($reserved[$key]));
            $assigned[$id] = $name; $reserved[$key] = true;
        }

        return $assigned;
    }
}
