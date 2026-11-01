<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

/** Fixed native-PDO transport; no connection escalation, SQL profiling or generic function selector. */
final readonly class PasswordChangeRoutine
{
    public function __construct(private Connection $db) {}

    public function available(string $surface): bool
    {
        if (!$this->db->getDatabasePlatform() instanceof PostgreSQLPlatform || !in_array($surface, ['user', 'admin'], true)) return false;
        try {
            $pdo = $this->db->getNativeConnection();
            if (!$pdo instanceof \PDO) return false;
            $statement = $pdo->prepare("SELECT current_user = session_user AND pg_catalog.current_setting('role') = 'none'
                AND session_user::text = CASE pg_catalog.current_database()
                    WHEN 'wcp360_test' THEN 'wcp360_test_' || ?::text
                    WHEN 'wcp360_workbench' THEN 'wcp360_dev_' || ?::text ELSE NULL END
                AND pg_catalog.has_function_privilege(current_user, pg_catalog.to_regprocedure(?), 'EXECUTE')");
            $statement->execute([$surface, $surface, PasswordChangeSchema::signature($surface)]);
            return in_array($statement->fetchColumn(), [true, 1, '1', 't'], true);
        } catch (\Throwable) {
            return false;
        }
    }

    /** @param array<array-key,mixed>|null $proof */
    public function change(#[\SensitiveParameter] Principal $actor, string $selector,
        #[\SensitiveParameter] string $sid, #[\SensitiveParameter] string $hash, ?array $proof): void
    {
        $name = PasswordChangeSchema::name($actor->context);
        $revision = $proof['revision'] ?? null;
        $at = $proof['at'] ?? null;
        if ($actor->context === 'admin' && (($proof['actor'] ?? null) !== $actor->id || !is_int($revision) || !is_int($at))) {
            throw new AccessDeniedHttpException('Current administrator proof required.');
        }
        try {
            $pdo = $this->db->getNativeConnection();
            if (!$pdo instanceof \PDO) throw new \RuntimeException('Credential connection unavailable.');
            $statement = $pdo->prepare('SELECT '.$name.'(?::uuid, ?::text, ?::text, ?::text, ?::uuid, ?::text, ?::integer, ?::bigint)');
            $statement->execute([$actor->id, $actor->username, $actor->getPassword(), $hash, $selector, $sid,
                is_int($revision) ? $revision : null, is_int($at) ? $at : null]);
            if (!in_array($statement->fetchColumn(), [true, 1, '1', 't'], true)) throw new \RuntimeException('Credential change outcome unavailable.');
        } catch (\PDOException $error) {
            if (($error->errorInfo[0] ?? null) === '42501') throw new AccessDeniedHttpException('Credential change refused.');
            // Driver errors can contain credential verifiers or private connection details.
            throw new \RuntimeException('Credential change failed. Sign in again before retrying.');
        }
    }
}
