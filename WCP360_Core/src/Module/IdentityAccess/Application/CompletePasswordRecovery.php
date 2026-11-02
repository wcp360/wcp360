<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Wcp360\Core\Module\IdentityAccess\Domain\NewAccountPassword;
use Wcp360\Core\Module\IdentityAccess\Domain\PasswordRecoveryCode;
use Wcp360\Core\Module\IdentityAccess\Domain\RecoveryKeySource;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\RecoveryAccountSnapshot;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Bearer redemption only; successful credential CAS consumes every code from the old state. */
final readonly class CompletePasswordRecovery
{
    public const REFUSED = 'Recovery code is invalid, expired or no longer usable.';
    public const UNCERTAIN = 'Password recovery outcome is uncertain. Inspect the account and audit before retrying.';

    public function __construct(private WorkbenchStore $store, private CandidateSessions $sessions,
        private UserPasswordHasherInterface $hasher, private RuntimeContext $runtime, private string $databaseMode,
        private RecoveryKeySource $keys, private bool $enabled = false) {}

    public function available(): bool
    {
        return in_array($this->runtime->context, ['user', 'admin'], true) && $this->databaseMode === 'shared'
            && $this->enabled && PasswordRecoveryCode::configured($this->keys->forSurface($this->runtime->context));
    }

    public function limiterKey(#[\SensitiveParameter] string $client): string
    {
        if (!$this->available()) throw new \RuntimeException('Password recovery unavailable.');
        return hash_hmac('sha256', 'wcp360.candidate.recovery-limiter/1|'.$this->runtime->context.'|'.$client, $this->keys->forSurface($this->runtime->context));
    }

    public function complete(#[\SensitiveParameter] string $code, #[\SensitiveParameter] string $password): void
    {
        if (!$this->available()) throw new \RuntimeException('Password recovery unavailable.');
        NewAccountPassword::require($password);
        $db = $this->store->db;
        $started = false;
        $committing = false;
        try {
            $pdo = $db->getNativeConnection();
            if (!$pdo instanceof \PDO || $db->isTransactionActive() || $pdo->inTransaction()) throw new \RuntimeException(self::REFUSED);
            $id = PasswordRecoveryCode::subject($code, $this->runtime->context, time());
            if ($id === null) throw new \RuntimeException(self::REFUSED);
            $snapshot = RecoveryAccountSnapshot::find($db, $id, $this->runtime->context);
            if ($snapshot === null || !PasswordRecoveryCode::valid($code, $snapshot['actor'], $snapshot['revision'], $this->keys->forSurface($this->runtime->context), time())) {
                throw new \RuntimeException(self::REFUSED);
            }
            $actor = $snapshot['actor'];
            if ($this->hasher->isPasswordValid($actor, $password)) throw new \RuntimeException(self::REFUSED);
            $hash = $this->hasher->hashPassword($actor, $password);
            unset($password);
            $db->beginTransaction();
            $started = true;
            $pg = $db->getDatabasePlatform() instanceof PostgreSQLPlatform;
            if ($pg) {
                $pdo->exec("SET LOCAL lock_timeout = '2s'");
                $pdo->exec("SET LOCAL statement_timeout = '5s'");
            }
            $table = $pg ? 'public.wcp_candidate_users' : 'main.wcp_candidate_users';
            $write = $pdo->prepare('UPDATE '.$table.' SET password_hash = ? WHERE id = ? AND username = ?'
                .' AND email = ? AND context = ? AND active = 1 AND password_hash = ?');
            $write->execute([$hash, $actor->id, $actor->username, $actor->email, $actor->context, $actor->getPassword()]);
            // The account lock also serializes the existing MFA enrollment/recovery transitions.
            if ($write->rowCount() !== 1 || RecoveryAccountSnapshot::revision($db, $id, $actor->context) !== $snapshot['revision']
                || PasswordRecoveryCode::subject($code, $actor->context, time()) !== $id) {
                throw new \RuntimeException(self::REFUSED);
            }
            $this->sessions->revokeAll($id, $actor->context);
            $this->store->audit(null, null, 'candidate.account.password_reset', $id);
            $committing = true;
            $db->commit();
        } catch (\Throwable) {
            $uncertain = $committing;
            if ($started) {
                try { if ($db->isTransactionActive()) $db->rollBack(); }
                catch (\Throwable) { $uncertain = true; }
            }
            if ($uncertain) $db->close();
            // No driver/hasher cause, verifier, code or private URL reaches a debug response.
            throw new \RuntimeException($uncertain ? self::UNCERTAIN : self::REFUSED);
        }
    }
}
