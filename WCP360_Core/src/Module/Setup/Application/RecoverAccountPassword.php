<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Application;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Domain\CandidateUsername;
use Wcp360\Core\Module\IdentityAccess\Domain\NewAccountPassword;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Operator-only candidate recovery; never a web reset or an MFA bypass. */
final readonly class RecoverAccountPassword
{
    public const REFUSED = 'Account recovery refused. Check the maintenance context, database, account and password policy.';
    public const UNCERTAIN = 'Account recovery outcome is uncertain. Inspect the account and audit before another attempt.';

    public function __construct(
        private WorkbenchStore $store,
        private CandidateSessions $sessions,
        private UserPasswordHasherInterface $hasher,
        private RuntimeContext $runtime,
        private bool $enabled = false,
    ) {}

    public function enabled(): bool
    {
        return $this->runtime->context === 'setup' && $this->enabled;
    }

    /** @return array{id:string,username:string,surface:string,apply_enabled:bool} */
    public function plan(string $username, string $surface): array
    {
        $this->requireContext();
        self::requireTarget($username, $surface);
        try {
            $pdo = $this->maintenance();
            $statement = $pdo->prepare('SELECT id FROM wcp_candidate_users WHERE username = ? AND context = ? AND active = 1');
            $statement->execute([$username, $surface]);
            $id = $statement->fetchColumn();
            if (!is_string($id) || !Uuid::isValid($id)) throw new \RuntimeException(self::REFUSED);
            return ['id' => $id, 'username' => $username, 'surface' => $surface, 'apply_enabled' => $this->enabled()];
        } catch (\Throwable) {
            throw new \RuntimeException(self::REFUSED);
        }
    }

    /** The target ID binds the inspected account across the interactive prompts.
     * @return array{id:string,username:string,surface:string,revoked_sessions:int}
     */
    public function recover(string $id, string $username, string $surface, #[\SensitiveParameter] string $password): array
    {
        $this->requireContext();
        if (!$this->enabled() || !Uuid::isValid($id)) throw new \RuntimeException(self::REFUSED);
        self::requireTarget($username, $surface);
        NewAccountPassword::require($password);
        $db = $this->store->db;
        $started = false;
        $committing = false;
        try {
            $pdo = $this->maintenance();
            // Never take ownership of another operation's transaction.
            if ($db->isTransactionActive() || $pdo->inTransaction()) throw new \RuntimeException(self::REFUSED);
            $statement = $pdo->prepare('SELECT id, username, email, password_hash, context FROM wcp_candidate_users WHERE id = ? AND username = ? AND context = ? AND active = 1');
            $statement->execute([$id, $username, $surface]);
            $row = $statement->fetch(\PDO::FETCH_ASSOC);
            if (!is_array($row)) throw new \RuntimeException(self::REFUSED);
            $actor = new Principal((string) $row['id'], (string) $row['username'], (string) $row['email'],
                (string) $row['password_hash'], (string) $row['context']);
            if ($this->hasher->isPasswordValid($actor, $password)) throw new \RuntimeException(self::REFUSED);
            $hash = $this->hasher->hashPassword($actor, $password);
            unset($password, $row);
            $db->beginTransaction();
            $started = true;
            if ($db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
                $pdo->exec("SET LOCAL lock_timeout = '2s'");
                $pdo->exec("SET LOCAL statement_timeout = '5s'");
            }
            // Hashes bypass Doctrine parameter profiling; compare-and-swap rejects concurrent changes.
            $write = $pdo->prepare('UPDATE wcp_candidate_users SET password_hash = ? WHERE id = ? AND username = ? AND context = ? AND active = 1 AND password_hash = ?');
            $write->execute([$hash, $id, $username, $surface, $actor->getPassword()]);
            if ($write->rowCount() !== 1) throw new \RuntimeException(self::REFUSED);
            $revoked = $this->sessions->revokeAll($id, $surface);
            // A maintenance operator is not represented as the affected web account.
            $this->store->audit(null, null, 'candidate.account.password_recovered', $id);
            $committing = true;
            $db->commit();
            return ['id' => $id, 'username' => $username, 'surface' => $surface, 'revoked_sessions' => $revoked];
        } catch (\Throwable) {
            $uncertain = $committing;
            if ($started) {
                try { if ($db->isTransactionActive()) $db->rollBack(); }
                catch (\Throwable) { $uncertain = true; }
            }
            if ($uncertain) $db->close();
            // Drop driver/hasher causes: they may contain credentials or private connection data.
            throw new \RuntimeException($uncertain ? self::UNCERTAIN : self::REFUSED);
        }
    }

    private function requireContext(): void
    {
        if ($this->runtime->context !== 'setup') throw new \RuntimeException(self::REFUSED);
    }

    private static function requireTarget(string $username, string $surface): void
    {
        if (!CandidateUsername::valid($username) || !in_array($surface, ['user', 'admin'], true)) {
            throw new \RuntimeException(self::REFUSED);
        }
    }

    private function maintenance(): \PDO
    {
        $db = $this->store->db;
        $platform = $db->getDatabasePlatform();
        if (!$platform instanceof PostgreSQLPlatform && !$platform instanceof SQLitePlatform) throw new \RuntimeException(self::REFUSED);
        $pdo = $db->getNativeConnection();
        if (!$pdo instanceof \PDO) throw new \RuntimeException(self::REFUSED);
        if ($platform instanceof PostgreSQLPlatform) {
            $identity = $pdo->query(<<<'SQL'
SELECT current_user = session_user AND pg_catalog.current_setting('role') = 'none'
    AND pg_catalog.current_setting('server_version_num')::integer BETWEEN 170000 AND 179999
    AND pg_catalog.current_database() IN ('wcp360_workbench', 'wcp360_test')
    AND (r.rolsuper OR (
        r.oid = (SELECT datdba FROM pg_catalog.pg_database WHERE datname = pg_catalog.current_database())
        AND 4 = (SELECT count(*) FROM pg_catalog.pg_class c
            JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
            WHERE n.nspname = 'public' AND c.relkind = 'r' AND c.relowner = r.oid
              AND c.relname IN ('wcp_candidate_users', 'wcp_candidate_user_sessions',
                  'wcp_candidate_admin_sessions', 'wcp_candidate_audit'))
    )) AS allowed
FROM pg_catalog.pg_roles r WHERE r.rolname = current_user
SQL);
            if ($identity === false || !in_array($identity->fetchColumn(), [true, 1, '1', 't'], true)) {
                throw new \RuntimeException(self::REFUSED);
            }
            $pdo->exec('SET search_path TO pg_catalog, public, pg_temp');
        }
        return $pdo;
    }
}
