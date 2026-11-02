<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;

/** Additive candidate topology metadata; no runtime migration or activation authority. */
final class SiteRuntimeProfileSchema
{
    /** Apply the same statements atomically to disposable fixtures.
     * @internal
     */
    public static function upgrade(Connection $db): void
    {
        $db->transactional(static function () use ($db): void {
            foreach (self::statements($db) as $statement) $db->executeStatement($statement);
        });
    }

    /** @return list<string> */
    public static function statements(Connection $db): array
    {
        $platform = $db->getDatabasePlatform();
        if (!$platform instanceof PostgreSQLPlatform && !$platform instanceof SQLitePlatform) {
            throw new \RuntimeException('Unsupported candidate site runtime profile database.');
        }

        // No default profile: an old writer must fail rather than mislabel a Worker site.
        $plan = [
            'ALTER TABLE wcp_candidate_sites ADD COLUMN web_stack VARCHAR(32)',
            'ALTER TABLE wcp_candidate_sites ADD COLUMN stack_revision INTEGER NOT NULL DEFAULT 1 CONSTRAINT wcp_candidate_site_runtime_revision CHECK (stack_revision = 1)',
            "UPDATE wcp_candidate_sites SET web_stack = CASE application_server WHEN 'php_fpm' THEN 'classic' WHEN 'frankenphp_worker' THEN 'advanced' ELSE NULL END",
        ];
        $pair = "((web_stack = 'classic' AND application_server = 'php_fpm') OR (web_stack = 'advanced' AND application_server = 'frankenphp_worker'))";
        if ($platform instanceof PostgreSQLPlatform) {
            $plan[] = 'ALTER TABLE wcp_candidate_sites ALTER COLUMN web_stack SET NOT NULL';
            $plan[] = 'ALTER TABLE wcp_candidate_sites ADD CONSTRAINT wcp_candidate_site_runtime_profile CHECK '.$pair;
        } else {
            // Preserve referenced tables and active foreign keys; validate all future row updates.
            $invalid = "typeof(NEW.web_stack) <> 'text' OR NEW.web_stack NOT IN ('classic','advanced') OR NOT ((NEW.web_stack = 'classic' AND NEW.application_server = 'php_fpm') OR (NEW.web_stack = 'advanced' AND NEW.application_server = 'frankenphp_worker')) OR typeof(NEW.stack_revision) <> 'integer' OR NEW.stack_revision <> 1";
            foreach (['insert' => 'INSERT', 'update' => 'UPDATE'] as $name => $event) {
                $plan[] = "CREATE TRIGGER wcp_candidate_site_runtime_$name BEFORE $event ON wcp_candidate_sites WHEN $invalid BEGIN SELECT RAISE(ABORT, 'candidate site runtime profile required'); END";
            }
            // Unlike PostgreSQL SET NOT NULL/CHECK, a new SQLite trigger does not inspect old rows.
            $plan[] = 'UPDATE wcp_candidate_sites SET web_stack = web_stack';
        }
        return $plan;
    }
}
