<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Domain;

use Symfony\Component\Uid\Uuid;

/** Experimental state-bound bearer code; never a persisted password or an MFA credential. */
final class PasswordRecoveryCode
{
    public const LIFETIME = 900;

    public static function configured(#[\SensitiveParameter] string $key): bool
    {
        return preg_match('/\A[a-fA-F0-9]{64}\z/D', $key) === 1;
    }

    public static function issue(#[\SensitiveParameter] Principal $actor, int $revision,
        #[\SensitiveParameter] string $key, int $now): string
    {
        if (!self::configured($key) || !$actor->active || !Uuid::isValid($actor->id)
            || !in_array($actor->context, ['user', 'admin'], true) || $revision < 0 || $now < 1000000000 || $now > 9999999999) {
            throw new \InvalidArgumentException('Recovery code unavailable.');
        }
        $prefix = implode('.', ['wcp-cr1', $actor->context, $actor->id, (string) $now, bin2hex(random_bytes(32))]);
        return $prefix.'.'.self::signature($prefix, $actor, $revision, $key);
    }

    public static function subject(#[\SensitiveParameter] string $code, string $surface, int $now): ?string
    {
        $parts = self::parts($code);
        if ($parts === null || $parts[1] !== $surface || !in_array($surface, ['user', 'admin'], true)
            || (int) $parts[3] > $now || $now >= (int) $parts[3] + self::LIFETIME) return null;
        return $parts[2];
    }

    public static function valid(#[\SensitiveParameter] string $code, #[\SensitiveParameter] Principal $actor,
        int $revision, #[\SensitiveParameter] string $key, int $now): bool
    {
        if (!self::configured($key) || !$actor->active || $revision < 0
            || self::subject($code, $actor->context, $now) !== $actor->id) return false;
        $parts = self::parts($code);
        if ($parts === null) return false;
        $prefix = implode('.', array_slice($parts, 0, 5));
        return hash_equals(self::signature($prefix, $actor, $revision, $key), $parts[5]);
    }

    /** @return list<string>|null */
    private static function parts(#[\SensitiveParameter] string $code): ?array
    {
        if (strlen($code) > 220 || preg_match('/\Awcp-cr1\.(user|admin)\.([a-f0-9-]{36})\.([1-9][0-9]{9})\.([a-f0-9]{64})\.([a-f0-9]{64})\z/D', $code) !== 1) return null;
        $parts = explode('.', $code);
        return Uuid::isValid($parts[2]) ? $parts : null;
    }

    private static function signature(#[\SensitiveParameter] string $prefix, #[\SensitiveParameter] Principal $actor,
        int $revision, #[\SensitiveParameter] string $key): string
    {
        $binary = hex2bin($key);
        if ($binary === false) throw new \InvalidArgumentException('Recovery code unavailable.');
        $message = json_encode(['wcp360.candidate.password-recovery/1', $prefix, $actor->username,
            $actor->email, $actor->getPassword(), $revision], JSON_THROW_ON_ERROR);
        return hash_hmac('sha256', $message, $binary);
    }
}
