<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Setup\Infrastructure;

/** PostgreSQL verifier encoding over native PBKDF2/HMAC; ASCII only, no SASLprep approximation. */
final class PostgresScram
{
    public static function verifier(#[\SensitiveParameter] string $password, ?string $salt = null): string
    {
        if (!preg_match('/\A[\x21-\x7e]{1,256}\z/D', $password)) {
            throw new \InvalidArgumentException('SCRAM input must be bounded printable ASCII.');
        }
        $salt ??= random_bytes(16);
        if (strlen($salt) !== 16) {
            throw new \InvalidArgumentException('SCRAM salt must contain 16 bytes.');
        }
        $salted = hash_pbkdf2('sha256', $password, $salt, 4096, 32, true);
        $client = hash_hmac('sha256', 'Client Key', $salted, true);
        $stored = hash('sha256', $client, true);
        $server = hash_hmac('sha256', 'Server Key', $salted, true);
        sodium_memzero($salted);
        sodium_memzero($client);

        return 'SCRAM-SHA-256$4096:'.base64_encode($salt).'$'.base64_encode($stored).':'.base64_encode($server);
    }
}
