<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Existing setup connection only: never opens or falls back to privileged credentials. */
final readonly class CandidateAccountMaintenance
{
    public function __construct(private WorkbenchStore $store, private RuntimeContext $runtime) {}

    public function connection(): \PDO
    {
        if ($this->runtime->context !== 'setup') throw new \RuntimeException('Account maintenance refused.');
        $db = $this->store->db;
        $platform = $db->getDatabasePlatform();
        if (!$platform instanceof PostgreSQLPlatform && !$platform instanceof SQLitePlatform) throw new \RuntimeException('Account maintenance refused.');
        $pdo = $db->getNativeConnection();
        if (!$pdo instanceof \PDO) throw new \RuntimeException('Account maintenance refused.');
        if ($platform instanceof PostgreSQLPlatform) {
            $identity = $pdo->query(<<<'SQL'
SELECT current_user = session_user AND pg_catalog.current_setting('role') = 'none'
    AND pg_catalog.current_setting('server_version_num')::integer BETWEEN 170000 AND 179999
    AND pg_catalog.current_database() IN ('wcp360_workbench', 'wcp360_test')
    AND (r.rolsuper OR (
        r.oid = (SELECT datdba FROM pg_catalog.pg_database WHERE datname = pg_catalog.current_database())
        AND 4 = (SELECT count(*) FROM pg_catalog.pg_class c
            JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
            WHERE n.nspname = 'public' AND c.relkind = 'r' AND c.relowner = r.oid
              AND c.relname IN ('wcp_candidate_users', 'wcp_candidate_user_sessions',
                  'wcp_candidate_admin_sessions', 'wcp_candidate_audit'))
    )) AS allowed
FROM pg_catalog.pg_roles r WHERE r.rolname = current_user
SQL);
            if ($identity === false || !in_array($identity->fetchColumn(), [true, 1, '1', 't'], true)) {
                throw new \RuntimeException('Account maintenance refused.');
            }
            $pdo->exec('SET search_path TO pg_catalog, public, pg_temp');
        }
        return $pdo;
    }
}
