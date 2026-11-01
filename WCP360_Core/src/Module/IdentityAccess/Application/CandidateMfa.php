<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Application;

use Doctrine\DBAL\Platforms\SQLitePlatform;
use OTPHP\TOTP;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\NativeClock;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

/**
 * Bounded development MFA. The caller owns browser authentication, CSRF/origin,
 * password reauthentication, fresh-MFA gates and SHOW_ONCE response handling.
 *
 * @phpstan-type Factor array{user_id: mixed, sealed_secret: mixed, pending_secret: mixed, pending_expires_at: mixed, revision: mixed, last_counter: mixed, failed_window_started: mixed, failed_attempts: mixed, enrollment_window_started: mixed, enrollment_attempts: mixed}
 */
final readonly class CandidateMfa
{
    private ClockInterface $clock;

    public function __construct(private WorkbenchStore $store, #[\SensitiveParameter] private string $key, ?ClockInterface $clock = null)
    {
        $this->clock = $clock ?? new NativeClock();
    }

    /** @return array{enabled: bool, revision: int, configured: bool} */
    public function status(string $actor): array
    {
        $this->store->requireAdmin($actor);
        $row = $this->store->db->fetchAssociative('SELECT sealed_secret, revision FROM wcp_candidate_mfa WHERE user_id = ?', [$actor]);
        $configured = preg_match('/^[a-fA-F0-9]{64}$/D', $this->key) === 1;
        if ($configured && $row !== false && is_string($row['sealed_secret'])) {
            try {
                $this->unseal($actor, $row['sealed_secret']);
            } catch (\Throwable) {
                $configured = false;
            }
        }

        return [
            'enabled' => $row !== false && $row['sealed_secret'] !== null,
            'revision' => $row === false ? 0 : (int) $row['revision'],
            'configured' => $configured,
        ];
    }

    /**
     * A new, five-minute pending seed is returned once. Existing active seeds are never returned.
     *
     * @return array{secret: string, uri: string, expires_at: int}
     */
    public function startEnrollment(string $actor): array
    {
        $this->requireKey();

        return $this->store->db->transactional(function () use ($actor): array {
            $row = $this->lockedFactor($actor);
            if ($row['sealed_secret'] !== null) {
                throw new \RuntimeException('MFA enrollment is unavailable.');
            }

            $now = $this->timestamp();
            [$started, $attempts] = $this->window((int) $row['enrollment_window_started'], (int) $row['enrollment_attempts'], $now);
            if ($attempts >= 5) {
                throw new \RuntimeException('MFA enrollment is unavailable.');
            }

            $totp = TOTP::generate($this->clock, 32);
            $totp->setIssuer('WCP360 candidate');
            $totp->setLabel('admin-'.$actor);
            $secret = $totp->getSecret();
            $expires = $now + 300;
            $this->store->db->update('wcp_candidate_mfa', [
                'pending_secret' => $this->seal($actor, $secret),
                'pending_expires_at' => $expires,
                'last_counter' => -1,
                'enrollment_window_started' => $started,
                'enrollment_attempts' => $attempts + 1,
            ], ['user_id' => $actor]);
            $this->store->audit($actor, null, 'candidate.mfa.enrollment.started', $actor);

            return ['secret' => $secret, 'uri' => $totp->getProvisioningUri(), 'expires_at' => $expires];
        });
    }

    /**
     * Failed attempts commit their counters; failure is a null result, not a rolled-back exception.
     * Recovery verifies a second factor after password authentication, not a password-reset channel.
     *
     * @return array{revision: int, method: string, recovery_codes?: list<string>}|null
     */
    public function check(string $actor, #[\SensitiveParameter] string $code, ?int $expectedRevision = null): ?array
    {
        $this->requireKey();

        return $this->store->db->transactional(function () use ($actor, $code, $expectedRevision): ?array {
            $row = $this->lockedFactor($actor);
            if ($expectedRevision !== null && (int) $row['revision'] !== $expectedRevision) {
                return null;
            }
            $now = $this->timestamp();
            [$started, $attempts] = $this->window((int) $row['failed_window_started'], (int) $row['failed_attempts'], $now);
            if ($attempts >= 5) {
                return null;
            }

            $enabled = is_string($row['sealed_secret']);
            if ($enabled && preg_match('/^[a-f0-9]{32}$/D', $code) === 1) {
                $removed = $this->store->db->delete('wcp_candidate_mfa_recovery', ['user_id' => $actor, 'code_hash' => $this->recoveryHash($actor, $code)]);
                if ($removed === 1) {
                    $revision = (int) $row['revision'] + 1;
                    $this->store->db->update('wcp_candidate_mfa', ['revision' => $revision, 'failed_attempts' => 0, 'failed_window_started' => 0], ['user_id' => $actor]);
                    $this->store->audit($actor, null, 'candidate.mfa.recovery.used', $actor);

                    return ['revision' => $revision, 'method' => 'recovery'];
                }
            }

            $sealed = $enabled ? $row['sealed_secret'] : $row['pending_secret'];
            $eligible = $enabled || ($row['pending_expires_at'] !== null && $now < (int) $row['pending_expires_at']);
            $counter = null;
            if ($eligible && is_string($sealed) && preg_match('/^[0-9]{6}$/D', $code) === 1) {
                $counter = $this->matchingCounter($this->unseal($actor, $sealed), $code, $now, (int) $row['last_counter']);
            }

            if ($counter === null) {
                $this->store->db->update('wcp_candidate_mfa', ['failed_window_started' => $started, 'failed_attempts' => $attempts + 1], ['user_id' => $actor]);
                $this->store->audit($actor, null, 'candidate.mfa.challenge.failed', $actor);

                return null;
            }

            $revision = (int) $row['revision'];
            $changes = ['last_counter' => $counter, 'failed_attempts' => 0, 'failed_window_started' => 0];
            $result = ['revision' => $revision, 'method' => 'totp'];
            if (!$enabled) {
                $revision++;
                $changes += ['sealed_secret' => $sealed, 'pending_secret' => null, 'pending_expires_at' => null, 'revision' => $revision];
                $codes = [];
                for ($i = 0; $i < 10; $i++) {
                    $recovery = bin2hex(random_bytes(16));
                    $this->store->db->insert('wcp_candidate_mfa_recovery', ['user_id' => $actor, 'code_hash' => $this->recoveryHash($actor, $recovery)]);
                    $codes[] = $recovery;
                }
                $result = ['revision' => $revision, 'method' => 'enrollment', 'recovery_codes' => $codes];
            }
            $this->store->db->update('wcp_candidate_mfa', $changes, ['user_id' => $actor]);
            $this->store->audit($actor, null, $enabled ? 'candidate.mfa.challenge.passed' : 'candidate.mfa.enrollment.completed', $actor);

            return $result;
        });
    }

    /** Caller must require password reauthentication and fresh MFA, then terminate the browser session. */
    public function reset(string $actor): void
    {
        $this->requireKey();
        $this->store->db->transactional(function () use ($actor): void {
            $row = $this->lockedFactor($actor);
            $this->store->db->delete('wcp_candidate_mfa_recovery', ['user_id' => $actor]);
            $this->store->db->update('wcp_candidate_mfa', [
                'sealed_secret' => null,
                'pending_secret' => null,
                'pending_expires_at' => null,
                'last_counter' => -1,
                'revision' => (int) $row['revision'] + 1,
            ], ['user_id' => $actor]);
            $this->store->audit($actor, null, 'candidate.mfa.reset', $actor);
        });
    }

    /** @return array<string, mixed> */
    private function lockedFactor(string $actor): array
    {
        if ($this->store->db->getDatabasePlatform() instanceof SQLitePlatform) {
            // Acquire SQLite's write reservation before any read, including a missing MFA row.
            // PostgreSQL instead serializes all factor changes on the authoritative admin row.
            $this->store->db->executeStatement("UPDATE wcp_candidate_users SET active = active WHERE id = ? AND context = 'admin' AND active = 1", [$actor]);
        }
        $this->store->requireAdmin($actor, true);
        $row = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id = ?', [$actor]);
        if ($row === false) {
            $this->store->db->insert('wcp_candidate_mfa', ['user_id' => $actor]);
            $row = $this->store->db->fetchAssociative('SELECT * FROM wcp_candidate_mfa WHERE user_id = ?', [$actor]);
        }
        if ($row === false) {
            throw new \RuntimeException('MFA is unavailable.');
        }

        return $row;
    }

    /** @return array{int, int} */
    private function window(int $started, int $attempts, int $now): array
    {
        return $attempts === 0 || $now >= $started + 900 ? [$now, 0] : [$started, $attempts];
    }

    private function timestamp(): int
    {
        $now = $this->clock->now()->getTimestamp();
        if ($now < 0 || $now > PHP_INT_MAX - 900) {
            throw new \RuntimeException('MFA is unavailable.');
        }

        return $now;
    }

    private function matchingCounter(#[\SensitiveParameter] string $secret, #[\SensitiveParameter] string $code, int $now, int $last): ?int
    {
        if ($secret === '' || $code === '') {
            return null;
        }
        $totp = TOTP::createFromSecret($secret, $this->clock);
        $current = intdiv($now, 30);
        // Prefer the greatest matching step, so a rare adjacent-code collision cannot permit replay.
        foreach ([$current + 1, $current, $current - 1] as $counter) {
            if ($counter >= 0 && $counter > $last && $totp->verify($code, $counter * 30)) {
                return $counter;
            }
        }

        return null;
    }

    private function requireKey(): void
    {
        if (preg_match('/^[a-fA-F0-9]{64}$/D', $this->key) !== 1) {
            throw new \RuntimeException('MFA is unavailable.');
        }
    }

    private function derivedKey(string $actor, string $purpose): string
    {
        $this->requireKey();
        $binary = hex2bin($this->key);
        if ($binary === false) {
            throw new \RuntimeException('MFA is unavailable.');
        }

        return sodium_crypto_generichash('wcp360.candidate.mfa/1|'.$purpose.'|'.$actor, $binary, SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    }

    private function seal(string $actor, #[\SensitiveParameter] string $secret): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        return 'v1.'.base64_encode($nonce.sodium_crypto_secretbox($secret, $nonce, $this->derivedKey($actor, 'seed')));
    }

    private function unseal(string $actor, string $sealed): string
    {
        $bytes = str_starts_with($sealed, 'v1.') ? base64_decode(substr($sealed, 3), true) : false;
        if ($bytes === false || strlen($bytes) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES + 1 || strlen($bytes) > 400) {
            throw new \RuntimeException('MFA is unavailable.');
        }
        $secret = sodium_crypto_secretbox_open(substr($bytes, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), substr($bytes, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES), $this->derivedKey($actor, 'seed'));
        if ($secret === false || preg_match('/^[A-Z2-7]{32,128}$/D', $secret) !== 1) {
            throw new \RuntimeException('MFA is unavailable.');
        }

        return $secret;
    }

    private function recoveryHash(string $actor, #[\SensitiveParameter] string $code): string
    {
        return hash_hmac('sha256', $code, $this->derivedKey($actor, 'recovery'));
    }
}
