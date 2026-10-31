<?php
declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Infrastructure;
final class ActivationGrantsSchema
{
    /** @return list<string> */
    public static function statements(\Doctrine\DBAL\Connection $db):array
    {
        $id=$db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform?'UUID':'VARCHAR(36)';
        return ["CREATE TABLE wcp_candidate_activation_grants (user_id $id NOT NULL REFERENCES wcp_candidate_users(id), tenant_id $id NOT NULL REFERENCES wcp_candidate_tenants(id), PRIMARY KEY(user_id,tenant_id))"];
    }
}
