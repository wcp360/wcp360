<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Operations\Infrastructure;
use Doctrine\DBAL\Connection;
/** Experimental persistence, deliberately outside all eight canonical schemas. Never a DB Model v1 implementation. */
final class WorkbenchSchema
{
    /** @return list<string> */
    public static function statements(Connection $db): array
    {
        $pg = $db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform;
        $id = $pg ? 'UUID' : 'VARCHAR(36)';
        return [
            "CREATE TABLE wcp_candidate_users (id $id PRIMARY KEY, email VARCHAR(254) NOT NULL, password_hash VARCHAR(255) NOT NULL, context VARCHAR(8) NOT NULL CHECK(context IN ('admin','user')), active INTEGER NOT NULL CHECK(active IN (0,1)), UNIQUE(email,context))",
            "CREATE TABLE wcp_candidate_accounts (id $id PRIMARY KEY, name VARCHAR(120) NOT NULL)",
            "CREATE TABLE wcp_candidate_tenants (id $id PRIMARY KEY, account_id $id NOT NULL REFERENCES wcp_candidate_accounts(id), name VARCHAR(120) NOT NULL, CHECK(id <> account_id))",
            "CREATE TABLE wcp_candidate_memberships (user_id $id NOT NULL REFERENCES wcp_candidate_users(id), tenant_id $id NOT NULL REFERENCES wcp_candidate_tenants(id), permission VARCHAR(64) NOT NULL CHECK(permission IN ('candidate.hosting.plan')), PRIMARY KEY(user_id,tenant_id,permission))",
            "CREATE TABLE wcp_candidate_sites (id $id PRIMARY KEY, tenant_id $id NOT NULL REFERENCES wcp_candidate_tenants(id), hostname VARCHAR(253) NOT NULL UNIQUE, state VARCHAR(16) NOT NULL CHECK(state IN ('PLAN_QUEUED','PLAN_READY','PLAN_FAILED')), revision INTEGER NOT NULL DEFAULT 1 CHECK(revision >= 1), UNIQUE(tenant_id,id))",
            "CREATE TABLE wcp_candidate_jobs (id $id PRIMARY KEY, tenant_id $id NOT NULL, site_id $id NOT NULL, actor_id $id NOT NULL REFERENCES wcp_candidate_users(id), idempotency_key VARCHAR(128) NOT NULL, fingerprint VARCHAR(64) NOT NULL, state VARCHAR(32) NOT NULL CHECK(state IN ('QUEUED','RUNNING','SUCCEEDED','FAILED','UNKNOWN_OUTCOME')), result TEXT NULL, created_at VARCHAR(32) NOT NULL, started_at VARCHAR(32) NULL, completed_at VARCHAR(32) NULL, UNIQUE(actor_id,tenant_id,idempotency_key), FOREIGN KEY(tenant_id,site_id) REFERENCES wcp_candidate_sites(tenant_id,id))",
            'CREATE INDEX wcp_candidate_jobs_queue ON wcp_candidate_jobs(state,created_at,id)',
            "CREATE TABLE wcp_candidate_audit (id $id PRIMARY KEY, actor_id $id NULL REFERENCES wcp_candidate_users(id), tenant_id $id NULL REFERENCES wcp_candidate_tenants(id), kind VARCHAR(64) NOT NULL, resource_id $id NULL, created_at VARCHAR(32) NOT NULL)",
            "CREATE TABLE wcp_candidate_plugins (id VARCHAR(64) PRIMARY KEY, version VARCHAR(32) NOT NULL, state VARCHAR(16) NOT NULL CHECK(state IN ('DISABLED','ENABLED')), manifest_hash VARCHAR(64) NOT NULL)",
        ];
    }
    /** @return list<string> */
    public static function auditStatements(Connection $db):array
    {
        $pg = $db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform;
        $sql=[];
        if($pg){
            $sql[] = 'DROP TRIGGER IF EXISTS wcp_candidate_audit_immutable ON wcp_candidate_audit';
            $sql[] = "CREATE OR REPLACE FUNCTION wcp_candidate_audit_immutable() RETURNS trigger LANGUAGE plpgsql AS 'BEGIN RAISE EXCEPTION ''candidate audit is append-only''; END;'";
            $sql[] = 'CREATE TRIGGER wcp_candidate_audit_immutable BEFORE UPDATE OR DELETE ON wcp_candidate_audit FOR EACH ROW EXECUTE FUNCTION wcp_candidate_audit_immutable()';
        }else{
            $sql[] = 'DROP TRIGGER IF EXISTS wcp_candidate_audit_no_update';
            $sql[] = 'DROP TRIGGER IF EXISTS wcp_candidate_audit_no_delete';
            $sql[] = "CREATE TRIGGER wcp_candidate_audit_no_update BEFORE UPDATE ON wcp_candidate_audit BEGIN SELECT RAISE(ABORT,'candidate audit is append-only'); END";
            $sql[] = "CREATE TRIGGER wcp_candidate_audit_no_delete BEFORE DELETE ON wcp_candidate_audit BEGIN SELECT RAISE(ABORT,'candidate audit is append-only'); END";
        }
        return $sql;
    }
}
