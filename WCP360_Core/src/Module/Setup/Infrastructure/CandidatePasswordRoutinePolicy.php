<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

use Wcp360\Core\Module\IdentityAccess\Infrastructure\PasswordChangeSchema;

/** Maintenance verification of exact frozen code, ownership, settings and direct ACLs. */
final class CandidatePasswordRoutinePolicy
{
    public static function verify(\PDO $pdo, bool $existingRoles = false): void
    {
        $identity = $pdo->query('SELECT current_database()');
        if ($identity === false) throw new \RuntimeException(CandidateDatabaseRoles::REJECTED);
        $database = $identity->fetchColumn();
        if (!is_string($database)) throw new \RuntimeException(CandidateDatabaseRoles::REJECTED);
        $roles = CandidateDatabasePolicy::roles($database);
        $inventory = $pdo->query("SELECT count(*) FROM pg_catalog.pg_proc p JOIN pg_catalog.pg_namespace n ON n.oid=p.pronamespace WHERE n.nspname='public' AND p.proname IN ('wcp_candidate_user_password_change','wcp_candidate_admin_password_change')");
        if ($inventory === false) throw new \RuntimeException(CandidateDatabaseRoles::REJECTED);
        $count = $inventory->fetchColumn();
        if ((int) $count !== 2) throw new \RuntimeException(CandidateDatabaseRoles::REJECTED);
        foreach (['user', 'admin'] as $surface) {
            $statement = $pdo->prepare(<<<'SQL'
SELECT p.oid, p.prosrc, p.prosecdef, p.proisstrict, p.provolatile, p.proparallel, p.prokind,
       p.prorettype = 'pg_catalog.bool'::regtype AS boolean_result, l.lanname,
       pg_catalog.to_json(p.proconfig)::text AS settings,
       r.rolname AS owner,
       (r.rolname = current_user OR p.proowner = (SELECT datdba FROM pg_catalog.pg_database WHERE datname=current_database())) AS owned
FROM pg_catalog.pg_proc p JOIN pg_catalog.pg_roles r ON r.oid=p.proowner
JOIN pg_catalog.pg_language l ON l.oid=p.prolang WHERE p.oid=pg_catalog.to_regprocedure(?)
SQL);
            $statement->execute([PasswordChangeSchema::signature($surface)]);
            $row = $statement->fetch(\PDO::FETCH_ASSOC);
            if (!is_array($row) || !self::truth($row['prosecdef']) || self::truth($row['proisstrict'])
                || !self::truth($row['boolean_result']) || !self::truth($row['owned'])
                || in_array($row['owner'], $roles, true) || $row['provolatile'] !== 'v'
                || $row['proparallel'] !== 'u' || $row['prokind'] !== 'f' || $row['lanname'] !== 'plpgsql'
                || !is_string($row['prosrc']) || !hash_equals(PasswordChangeSchema::body($surface), $row['prosrc'])) {
                throw new \RuntimeException(CandidateDatabaseRoles::REJECTED);
            }
            $settings = json_decode((string) $row['settings'], true, 8, JSON_THROW_ON_ERROR);
            if (!is_array($settings) || count($settings) !== 2
                || !in_array('search_path=pg_catalog, pg_temp', $settings, true)
                || !in_array('lock_timeout=2s', $settings, true)) {
                throw new \RuntimeException(CandidateDatabaseRoles::REJECTED);
            }
            $acl = $pdo->prepare(<<<'SQL'
SELECT a.privilege_type, a.is_grantable, a.grantee=p.proowner AS owned, r.rolname AS grantee
FROM pg_catalog.pg_proc p CROSS JOIN LATERAL pg_catalog.aclexplode(COALESCE(p.proacl,pg_catalog.acldefault('f',p.proowner))) a
LEFT JOIN pg_catalog.pg_roles r ON r.oid=a.grantee WHERE p.oid=pg_catalog.to_regprocedure(?)
SQL);
            $acl->execute([PasswordChangeSchema::signature($surface)]);
            foreach ($acl->fetchAll(\PDO::FETCH_ASSOC) as $entry) {
                if (self::truth($entry['owned'])) continue;
                if (!$existingRoles || $entry['grantee'] !== $roles[$surface] || $entry['privilege_type'] !== 'EXECUTE'
                    || self::truth($entry['is_grantable'])) {
                    throw new \RuntimeException(CandidateDatabaseRoles::REJECTED);
                }
            }
        }
    }

    public static function verifyAccess(\PDO $pdo, string $context, string $role, ?bool $ownEnabled): void
    {
        foreach (['user', 'admin'] as $surface) {
            $allowed = self::enabled($pdo, $surface, $role);
            if (($surface !== $context && $allowed) || ($surface === $context && $ownEnabled !== null && $allowed !== $ownEnabled)) {
                throw new \RuntimeException(CandidateDatabaseRoles::REJECTED);
            }
        }
    }

    public static function enabled(\PDO $pdo, string $surface, string $role): bool
    {
        $statement = $pdo->prepare('SELECT pg_catalog.has_function_privilege(?, pg_catalog.to_regprocedure(?), \'EXECUTE\')');
        $statement->execute([$role, PasswordChangeSchema::signature($surface)]);
        return self::truth($statement->fetchColumn());
    }

    private static function truth(mixed $value): bool
    {
        if (!in_array($value, [true, false, 1, 0, '1', '0', 't', 'f'], true)) throw new \RuntimeException(CandidateDatabaseRoles::REJECTED);
        return in_array($value, [true, 1, '1', 't'], true);
    }
}
