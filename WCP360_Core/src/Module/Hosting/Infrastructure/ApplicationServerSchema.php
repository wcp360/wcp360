<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Infrastructure;

/** Frozen migration 20261031000300 input; experimental tables, not canonical production schemas. */
final class ApplicationServerSchema
{
    /** @return list<string> */
    public static function statements(): array
    {
        return [
            "ALTER TABLE wcp_candidate_sites ADD COLUMN application_server VARCHAR(32) NOT NULL DEFAULT 'php_fpm' CHECK(application_server IN ('php_fpm','frankenphp_worker'))",
            "ALTER TABLE wcp_candidate_sites ADD COLUMN worker_acknowledged INTEGER NOT NULL DEFAULT 0 CHECK(worker_acknowledged IN (0,1) AND (application_server <> 'frankenphp_worker' OR worker_acknowledged=1))",
            "CREATE TABLE wcp_candidate_runtime_settings (id INTEGER PRIMARY KEY CHECK(id=1), application_server VARCHAR(32) NOT NULL CHECK(application_server IN ('php_fpm','frankenphp_worker')), revision INTEGER NOT NULL CHECK(revision>=1))",
            "INSERT INTO wcp_candidate_runtime_settings(id,application_server,revision) VALUES(1,'php_fpm',1)",
        ];
    }
}
