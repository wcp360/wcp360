<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\NativeClock;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Security\ContractViolation;
use Wcp360\Core\Shared\Security\SessionKind;
use Wcp360\Core\Shared\Security\SessionLifetimePolicy;

/** Durable candidate revocations; the UUID selector alone never authenticates a browser. */
final readonly class CandidateSessions
{
    private ClockInterface $clock;

    public function __construct(private WorkbenchStore $store, ?ClockInterface $clock = null)
    {
        $this->clock = $clock ?? new NativeClock();
    }

    public static function binding(string $actor, string $surface, #[\SensitiveParameter] string $sid): string
    {
        self::table($surface);
        if (!Uuid::isValid($actor) || !preg_match('/\A[A-Za-z0-9,-]{16,256}\z/D', $sid)) {
            throw new \InvalidArgumentException('Invalid candidate session binding.');
        }

        return hash('sha256', 'wcp360.candidate.session/1|'.$surface.'|'.$actor.'|'.$sid);
    }

    public function issue(string $actor, string $surface, #[\SensitiveParameter] string $sid): string
    {
        $table = self::table($surface);
        $binding = self::binding($actor, $surface, $sid);

        return $this->store->db->transactional(function () use ($actor, $surface, $table, $binding): string {
            $this->requireActor($actor, $surface, true);
            $now = $this->now();
            $selector = WorkbenchStore::id();
            $this->store->db->insert($table, [
                'id' => $selector, 'user_id' => $actor, 'secret_verifier' => $binding,
                'surface_claim' => $surface === 'admin' ? 'WCP-ADMIN' : 'WCP-USER',
                'auth_strength' => 1, 'created_at' => $now, 'last_seen_at' => $now,
                'expires_at' => $now + self::absoluteLifetime($surface), 'revoked_at' => null,
            ]);
            $this->store->audit($actor, null, 'candidate.session.opened', $selector);

            return $selector;
        });
    }

    /**
     * Database state and the clock may change between calls, including when touch is false.
     * @phpstan-impure
     */
    public function current(string $actor, string $surface, string $selector, #[\SensitiveParameter] string $sid, int $minimumStrength = 1, bool $touch = true): bool
    {
        if (!in_array($surface, ['user', 'admin'], true) || !Uuid::isValid($actor) || !Uuid::isValid($selector)
            || !in_array($minimumStrength, [1, 2], true)) {
            return false;
        }
        try {
            $binding = self::binding($actor, $surface, $sid);
        } catch (\InvalidArgumentException) {
            return false;
        }
        $table = self::table($surface);
        $row = $this->store->db->fetchAssociative("SELECT s.* FROM $table s JOIN wcp_candidate_users u ON u.id = s.user_id WHERE s.id = ? AND s.user_id = ? AND u.context = ? AND u.active = 1", [$selector, $actor, $surface]);
        $now = $this->now();
        if (!$row || !hash_equals((string) $row['secret_verifier'], $binding) || !$this->valid($row, $surface, $now)
            || (int) $row['auth_strength'] < $minimumStrength) {
            return false;
        }
        if (!$touch) {
            return true;
        }

        return 1 === $this->store->db->executeStatement("UPDATE $table SET last_seen_at = CASE WHEN last_seen_at > ? THEN last_seen_at ELSE ? END WHERE id = ? AND user_id = ? AND secret_verifier = ? AND revoked_at IS NULL AND auth_strength = ? AND expires_at = ? AND EXISTS (SELECT 1 FROM wcp_candidate_users u WHERE u.id = ? AND u.context = ? AND u.active = 1)", [$now, $now, $selector, $actor, $binding, $row['auth_strength'], $row['expires_at'], $actor, $surface]);
    }

    /** Called only after the HTTP boundary has verified real administrator MFA. */
    public function upgrade(string $actor, string $surface, string $selector, string $oldBinding, #[\SensitiveParameter] string $sid): void
    {
        if ($surface !== 'admin' || !Uuid::isValid($selector) || !preg_match('/\A[a-f0-9]{64}\z/D', $oldBinding)) {
            throw new AccessDeniedHttpException('Current pending administrator session required.');
        }
        $binding = self::binding($actor, $surface, $sid);
        if (hash_equals($oldBinding, $binding)) {
            throw new AccessDeniedHttpException('Administrator session rotation required.');
        }
        $this->store->db->transactional(function () use ($actor, $surface, $selector, $oldBinding, $binding): void {
            $this->requireActor($actor, $surface, true);
            $row = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_admin_sessions WHERE id = ? AND user_id = ?', [$selector, $actor]);
            $now = $this->now();
            if (!$row || !$this->valid($row, $surface, $now) || (int) $row['auth_strength'] !== 1
                || !hash_equals((string) $row['secret_verifier'], $oldBinding)) {
                throw new AccessDeniedHttpException('Current pending administrator session required.');
            }
            $changed = $this->store->db->executeStatement('UPDATE wcp_candidate_admin_sessions SET secret_verifier = ?, auth_strength = 2, last_seen_at = CASE WHEN last_seen_at > ? THEN last_seen_at ELSE ? END WHERE id = ? AND user_id = ? AND secret_verifier = ? AND auth_strength = 1 AND revoked_at IS NULL', [$binding, $now, $now, $selector, $actor, $oldBinding]);
            if ($changed !== 1) {
                throw new AccessDeniedHttpException('Current pending administrator session required.');
            }
            $this->store->audit($actor, null, 'candidate.session.rotated', $selector);
        });
    }

    /** Revokes a bound local session even after its principal has been deactivated. */
    public function retire(string $actor, string $surface, string $selector, string $binding): bool
    {
        $table = self::table($surface);
        if (!Uuid::isValid($actor) || !Uuid::isValid($selector) || !preg_match('/\A[a-f0-9]{64}\z/D', $binding)) {
            return false;
        }

        return $this->store->db->transactional(function () use ($actor, $surface, $table, $selector, $binding): bool {
            $this->requireActor($actor, $surface, true, false);
            $row = $this->store->db->fetchAssociative("SELECT last_seen_at, revoked_at FROM $table WHERE id = ? AND user_id = ? AND secret_verifier = ?", [$selector, $actor, $binding]);
            if (!$row || $row['revoked_at'] !== null) {
                return false;
            }
            $now = max($this->now(), (int) $row['last_seen_at']);
            $changed = $this->store->db->executeStatement("UPDATE $table SET revoked_at = CASE WHEN last_seen_at > ? THEN last_seen_at ELSE ? END WHERE id = ? AND user_id = ? AND secret_verifier = ? AND revoked_at IS NULL", [$now, $now, $selector, $actor, $binding]);
            if ($changed === 1) {
                $this->store->audit($actor, null, 'candidate.session.revoked', $selector);
            }

            return $changed === 1;
        });
    }

    /** Consumed after Origin/CSRF checks and committed before any password verification. */
    public function allowReauthentication(string $actor, string $surface, string $selector, #[\SensitiveParameter] string $sid): bool
    {
        if (!in_array($surface, ['user', 'admin'], true) || !Uuid::isValid($actor) || !Uuid::isValid($selector)) {
            return false;
        }
        try {
            $binding = self::binding($actor, $surface, $sid);
        } catch (\InvalidArgumentException) {
            return false;
        }
        $table = self::table($surface);

        return $this->store->db->transactional(function () use ($actor, $surface, $table, $selector, $binding): bool {
            try {
                $this->requireActor($actor, $surface, true);
            } catch (AccessDeniedHttpException) {
                return false;
            }
            $row = $this->store->db->fetchAssociative("SELECT * FROM $table WHERE id = ? AND user_id = ? AND secret_verifier = ?", [$selector, $actor, $binding]);
            $now = $this->now();
            if (!$row || !$this->valid($row, $surface, $now)) {
                return false;
            }
            $started = (int) $row['reauth_window_started'];
            $attempts = (int) $row['reauth_attempts'];
            if ($now < $started || ($attempts >= 5 && $now < $started + 900)) {
                return false;
            }
            if ($attempts === 0 || $now >= $started + 900) {
                $started = $now;
                $attempts = 0;
            }
            $changed = $this->store->db->executeStatement("UPDATE $table SET reauth_window_started = ?, reauth_attempts = ? WHERE id = ? AND user_id = ? AND secret_verifier = ? AND revoked_at IS NULL AND reauth_window_started = ? AND reauth_attempts = ?", [$started, $attempts + 1, $selector, $actor, $binding, $row['reauth_window_started'], $row['reauth_attempts']]);
            if ($changed !== 1) {
                return false;
            }
            $this->store->audit($actor, null, 'candidate.session.reauthentication_attempted', $selector);

            return true;
        });
    }

    /** @return array{items:list<array{id:string,created_at:int,last_seen_at:int,expires_at:int,revoked_at:?int,auth_strength:int,state:string,current:bool}>,page:int,has_next:bool} */
    public function listing(string $actor, string $surface, string $currentSelector, int $page = 1, int $requiredStrength = 1): array
    {
        $table = self::table($surface);
        if ($page < 1 || $page > 10000) {
            throw new \InvalidArgumentException('Invalid candidate session page.');
        }
        if (!in_array($requiredStrength, [1, 2], true)) {
            throw new \InvalidArgumentException('Invalid candidate session display policy.');
        }
        $this->requireActor($actor, $surface);
        $rows = $this->store->db->fetchAllAssociative("SELECT id, created_at, last_seen_at, expires_at, revoked_at, auth_strength, surface_claim FROM $table WHERE user_id = ? ORDER BY created_at DESC, id DESC LIMIT 101 OFFSET ".(($page - 1) * 100), [$actor]);
        $hasNext = count($rows) > 100;
        $now = $this->now();
        $items = [];
        // Display policy only: admission independently checks the current account policy.
        foreach (array_slice($rows, 0, 100) as $row) {
            $state = $row['revoked_at'] !== null ? 'revoked' : (!$this->valid($row, $surface, $now) ? 'expired' : ((int) $row['auth_strength'] < $requiredStrength ? 'pending' : 'active'));
            $items[] = [
                'id' => (string) $row['id'], 'created_at' => (int) $row['created_at'],
                'last_seen_at' => (int) $row['last_seen_at'], 'expires_at' => (int) $row['expires_at'],
                'revoked_at' => $row['revoked_at'] === null ? null : (int) $row['revoked_at'],
                'auth_strength' => (int) $row['auth_strength'],
                'state' => $state, 'current' => $row['id'] === $currentSelector,
            ];
        }

        return ['items' => $items, 'page' => $page, 'has_next' => $hasNext];
    }

    public function revoke(string $actor, string $surface, string $targetSelector): bool
    {
        $table = self::table($surface);
        if (!Uuid::isValid($targetSelector)) {
            throw new AccessDeniedHttpException('Own candidate session required.');
        }

        return $this->store->db->transactional(function () use ($actor, $surface, $table, $targetSelector): bool {
            $this->requireActor($actor, $surface, true);
            $row = $this->store->db->fetchAssociative("SELECT last_seen_at, revoked_at FROM $table WHERE id = ? AND user_id = ?", [$targetSelector, $actor]);
            if (!$row) {
                throw new AccessDeniedHttpException('Own candidate session required.');
            }
            if ($row['revoked_at'] !== null) {
                return false;
            }
            $now = $this->now();
            $this->store->db->executeStatement("UPDATE $table SET revoked_at = CASE WHEN last_seen_at > ? THEN last_seen_at ELSE ? END WHERE id = ? AND user_id = ?", [$now, $now, $targetSelector, $actor]);
            $this->store->audit($actor, null, 'candidate.session.revoked', $targetSelector);

            return true;
        });
    }

    public function revokeAll(string $actor, string $surface): int
    {
        $table = self::table($surface);

        return $this->store->db->transactional(function () use ($actor, $surface, $table): int {
            $this->requireActor($actor, $surface, true);
            $rows = $this->store->db->fetchAllAssociative("SELECT id, last_seen_at FROM $table WHERE user_id = ? AND revoked_at IS NULL", [$actor]);
            $now = $this->now();
            foreach ($rows as $row) {
                $this->store->db->executeStatement("UPDATE $table SET revoked_at = CASE WHEN last_seen_at > ? THEN last_seen_at ELSE ? END WHERE id = ? AND user_id = ?", [$now, $now, $row['id'], $actor]);
                $this->store->audit($actor, null, 'candidate.session.revoked', (string) $row['id']);
            }

            return count($rows);
        });
    }

    /** Revoke the current account's other registrations without loading their metadata. */
    public function revokeOthers(string $actor, string $surface, string $selector,
        #[\SensitiveParameter] string $sessionId, int $requiredStrength = 1): int
    {
        $table = self::table($surface);

        return $this->store->db->transactional(function () use ($actor, $surface, $table, $selector, $sessionId, $requiredStrength): int {
            $this->requireActor($actor, $surface, true);
            if (!$this->current($actor, $surface, $selector, $sessionId, $requiredStrength, false)) {
                throw new AccessDeniedHttpException('Current bound session required.');
            }
            $now = $this->now();
            $changed = $this->store->db->executeStatement("UPDATE $table SET revoked_at = CASE WHEN last_seen_at > ? THEN last_seen_at ELSE ? END WHERE user_id = ? AND id <> ? AND revoked_at IS NULL", [$now, $now, $actor, $selector]);
            if (!$this->current($actor, $surface, $selector, $sessionId, $requiredStrength, false)) {
                throw new AccessDeniedHttpException('Current bound session required.');
            }
            if ($changed > 0) {
                // One aggregate event references the retained selector; raw bindings stay private.
                $this->store->audit($actor, null, 'candidate.session.others_revoked', $selector);
            }

            return (int) $changed;
        });
    }

    private static function table(string $surface): string
    {
        return match ($surface) {
            'user' => 'wcp_candidate_user_sessions',
            'admin' => 'wcp_candidate_admin_sessions',
            default => throw new \InvalidArgumentException('Invalid candidate session surface.'),
        };
    }

    private static function absoluteLifetime(string $surface): int
    {
        return $surface === 'admin' ? 8 * 3600 : 12 * 3600;
    }

    private function requireActor(string $actor, string $surface, bool $lock = false, bool $active = true): void
    {
        if (!Uuid::isValid($actor)) {
            throw new AccessDeniedHttpException('Current candidate account required.');
        }
        $db = $this->store->db;
        if ($lock && $db->getDatabasePlatform() instanceof SQLitePlatform) {
            $db->executeStatement('UPDATE wcp_candidate_users SET active = active WHERE id = ? AND context = ?', [$actor, $surface]);
        }
        $suffix = $lock && $db->getDatabasePlatform() instanceof PostgreSQLPlatform ? ' FOR UPDATE' : '';
        if (!$db->fetchOne('SELECT id FROM wcp_candidate_users WHERE id = ? AND context = ?'.($active ? ' AND active = 1' : '').$suffix, [$actor, $surface])) {
            throw new AccessDeniedHttpException('Current candidate account required.');
        }
    }

    /** @param array<string, mixed> $row */
    private function valid(array $row, string $surface, int $now): bool
    {
        $created = (int) $row['created_at'];
        $last = (int) $row['last_seen_at'];
        if ($row['revoked_at'] !== null || $row['surface_claim'] !== ($surface === 'admin' ? 'WCP-ADMIN' : 'WCP-USER')
            || $last < $created || $now < $last || (int) $row['expires_at'] !== $created + self::absoluteLifetime($surface)
            || $now >= (int) $row['expires_at']) {
            return false;
        }
        try {
            return !SessionLifetimePolicy::isExpired($surface === 'admin' ? SessionKind::Admin : SessionKind::User, new \DateTimeImmutable('@'.$created), new \DateTimeImmutable('@'.$last), new \DateTimeImmutable('@'.$now));
        } catch (ContractViolation) {
            return false;
        }
    }

    private function now(): int
    {
        $now = $this->clock->now()->getTimestamp();
        if ($now < 0 || $now > PHP_INT_MAX - 12 * 3600) {
            throw new \RuntimeException('Candidate session clock is unavailable.');
        }

        return $now;
    }
}
