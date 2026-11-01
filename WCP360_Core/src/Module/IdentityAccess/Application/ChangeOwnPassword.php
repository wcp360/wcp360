<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Wcp360\Core\Module\IdentityAccess\Domain\NewAccountPassword;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Called after bounded current-password confirmation; no arbitrary target account. */
final readonly class ChangeOwnPassword
{
    public function __construct(private WorkbenchStore $store, private CandidateSessions $sessions,
        private CandidateMfa $mfa, private UserPasswordHasherInterface $passwords,
        private RuntimeContext $runtime, private string $databaseMode) {}

    public function available(): bool
    {
        return $this->databaseMode === 'shared' && in_array($this->runtime->context, ['user', 'admin'], true);
    }

    /** @param array<array-key,mixed>|null $proof Server-side administrator proof, never form input. */
    public function change(#[\SensitiveParameter] Principal $actor, string $selector,
        #[\SensitiveParameter] string $sessionId, #[\SensitiveParameter] string $newPassword, ?array $proof = null): void
    {
        if (!$this->available()) throw new ServiceUnavailableHttpException(null, 'Password change is unavailable on this instance.');
        if (!$actor->active || $actor->context !== $this->runtime->context) throw new AccessDeniedHttpException('Current account required.');
        NewAccountPassword::require($newPassword);
        if ($this->passwords->isPasswordValid($actor, $newPassword)) {
            throw new \InvalidArgumentException('Choose a password different from the current one.');
        }
        $hash = $this->passwords->hashPassword($actor, $newPassword);
        unset($newPassword);
        $this->store->db->transactional(function () use ($actor, $selector, $sessionId, $hash, $proof): void {
            // The conditional write locks the account before checking sessions/factor state.
            // Native PDO keeps credential verifiers out of Doctrine SQL parameter profiling.
            $pdo = $this->store->db->getNativeConnection();
            if (!$pdo instanceof \PDO) throw new \RuntimeException('Candidate credential connection unavailable.');
            $statement = $pdo->prepare('UPDATE wcp_candidate_users SET password_hash = ? WHERE id = ? AND context = ? AND active = 1 AND username = ? AND password_hash = ?');
            $statement->execute([$hash, $actor->id, $actor->context, $actor->username, $actor->getPassword()]);
            if ($statement->rowCount() !== 1) throw new AccessDeniedHttpException('Credentials changed. Sign in again.');
            $minimumStrength = 1;
            if ($actor->context === 'admin') {
                $status = $this->mfa->status($actor->id);
                $now = time();
                if ($proof === null || ($proof['actor'] ?? null) !== $actor->id
                    || ($proof['revision'] ?? null) !== $status['revision'] || !is_int($proof['at'] ?? null)
                    || $proof['at'] > $now || $proof['at'] + 300 <= $now) {
                    throw new AccessDeniedHttpException('Recent administrator authentication required.');
                }
                $minimumStrength = $status['enabled'] ? 2 : 1;
                if ($status['enabled'] && !$status['configured']) throw new AccessDeniedHttpException('Current administrator factor required.');
            }
            if (!$this->sessions->current($actor->id, $actor->context, $selector, $sessionId, $minimumStrength, false)) {
                throw new AccessDeniedHttpException('Current account session required.');
            }
            $this->sessions->revokeAll($actor->id, $actor->context);
            $this->store->audit($actor->id, null, 'candidate.account.password_changed', $actor->id);
        });
    }
}
