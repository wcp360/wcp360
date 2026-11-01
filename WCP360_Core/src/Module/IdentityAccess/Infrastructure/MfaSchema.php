<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

/** Frozen migration input for candidate MFA; not a canonical production IAM schema. */
final class MfaSchema
{
    /** @return list<string> */
    public static function statements(Connection $db): array
    {
        $id = $db->getDatabasePlatform() instanceof PostgreSQLPlatform ? 'UUID' : 'VARCHAR(36)';

        return [
            "CREATE TABLE wcp_candidate_mfa (
                user_id $id PRIMARY KEY REFERENCES wcp_candidate_users(id),
                sealed_secret VARCHAR(512) NULL,
                pending_secret VARCHAR(512) NULL,
                pending_expires_at BIGINT NULL,
                revision INTEGER NOT NULL DEFAULT 1 CHECK(revision >= 1),
                last_counter BIGINT NOT NULL DEFAULT -1 CHECK(last_counter >= -1),
                failed_window_started BIGINT NOT NULL DEFAULT 0 CHECK(failed_window_started >= 0),
                failed_attempts INTEGER NOT NULL DEFAULT 0 CHECK(failed_attempts BETWEEN 0 AND 5),
                enrollment_window_started BIGINT NOT NULL DEFAULT 0 CHECK(enrollment_window_started >= 0),
                enrollment_attempts INTEGER NOT NULL DEFAULT 0 CHECK(enrollment_attempts BETWEEN 0 AND 5),
                CHECK((pending_secret IS NULL AND pending_expires_at IS NULL) OR (pending_secret IS NOT NULL AND pending_expires_at IS NOT NULL)),
                CHECK(sealed_secret IS NULL OR pending_secret IS NULL)
            )",
            "CREATE TABLE wcp_candidate_mfa_recovery (
                user_id $id NOT NULL REFERENCES wcp_candidate_mfa(user_id),
                code_hash VARCHAR(64) NOT NULL CHECK(length(code_hash) = 64),
                PRIMARY KEY(user_id, code_hash)
            )",
        ];
    }
}
