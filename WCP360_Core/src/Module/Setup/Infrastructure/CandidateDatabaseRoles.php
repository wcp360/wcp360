<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\PasswordChangeSchema;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Fresh candidate role creation and explicit fixed-capability upgrades; no credential rotation or generic repair. */
final readonly class CandidateDatabaseRoles
{
    public const REJECTED = 'Candidate database role provisioning refused. Check the private setup configuration, schema, role conflicts and grants.';
    public const UNCERTAIN = 'Candidate database role outcome is uncertain. Inspect the database before any further attempt.';

    public function __construct(private Connection $db, private RuntimeContext $runtime, private bool $enabled = false)
    {
    }

    /** @return array<string,mixed> */
    public function plan(): array
    {
        $this->requireSetup();
        return $this->inspect($this->pdo()) + ['applied' => false, 'apply_enabled' => $this->enabled];
    }

    /** @param array<string,string> $passwords
     * @return array<string,mixed>
     */
    public function provision(#[\SensitiveParameter] array $passwords): array
    {
        $this->requireSetup();
        if (!$this->enabled || count($passwords) !== 3 || array_diff(array_keys($passwords), CandidateDatabasePolicy::CONTEXTS)) {
            throw new \RuntimeException(self::REJECTED);
        }
        foreach ($passwords as $password) {
            if (!preg_match('/\A[a-f0-9]{64}\z/D', $password)) {
                throw new \RuntimeException(self::REJECTED);
            }
        }
        if (count(array_unique($passwords)) !== 3) {
            throw new \RuntimeException(self::REJECTED);
        }
        $pdo = $this->pdo();
        if ($this->db->isTransactionActive() || $pdo->inTransaction()) {
            throw new \RuntimeException(self::REJECTED);
        }
        $committing = false;
        try {
            if (!$pdo->beginTransaction()) {
                throw new \RuntimeException(self::REJECTED);
            }
            $pdo->exec("SET LOCAL lock_timeout = '2000ms'");
            $pdo->exec("SET LOCAL statement_timeout = '5000ms'");
            if (!self::boolean($this->query($pdo, "SELECT pg_catalog.pg_try_advisory_xact_lock(pg_catalog.hashtext(current_database()), 3601701)")->fetchColumn())) {
                throw new \RuntimeException(self::REJECTED);
            }
            $plan = $this->inspect($pdo);
            foreach ($plan['roles'] as $context => $role) {
                $quoted = '"'.$role.'"';
                // Native PDO bypasses application SQL profiling. SQL receives a verifier, never the password.
                $verifier = PostgresScram::verifier($passwords[$context]);
                $pdo->exec('CREATE ROLE '.$quoted.' NOLOGIN NOINHERIT NOSUPERUSER NOCREATEDB NOCREATEROLE NOREPLICATION NOBYPASSRLS PASSWORD '.$pdo->quote($verifier));
                $pdo->exec('GRANT CONNECT ON DATABASE "'.$plan['database'].'" TO '.$quoted);
                $pdo->exec('GRANT USAGE ON SCHEMA public TO '.$quoted);
                foreach (CandidateDatabasePolicy::grants($context) as $table => $privileges) {
                    $pdo->exec('GRANT '.implode(', ', $privileges).' ON TABLE public.wcp_candidate_'.$table.' TO '.$quoted);
                }
                if (in_array($context, ['user', 'admin'], true)) {
                    $pdo->exec('GRANT EXECUTE ON FUNCTION '.PasswordChangeSchema::signature($context).' TO '.$quoted);
                }
                $this->verifyGrants($pdo, $context, $role);
                CandidatePasswordRoutinePolicy::verifyAccess($pdo, $context, $role, true);
                $pdo->exec('ALTER ROLE '.$quoted.' LOGIN');
            }
            $audit = $pdo->prepare('INSERT INTO public.wcp_candidate_audit (id,actor_id,tenant_id,kind,resource_id,created_at) VALUES (?,NULL,NULL,?,NULL,?)');
            $audit->execute([WorkbenchStore::id(), 'candidate.database.roles.created', WorkbenchStore::now()]);
            $committing = true;
            if (!$pdo->commit()) {
                throw new \RuntimeException(self::UNCERTAIN);
            }

            return $plan + ['applied' => true, 'apply_enabled' => true];
        } catch (\Throwable) {
            $uncertain = $committing;
            try {
                if ($pdo->inTransaction()) {
                    if (!$pdo->rollBack()) {
                        $uncertain = true;
                    }
                }
            } catch (\Throwable) {
                $uncertain = true;
            }
            $this->db->close();
            throw new \RuntimeException($uncertain ? self::UNCERTAIN : self::REJECTED);
        }
    }

    /** @return array<string,mixed> */
    public function passwordChangePlan(): array
    {
        $this->requireSetup();
        $pdo = $this->pdo();
        $plan = $this->inspect($pdo, true);
        return $plan + ['password_changes' => [
            'user' => CandidatePasswordRoutinePolicy::enabled($pdo, 'user', $plan['roles']['user']),
            'admin' => CandidatePasswordRoutinePolicy::enabled($pdo, 'admin', $plan['roles']['admin']),
            'worker' => false,
        ], 'applied' => false, 'apply_enabled' => $this->enabled];
    }

    /** Enable only the new fixed operations on existing verified candidate roles.
     * @return array<string,mixed>
     */
    public function enablePasswordChanges(): array
    {
        $this->requireSetup();
        if (!$this->enabled) throw new \RuntimeException(self::REJECTED);
        $pdo = $this->pdo();
        if ($this->db->isTransactionActive() || $pdo->inTransaction()) throw new \RuntimeException(self::REJECTED);
        $committing = false;
        try {
            if (!$pdo->beginTransaction()) throw new \RuntimeException(self::REJECTED);
            $pdo->exec("SET LOCAL lock_timeout = '2000ms'");
            $pdo->exec("SET LOCAL statement_timeout = '5000ms'");
            if (!self::boolean($this->query($pdo, "SELECT pg_catalog.pg_try_advisory_xact_lock(pg_catalog.hashtext(current_database()), 3601701)")->fetchColumn())) throw new \RuntimeException(self::REJECTED);
            $plan = $this->inspect($pdo, true);
            $changed = false;
            foreach ($plan['roles'] as $context => $role) {
                if (!in_array($context, ['user', 'admin'], true)) continue;
                $access = $pdo->prepare("SELECT pg_catalog.has_function_privilege(?, pg_catalog.to_regprocedure(?), 'EXECUTE')");
                $access->execute([$role, PasswordChangeSchema::signature($context)]);
                if (!self::boolean($access->fetchColumn())) {
                    $pdo->exec('GRANT EXECUTE ON FUNCTION '.PasswordChangeSchema::signature($context).' TO "'.$role.'"');
                    $changed = true;
                }
                CandidatePasswordRoutinePolicy::verifyAccess($pdo, $context, $role, true);
            }
            if ($changed) {
                $audit = $pdo->prepare('INSERT INTO public.wcp_candidate_audit (id,actor_id,tenant_id,kind,resource_id,created_at) VALUES (?,NULL,NULL,?,NULL,?)');
                $audit->execute([WorkbenchStore::id(), 'candidate.database.password_changes.enabled', WorkbenchStore::now()]);
            }
            $committing = true;
            if (!$pdo->commit()) throw new \RuntimeException(self::UNCERTAIN);
            return $plan + ['password_changes' => ['user' => true, 'admin' => true, 'worker' => false], 'applied' => true, 'changed' => $changed, 'apply_enabled' => true];
        } catch (\Throwable) {
            $uncertain = $committing;
            try { if ($pdo->inTransaction() && !$pdo->rollBack()) $uncertain = true; }
            catch (\Throwable) { $uncertain = true; }
            $this->db->close();
            throw new \RuntimeException($uncertain ? self::UNCERTAIN : self::REJECTED);
        }
    }

    private function requireSetup(): void
    {
        if ($this->runtime->context !== 'setup') {
            throw new \RuntimeException(self::REJECTED);
        }
    }

    private function pdo(): \PDO
    {
        try {
            if (!$this->db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
                throw new \RuntimeException(self::REJECTED);
            }
            $pdo = $this->db->getNativeConnection();
            if (!$pdo instanceof \PDO) {
                throw new \RuntimeException(self::REJECTED);
            }
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\Throwable) {
            throw new \RuntimeException(self::REJECTED);
        }
    }

    /** @return array{schema:string,database:string,roles:array{user:string,admin:string,worker:string},grants:array<string,array<string,list<string>>>,password_change_operations:array{user:string,admin:string},production_qualified:bool} */
    private function inspect(\PDO $pdo, bool $existingRoles = false): array
    {
        try {
            $identity = $this->query($pdo, "SELECT current_database() AS database, current_setting('server_version_num')::integer AS version, current_user = session_user AS unchanged, rolsuper, rolcreaterole, rolsuper OR EXISTS (SELECT 1 FROM pg_catalog.pg_database d WHERE d.datname=current_database() AND d.datdba=r.oid) AS database_owned, pg_catalog.has_schema_privilege(current_user, 'public', 'USAGE WITH GRANT OPTION') AS schema_grant FROM pg_catalog.pg_roles r WHERE rolname = current_user")->fetch(\PDO::FETCH_ASSOC);
            if (!is_array($identity) || !self::boolean($identity['unchanged']) || !self::boolean($identity['database_owned']) || !self::boolean($identity['schema_grant'])
                || (int) $identity['version'] < 170000 || (int) $identity['version'] >= 180000
                || (!self::boolean($identity['rolsuper']) && !self::boolean($identity['rolcreaterole']))) {
                throw new \RuntimeException(self::REJECTED);
            }
            $roles = CandidateDatabasePolicy::roles($identity['database']);
            if ((int) $this->query($pdo, "SELECT count(*) FROM pg_catalog.pg_namespace WHERE left(nspname,4) = 'wcp_'")->fetchColumn() !== 0) {
                throw new \RuntimeException(self::REJECTED);
            }
            $tables = $this->query($pdo, "SELECT c.relname, c.relkind, c.relowner = r.oid OR r.rolsuper AS owned FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid=c.relnamespace CROSS JOIN pg_catalog.pg_roles r WHERE n.nspname='public' AND c.relkind IN ('r','p','v','m','f') AND r.rolname=current_user")->fetchAll(\PDO::FETCH_ASSOC);
            $expected = array_map(static fn (string $name): string => 'wcp_candidate_'.$name, CandidateDatabasePolicy::TABLES);
            $seen = [];
            foreach ($tables as $table) {
                if ($table['relname'] === 'doctrine_migration_versions') {
                    continue;
                }
                if (!in_array($table['relname'], $expected, true) || $table['relkind'] !== 'r' || !self::boolean($table['owned'])) {
                    throw new \RuntimeException(self::REJECTED);
                }
                $seen[] = $table['relname'];
            }
            if (count($seen) !== count($expected) || array_diff($expected, $seen)) {
                throw new \RuntimeException(self::REJECTED);
            }
            $guards = $this->query($pdo, "SELECT count(*) FROM pg_catalog.pg_constraint k JOIN pg_catalog.pg_class c ON c.oid=k.conrelid JOIN pg_catalog.pg_namespace n ON n.oid=c.relnamespace JOIN pg_catalog.pg_attribute a ON a.attrelid=c.oid AND a.attname='lock_guard' WHERE n.nspname='public' AND c.relname IN ('wcp_candidate_users','wcp_candidate_tenants','wcp_candidate_sites') AND k.conname=c.relname || '_lock_guard_zero' AND k.contype='c' AND k.convalidated AND NOT k.connoinherit AND a.atttypid='pg_catalog.int2'::regtype AND a.attnotnull AND pg_catalog.pg_get_expr(k.conbin,k.conrelid)='(lock_guard = 0)'")->fetchColumn();
            if ((int) $guards !== 3) {
                throw new \RuntimeException(self::REJECTED);
            }
            $exists = $pdo->prepare('SELECT count(*) FROM pg_catalog.pg_roles WHERE rolname IN (?,?,?)');
            $exists->execute(array_values($roles));
            if ((int) $exists->fetchColumn() !== ($existingRoles ? 3 : 0)) {
                throw new \RuntimeException(self::REJECTED);
            }
            CandidatePasswordRoutinePolicy::verify($pdo, $existingRoles);
            if ($existingRoles) {
                foreach ($roles as $context => $role) {
                    $identityCheck = $pdo->prepare("SELECT rolcanlogin AND NOT (rolsuper OR rolcreatedb OR rolcreaterole OR rolreplication OR rolbypassrls) AND rolconfig IS NULL AND rolvaliduntil IS NULL AND NOT EXISTS (SELECT 1 FROM pg_catalog.pg_auth_members m WHERE m.member = r.oid) AND NOT EXISTS (SELECT 1 FROM pg_catalog.pg_database d WHERE d.datdba=r.oid) AND NOT EXISTS (SELECT 1 FROM pg_catalog.pg_namespace n WHERE n.nspowner=r.oid) AS safe FROM pg_catalog.pg_roles r WHERE rolname=?");
                    $identityCheck->execute([$role]);
                    if (!self::boolean($identityCheck->fetchColumn())) throw new \RuntimeException(self::REJECTED);
                    $this->verifyGrants($pdo, $context, $role);
                    CandidatePasswordRoutinePolicy::verifyAccess($pdo, $context, $role, null);
                }
            }
            $grants = [];
            foreach (CandidateDatabasePolicy::CONTEXTS as $context) {
                $grants[$context] = CandidateDatabasePolicy::grants($context);
            }

            return ['schema' => 'wcp360.candidate.database-roles/1', 'database' => $identity['database'], 'roles' => $roles, 'grants' => $grants, 'password_change_operations' => ['user' => PasswordChangeSchema::signature('user'), 'admin' => PasswordChangeSchema::signature('admin')], 'production_qualified' => false];
        } catch (\Throwable) {
            throw new \RuntimeException(self::REJECTED);
        }
    }

    private function verifyGrants(\PDO $pdo, string $context, string $role): void
    {
        $grants = CandidateDatabasePolicy::grants($context);
        $options = $pdo->prepare("SELECT count(*) FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid=c.relnamespace CROSS JOIN LATERAL pg_catalog.aclexplode(COALESCE(c.relacl,pg_catalog.acldefault('r',c.relowner))) a JOIN pg_catalog.pg_roles r ON r.oid=a.grantee WHERE n.nspname='public' AND c.relname LIKE 'wcp_candidate_%' AND r.rolname=? AND a.is_grantable");
        $options->execute([$role]);
        if ((int) $options->fetchColumn() !== 0) throw new \RuntimeException(self::REJECTED);
        $columnOptions = $pdo->prepare("SELECT count(*) FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid=c.relnamespace JOIN pg_catalog.pg_attribute col ON col.attrelid=c.oid CROSS JOIN LATERAL pg_catalog.aclexplode(col.attacl) a JOIN pg_catalog.pg_roles r ON r.oid=a.grantee WHERE n.nspname='public' AND c.relname LIKE 'wcp_candidate_%' AND r.rolname=? AND a.is_grantable");
        $columnOptions->execute([$role]);
        if ((int) $columnOptions->fetchColumn() !== 0) throw new \RuntimeException(self::REJECTED);
        $schema = $pdo->prepare("SELECT pg_catalog.has_schema_privilege(?, 'public', 'CREATE')");
        $schema->execute([$role]);
        if (self::boolean($schema->fetchColumn())) {
            throw new \RuntimeException(self::REJECTED);
        }
        $tables = $pdo->prepare(<<<'SQL'
SELECT c.relname, p.privilege, pg_catalog.has_table_privilege(?, c.oid, p.privilege) AS allowed
FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid=c.relnamespace
CROSS JOIN (VALUES ('SELECT'),('INSERT'),('UPDATE'),('DELETE'),('TRUNCATE'),('REFERENCES'),('TRIGGER'),('MAINTAIN')) p(privilege)
WHERE n.nspname='public' AND c.relkind='r' AND c.relname LIKE 'wcp_candidate_%'
SQL);
        $tables->execute([$role]);
        foreach ($tables->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $table = substr($row['relname'], strlen('wcp_candidate_'));
            if (self::boolean($row['allowed']) !== in_array($row['privilege'], $grants[$table] ?? [], true)) {
                throw new \RuntimeException(self::REJECTED);
            }
        }
        $columns = $pdo->prepare(<<<'SQL'
SELECT c.relname, a.attname, p.privilege, pg_catalog.has_column_privilege(?, c.oid, a.attnum, p.privilege) AS allowed
FROM pg_catalog.pg_class c JOIN pg_catalog.pg_namespace n ON n.oid=c.relnamespace
JOIN pg_catalog.pg_attribute a ON a.attrelid=c.oid AND a.attnum>0 AND NOT a.attisdropped
CROSS JOIN (VALUES ('SELECT'),('INSERT'),('UPDATE'),('REFERENCES')) p(privilege)
WHERE n.nspname='public' AND c.relkind='r' AND c.relname LIKE 'wcp_candidate_%'
SQL);
        $columns->execute([$role]);
        foreach ($columns->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $allowed = $grants[substr($row['relname'], strlen('wcp_candidate_'))] ?? [];
            $expected = in_array($row['privilege'], $allowed, true) || in_array($row['privilege'].'('.$row['attname'].')', $allowed, true);
            if (self::boolean($row['allowed']) !== $expected) {
                throw new \RuntimeException(self::REJECTED);
            }
        }
    }

    private function query(\PDO $pdo, string $sql): \PDOStatement
    {
        $statement = $pdo->query($sql);
        if ($statement === false) {
            throw new \RuntimeException(self::REJECTED);
        }
        return $statement;
    }

    private static function boolean(mixed $value): bool
    {
        return match ($value) {
            true, 1, '1', 't' => true,
            false, 0, '0', 'f' => false,
            default => throw new \RuntimeException(self::REJECTED),
        };
    }
}
