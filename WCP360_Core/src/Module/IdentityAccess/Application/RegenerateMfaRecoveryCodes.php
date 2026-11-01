<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\RecoveryAccountSnapshot;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Own ADMIN factor only; disclosure follows an atomic factor/session/audit commit. */
final readonly class RegenerateMfaRecoveryCodes
{
    public function __construct(private WorkbenchStore $store, private CandidateMfa $mfa,
        private CandidateSessions $sessions, private RuntimeContext $runtime) {}

    /**
     * Current password was checked by CurrentPasswordConfirmation.
     *
     * @param array<array-key,mixed>|null $proof Server-side proof; never request input.
     * @return list<string>
     */
    public function regenerate(#[\SensitiveParameter] Principal $actor, string $selector,
        #[\SensitiveParameter] string $sessionId, #[\SensitiveParameter] ?array $proof): array
    {
        if ($this->runtime->context !== 'admin' || $actor->context !== 'admin' || !$actor->active) {
            throw new AccessDeniedHttpException('Current administrator required.');
        }
        $db = $this->store->db;
        // Returning show-once data from a caller-owned uncommitted transaction is unsafe.
        if ($db->isTransactionActive()) {
            throw new ServiceUnavailableHttpException(null, 'Recovery code renewal is unavailable.');
        }
        $started = false;
        $committing = false;
        try {
            $db->beginTransaction();
            $started = true;
            if ($db->getDatabasePlatform() instanceof PostgreSQLPlatform) {
                $db->executeStatement("SET LOCAL lock_timeout = '2s'");
                $db->executeStatement("SET LOCAL statement_timeout = '5s'");
            }
            $this->store->requireAdmin($actor->id, true);
            $snapshot = RecoveryAccountSnapshot::find($db, $actor->id, 'admin');
            $now = time();
            if ($snapshot === null || $snapshot['actor']->username !== $actor->username
                || $snapshot['actor']->email !== $actor->email
                || !hash_equals($snapshot['actor']->getPassword(), $actor->getPassword())
                || $proof === null || ($proof['actor'] ?? null) !== $actor->id
                || ($proof['revision'] ?? null) !== $snapshot['revision']
                || !is_int($proof['at'] ?? null) || $proof['at'] > $now || $proof['at'] + 300 <= $now
                || !$this->sessions->current($actor->id, 'admin', $selector, $sessionId, 2, false)) {
                throw new AccessDeniedHttpException('Recent administrator MFA required.');
            }
            $codes = $this->mfa->regenerateRecoveryCodes($actor->id, $snapshot['revision']);
            $this->sessions->revokeAll($actor->id, 'admin');
            $committing = true;
            $db->commit();

            return $codes;
        } catch (\Throwable $error) {
            $uncertain = $committing;
            if ($started) {
                try { if ($db->isTransactionActive()) $db->rollBack(); }
                catch (\Throwable) { $uncertain = true; }
            }
            if ($uncertain) $db->close();
            // Do not retain a driver cause or generated codes in an exception chain.
            if (!$uncertain && $error instanceof AccessDeniedHttpException) {
                throw new AccessDeniedHttpException('Recent administrator MFA required.');
            }
            throw new ServiceUnavailableHttpException(null, $uncertain
                ? 'Recovery code renewal outcome is uncertain. Sign in again before reviewing account security.'
                : 'Recovery code renewal is unavailable. No new codes can be displayed.');
        }
    }
}
