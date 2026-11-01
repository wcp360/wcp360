<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Operations\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

/** Additive candidate metadata. Historical rows stay NULL; no audit history is rewritten. */
final class PlanCorrelationSchema
{
    /** @return list<string> */
    public static function statements(Connection $db): array
    {
        $format = $db->getDatabasePlatform() instanceof PostgreSQLPlatform
            ? "request_id ~ '^[a-f0-9]{32}$'"
            : "length(request_id) = 32 AND request_id NOT GLOB '*[^a-f0-9]*'";

        return [
            'ALTER TABLE wcp_candidate_jobs ADD COLUMN request_id VARCHAR(32) NULL CHECK (request_id IS NULL OR ('.$format.'))',
            'ALTER TABLE wcp_candidate_audit ADD COLUMN request_id VARCHAR(32) NULL CHECK (request_id IS NULL OR ('.$format.'))',
            'CREATE INDEX wcp_candidate_audit_request ON wcp_candidate_audit(request_id,created_at,id)',
        ];
    }
}
