<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Fixtures;

use Doctrine\DBAL\Connection;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

/** Synthetic rows only; caller already owns the disposable fixture database. */
final class HostingJobsFixture
{
    public static function seed(Connection $db, array $fixture, int $count, string $name = 'alice', int $offset = 0): array
    {
        $ids = [];
        for ($i = 1; $i <= $count; $i++) {
            $id = sprintf('00000000-0000-7000-8000-%012d', $offset + $i);
            $site = WorkbenchStore::id();
            $state = $i % 2 === 0 ? 'SUCCEEDED' : 'QUEUED';
            $db->insert('wcp_candidate_sites', [
                'id' => $site, 'tenant_id' => $fixture['tenants'][$name], 'hostname' => 'job-'.$name.'-'.($offset + $i).'.test',
                'state' => $state === 'SUCCEEDED' ? 'PLAN_READY' : 'PLAN_QUEUED', 'revision' => 1,
                'application_server' => 'php_fpm', 'worker_acknowledged' => 0,
                'web_stack' => 'classic', 'stack_revision' => 1,
            ]);
            $db->insert('wcp_candidate_jobs', [
                'id' => $id, 'tenant_id' => $fixture['tenants'][$name], 'site_id' => $site, 'actor_id' => $fixture['users'][$name],
                'idempotency_key' => 'synthetic-job-key-'.$offset.'-'.$i, 'fingerprint' => str_repeat('a', 64),
                'state' => $state, 'result' => $state === 'SUCCEEDED' ? '{"private_result_marker":"not-a-list-field"}' : null,
                'created_at' => '2026-11-02T00:00:00Z', 'started_at' => null, 'completed_at' => null,
            ]);
            $ids[] = $id;
        }

        return $ids;
    }
}
