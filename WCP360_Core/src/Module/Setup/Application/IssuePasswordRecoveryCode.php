<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Application;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Module\IdentityAccess\Domain\CandidateUsername;
use Wcp360\Core\Module\IdentityAccess\Domain\PasswordRecoveryCode;
use Wcp360\Core\Module\IdentityAccess\Domain\RecoveryKeySource;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\RecoveryAccountSnapshot;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateAccountMaintenance;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Explicit private operator handoff; no public issuance endpoint or mail transport. */
final readonly class IssuePasswordRecoveryCode
{
    public function __construct(private WorkbenchStore $store, private CandidateAccountMaintenance $maintenance,
        private RuntimeContext $runtime, private string $databaseMode,
        private RecoveryKeySource $keys, private bool $enabled = false) {}

    public function enabled(?string $surface = null): bool
    {
        return $this->runtime->context === 'setup' && $this->databaseMode === 'shared'
            && $this->enabled && ($surface === null || (in_array($surface, ['user','admin'], true)
                && PasswordRecoveryCode::configured($this->keys->forSurface($surface))));
    }

    public function issue(string $id, string $username, string $surface): string
    {
        if (!$this->enabled($surface) || !Uuid::isValid($id) || !CandidateUsername::valid($username)
            || !in_array($surface, ['user', 'admin'], true)) throw new \RuntimeException('Recovery code issuance refused.');
        $db = $this->store->db;
        $started = false;
        $committing = false;
        try {
            $pdo = $this->maintenance->connection();
            if ($db->isTransactionActive() || $pdo->inTransaction()) throw new \RuntimeException('Recovery code issuance refused.');
            $db->beginTransaction();
            $started = true;
            if ($db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
                $pdo->exec("SET LOCAL lock_timeout = '2s'");
                $pdo->exec("SET LOCAL statement_timeout = '5s'");
            }
            $snapshot = RecoveryAccountSnapshot::find($db, $id, $surface, true);
            if ($snapshot === null || $snapshot['actor']->username !== $username) throw new \RuntimeException('Recovery code issuance refused.');
            $code = PasswordRecoveryCode::issue($snapshot['actor'], $snapshot['revision'], $this->keys->forSurface($surface), time());
            $this->store->audit(null, null, 'candidate.account.recovery_code_issued', $id);
            $committing = true;
            $db->commit();
            return $code;
        } catch (\Throwable) {
            $uncertain = $committing;
            if ($started) {
                try { if ($db->isTransactionActive()) $db->rollBack(); }
                catch (\Throwable) { $uncertain = true; }
            }
            if ($uncertain) $db->close();
            throw new \RuntimeException($uncertain ? RecoverAccountPassword::UNCERTAIN : 'Recovery code issuance refused.');
        }
    }
}
