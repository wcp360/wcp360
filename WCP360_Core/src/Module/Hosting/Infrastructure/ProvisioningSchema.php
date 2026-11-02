<?php
declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Infrastructure;
final class ProvisioningSchema
{
    /** @return list<string> */
    public static function statements(\Doctrine\DBAL\Connection $db):array
    {
        $id=$db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform?'UUID':'VARCHAR(36)';
        return ["CREATE TABLE wcp_candidate_activations (id $id PRIMARY KEY, plan_job_id $id NOT NULL UNIQUE REFERENCES wcp_candidate_jobs(id), tenant_id $id NOT NULL, site_id $id NOT NULL, actor_id $id NOT NULL REFERENCES wcp_candidate_users(id), expected_revision INTEGER NOT NULL CHECK(expected_revision=2), state VARCHAR(32) NOT NULL CHECK(state IN ('QUEUED','DISPATCHING','ACTIVE','FAILED','UNKNOWN_OUTCOME')), created_at VARCHAR(32) NOT NULL, completed_at VARCHAR(32) NULL, FOREIGN KEY(tenant_id,site_id) REFERENCES wcp_candidate_sites(tenant_id,id))"];
    }
}
