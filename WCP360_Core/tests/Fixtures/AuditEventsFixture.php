<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Fixtures;

use Doctrine\DBAL\Connection;

final class AuditEventsFixture
{
    public static function id(int $number): string { return sprintf('00000000-0000-7000-8000-%012d', $number); }

    public static function seed(Connection $db, array $fixture, int $count = 120): array
    {
        $ids = [];
        for ($i = 1; $i <= $count; $i++) {
            $name = $i % 2 === 0 ? 'bob' : 'alice';
            $id = self::id($i);
            $db->insert('wcp_candidate_audit', ['id' => $id, 'actor_id' => $fixture['users'][$name],
                'tenant_id' => $fixture['tenants'][$name], 'kind' => $i % 2 === 0 ? 'hosting.plan.succeeded' : 'hosting.plan.queued',
                'resource_id' => self::id(10000 + $i), 'created_at' => $i <= 60 ? '2026-11-01T00:00:00Z' : '2026-11-02T00:00:00Z',
                'request_id' => str_repeat($i % 2 === 0 ? 'b' : 'a', 32)]);
            $ids[] = $id;
        }

        return $ids;
    }
}
