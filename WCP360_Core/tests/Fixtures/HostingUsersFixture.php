<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Fixtures;

use Doctrine\DBAL\Connection;

final class HostingUsersFixture
{
    public static function seed(Connection $db, array $fixture, int $count = 120): array
    {
        $hash = $db->fetchOne('SELECT password_hash FROM wcp_candidate_users WHERE id=?', [$fixture['users']['alice']]);
        $ids = [];
        for ($i = 1; $i <= $count; $i++) {
            $id = sprintf('00000000-0000-7000-8000-%012d', 300000 + $i);
            $name = sprintf('acct%04d', $i);
            $db->insert('wcp_candidate_users', ['id' => $id, 'username' => $name, 'email' => $name.'@example.test',
                'password_hash' => $hash, 'context' => 'user', 'active' => $i % 2]);
            $ids[] = $id;
        }

        return $ids;
    }
}
