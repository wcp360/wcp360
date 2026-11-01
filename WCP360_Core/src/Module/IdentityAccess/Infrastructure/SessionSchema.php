<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

/** Frozen candidate DDL; canonical IAM namespaces and production grants remain separate work. */
final class SessionSchema
{
    /** @return list<string> */
    public static function statements(Connection $db): array
    {
        $id = $db->getDatabasePlatform() instanceof PostgreSQLPlatform ? 'UUID' : 'VARCHAR(36)';
        $statements = [];
        foreach (['user' => 'WCP-USER', 'admin' => 'WCP-ADMIN'] as $surface => $claim) {
            $strength = $surface === 'admin' ? 'auth_strength IN (1, 2)' : 'auth_strength = 1';
            $statements[] = "CREATE TABLE wcp_candidate_{$surface}_sessions (
                id $id PRIMARY KEY,
                user_id $id NOT NULL REFERENCES wcp_candidate_users(id),
                secret_verifier VARCHAR(64) NOT NULL UNIQUE CHECK(length(secret_verifier) = 64),
                surface_claim VARCHAR(16) NOT NULL CHECK(surface_claim = '$claim'),
                auth_strength INTEGER NOT NULL CHECK($strength),
                created_at BIGINT NOT NULL CHECK(created_at >= 0),
                last_seen_at BIGINT NOT NULL CHECK(last_seen_at >= created_at),
                expires_at BIGINT NOT NULL CHECK(expires_at > created_at AND last_seen_at < expires_at),
                revoked_at BIGINT NULL CHECK(revoked_at IS NULL OR revoked_at >= last_seen_at),
                reauth_window_started BIGINT NOT NULL DEFAULT 0 CHECK(reauth_window_started >= 0),
                reauth_attempts INTEGER NOT NULL DEFAULT 0 CHECK(reauth_attempts BETWEEN 0 AND 5)
            )";
            $statements[] = "CREATE INDEX wcp_candidate_{$surface}_sessions_owner ON wcp_candidate_{$surface}_sessions (user_id, created_at, id)";
        }

        return $statements;
    }
}
