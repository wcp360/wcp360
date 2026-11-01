<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

/** Frozen migration input; no canonical IAM table or permission is introduced. */
final class PermissionDenialSchema
{
    /** @return list<string> */
    public static function statements(Connection $db): array
    {
        $id = $db->getDatabasePlatform() instanceof PostgreSQLPlatform ? 'UUID' : 'VARCHAR(36)';

        return [
            "CREATE TABLE wcp_candidate_permission_denials (
                user_id $id NOT NULL REFERENCES wcp_candidate_users(id),
                tenant_id $id NOT NULL REFERENCES wcp_candidate_tenants(id),
                permission VARCHAR(64) NOT NULL CHECK(permission = 'candidate.hosting.plan'),
                created_by $id NOT NULL REFERENCES wcp_candidate_users(id),
                created_at VARCHAR(32) NOT NULL,
                PRIMARY KEY(user_id, tenant_id, permission)
            )",
        ];
    }
}
