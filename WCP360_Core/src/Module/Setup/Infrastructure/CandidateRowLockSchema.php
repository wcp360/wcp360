<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

/** Frozen migration input: UPDATE on an immutable-zero marker permits PostgreSQL row locks. */
final class CandidateRowLockSchema
{
    /** @return list<string> */
    public static function statements(): array
    {
        $sql = [];
        foreach (['users', 'tenants', 'sites'] as $table) {
            $sql[] = 'ALTER TABLE wcp_candidate_'.$table.' ADD COLUMN lock_guard SMALLINT NOT NULL DEFAULT 0 CONSTRAINT wcp_candidate_'.$table.'_lock_guard_zero CHECK (lock_guard = 0)';
        }
        return $sql;
    }
}
