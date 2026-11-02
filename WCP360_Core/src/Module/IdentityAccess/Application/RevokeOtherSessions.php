<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\RecoveryAccountSnapshot;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Current-password confirmation precedes this own-account transactional use case. */
final readonly class RevokeOtherSessions
{
    public function __construct(private WorkbenchStore $store, private CandidateSessions $sessions,
        private CandidateMfa $mfa, private RuntimeContext $runtime) {}

    /** @param array<array-key,mixed>|null $proof Server-side ADMIN proof, never request input. */
    public function revoke(#[\SensitiveParameter] Principal $actor, string $selector,
        #[\SensitiveParameter] string $sessionId, #[\SensitiveParameter] ?array $proof = null): int
    {
        if (!$actor->active || !in_array($actor->context, ['user', 'admin'], true)
            || $actor->context !== $this->runtime->context) {
            throw new AccessDeniedHttpException('Current account required.');
        }
        $db = $this->store->db;
        if ($db->isTransactionActive()) {
            throw new ServiceUnavailableHttpException(null, 'Session revocation is unavailable.');
        }
        $started = false;
        $committing = false;
        try {
            $db->beginTransaction();
            $started = true;
            if ($db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
                $db->executeStatement("SET LOCAL lock_timeout = '2s'");
                $db->executeStatement("SET LOCAL statement_timeout = '5s'");
            } elseif ($db->getDatabasePlatform() instanceof SQLitePlatform) {
                $db->executeStatement('UPDATE wcp_candidate_users SET lock_guard = 0 WHERE id = ? AND context = ?', [$actor->id, $actor->context]);
            }
            // Native PDO keeps credential snapshots outside Doctrine profiling.
            $snapshot = RecoveryAccountSnapshot::find($db, $actor->id, $actor->context, true);
            if ($snapshot === null || $snapshot['actor']->username !== $actor->username
                || $snapshot['actor']->email !== $actor->email
                || !hash_equals($snapshot['actor']->getPassword(), $actor->getPassword())) {
                throw new AccessDeniedHttpException('Current credentials required.');
            }
            $strength = 1;
            if ($actor->context === 'admin') {
                $status = $this->mfa->status($actor->id);
                $now = time();
                if ($proof === null || ($proof['actor'] ?? null) !== $actor->id
                    || ($proof['revision'] ?? null) !== $status['revision']
                    || !is_int($proof['at'] ?? null) || $proof['at'] > $now || $proof['at'] <= $now - 300
                    || ($status['enabled'] && !$status['configured'])) {
                    throw new AccessDeniedHttpException('Recent administrator authentication required.');
                }
                $strength = $status['enabled'] ? 2 : 1;
            }
            $count = $this->sessions->revokeOthers($actor->id, $actor->context, $selector, $sessionId, $strength);
            $committing = true;
            $db->commit();

            return $count;
        } catch (\Throwable $error) {
            $uncertain = $committing;
            if ($started) {
                try { if ($db->isTransactionActive()) $db->rollBack(); }
                catch (\Throwable) { $uncertain = true; }
            }
            if ($uncertain) $db->close();
            if (!$uncertain && $error instanceof AccessDeniedHttpException) {
                throw new AccessDeniedHttpException('Current account authentication required.');
            }
            // No verifier, SQL parameters or driver cause in the public exception.
            throw new ServiceUnavailableHttpException(null, $uncertain
                ? 'Session revocation outcome is uncertain. Sign in again and review your sessions.'
                : 'Session revocation is unavailable. Try again later.');
        }
    }
}
