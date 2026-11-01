<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Security;

use Doctrine\DBAL\Driver\Connection;

/** Bounded candidate session-table admission; not a complete production grant policy. */
final class CandidateSessionGrants
{
    public static function verify(Connection $connection, string $context): void
    {
        if (!in_array($context, ['user', 'admin', 'worker'], true)) {
            throw new \RuntimeException(CandidateDatabaseUrl::REJECTED);
        }
        // Remove "$user" and caller-controlled schemas before candidate application SQL.
        $connection->exec('SET search_path TO pg_catalog, public');
        $row = $connection->query(<<<'SQL'
SELECT
    current_user = session_user
    AND current_setting('server_version_num')::integer BETWEEN 170000 AND 179999
    AND EXISTS (
        SELECT 1 FROM pg_catalog.pg_roles r
        WHERE r.rolname = session_user AND r.rolcanlogin
          AND NOT (r.rolsuper OR r.rolcreatedb OR r.rolcreaterole OR r.rolreplication OR r.rolbypassrls)
          AND NOT EXISTS (SELECT 1 FROM pg_catalog.pg_auth_members m WHERE m.member = r.oid)
          AND NOT EXISTS (SELECT 1 FROM pg_catalog.pg_database d WHERE d.datname = current_database() AND d.datdba = r.oid)
          AND NOT EXISTS (SELECT 1 FROM pg_catalog.pg_namespace n WHERE n.nspname = 'public' AND n.nspowner = r.oid)
    )
    AND NOT pg_catalog.has_schema_privilege(current_user, 'public', 'CREATE')
    AND (SELECT count(*) = 2 FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
         WHERE n.nspname = 'public' AND c.relname IN ('wcp_candidate_user_sessions', 'wcp_candidate_admin_sessions')
           AND c.relkind = 'r') AS identity_ok,
    (SELECT bool_and(pg_catalog.has_table_privilege(current_user, 'public.wcp_candidate_user_sessions', p))
        FROM (VALUES ('SELECT'), ('INSERT'), ('UPDATE'), ('DELETE')) privileges(p)) AS user_crud,
    (SELECT bool_and(pg_catalog.has_table_privilege(current_user, 'public.wcp_candidate_admin_sessions', p))
        FROM (VALUES ('SELECT'), ('INSERT'), ('UPDATE'), ('DELETE')) privileges(p)) AS admin_crud,
    pg_catalog.has_table_privilege(current_user, 'public.wcp_candidate_user_sessions', 'SELECT,INSERT,UPDATE,DELETE,TRUNCATE,REFERENCES,TRIGGER')
        OR pg_catalog.has_any_column_privilege(current_user, 'public.wcp_candidate_user_sessions', 'SELECT,INSERT,UPDATE,REFERENCES') AS user_access,
    pg_catalog.has_table_privilege(current_user, 'public.wcp_candidate_admin_sessions', 'SELECT,INSERT,UPDATE,DELETE,TRUNCATE,REFERENCES,TRIGGER')
        OR pg_catalog.has_any_column_privilege(current_user, 'public.wcp_candidate_admin_sessions', 'SELECT,INSERT,UPDATE,REFERENCES') AS admin_access
SQL)->fetchAssociative();
        if (!$row || !self::truth($row['identity_ok'] ?? null)
            || ($context === 'user' && (!self::truth($row['user_crud'] ?? null) || !self::falsity($row['admin_access'] ?? null)))
            || ($context === 'admin' && (!self::truth($row['admin_crud'] ?? null) || !self::falsity($row['user_access'] ?? null)))
            || ($context === 'worker' && (!self::falsity($row['user_access'] ?? null) || !self::falsity($row['admin_access'] ?? null)))) {
            throw new \RuntimeException(CandidateDatabaseUrl::REJECTED);
        }
    }

    private static function truth(mixed $value): bool
    {
        return in_array($value, [true, 1, '1', 't'], true);
    }

    private static function falsity(mixed $value): bool
    {
        return in_array($value, [false, 0, '0', 'f'], true);
    }
}
