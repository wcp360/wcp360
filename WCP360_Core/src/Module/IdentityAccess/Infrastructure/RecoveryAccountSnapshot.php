<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

/** Native reads keep verifier-bearing state outside Doctrine parameter/result profiling. */
final class RecoveryAccountSnapshot
{
    /** @return array{actor:Principal,revision:int}|null */
    public static function find(Connection $db, string $id, string $surface, bool $lock = false): ?array
    {
        $pdo = $db->getNativeConnection();
        if (!$pdo instanceof \PDO) throw new \RuntimeException('Recovery connection unavailable.');
        $platform = $db->getDatabasePlatform();
        if (!$platform instanceof PostgreSQLPlatform && !$platform instanceof SQLitePlatform) throw new \RuntimeException('Recovery platform unavailable.');
        $pg = $platform instanceof PostgreSQLPlatform;
        $table = $pg ? 'public.wcp_candidate_users' : 'main.wcp_candidate_users';
        $statement = $pdo->prepare('SELECT id, username, email, password_hash, context FROM '.$table
            .' WHERE id = ? AND context = ? AND active = 1'.($pg && $lock ? ' FOR UPDATE' : ''));
        $statement->execute([$id, $surface]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);
        if (!is_array($row)) return null;
        return ['actor' => new Principal((string) $row['id'], (string) $row['username'], (string) $row['email'],
            (string) $row['password_hash'], (string) $row['context']), 'revision' => self::revision($db, $id, $surface)];
    }

    public static function revision(Connection $db, string $id, string $surface): int
    {
        if ($surface === 'user') return 0;
        if ($surface !== 'admin') throw new \RuntimeException('Recovery context unavailable.');
        $pdo = $db->getNativeConnection();
        if (!$pdo instanceof \PDO) throw new \RuntimeException('Recovery connection unavailable.');
        $table = $db->getDatabasePlatform() instanceof PostgreSQLPlatform ? 'public.wcp_candidate_mfa' : 'main.wcp_candidate_mfa';
        $statement = $pdo->prepare('SELECT revision FROM '.$table.' WHERE user_id = ?');
        $statement->execute([$id]);
        $revision = $statement->fetchColumn();
        return $revision === false ? 0 : (int) $revision;
    }
}
