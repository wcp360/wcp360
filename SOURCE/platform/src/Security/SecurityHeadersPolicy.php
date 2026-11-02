<?php

declare(strict_types=1);

namespace Wcp360\Security;

use Wcp360\Shared\ContractViolation;

final class SecurityHeadersPolicy
{
    /** @param list<string> $explicitWssEndpoints @return array<string,string> */
    public static function headers(string $nonce, array $explicitWssEndpoints = []): array
    {
        if (!preg_match('/^[A-Za-z0-9+\/_=-]{16,128}$/', $nonce)) {
            throw new ContractViolation('invalid CSP nonce');
        }

        $connect = ["'self'"];
        foreach ($explicitWssEndpoints as $endpoint) {
            if (!preg_match('#^wss://[A-Za-z0-9.-]+(?::[0-9]{1,5})?$#', $endpoint)) {
                throw new ContractViolation('invalid explicit WSS endpoint');
            }
            $connect[] = $endpoint;
        }
        $connect = array_values(array_unique($connect));

        $csp = implode('; ', [
            "default-src 'self'",
            "base-uri 'none'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "script-src 'self' 'nonce-{$nonce}'",
            "style-src 'self' 'nonce-{$nonce}'",
            "img-src 'self' data: blob:",
            "font-src 'self'",
            'connect-src ' . implode(' ', $connect),
            "frame-src 'none'",
        ]) . ';';

        return [
            'Content-Security-Policy' => $csp,
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'no-referrer',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Strict-Transport-Security' => 'max-age=31536000',
        ];
    }
}
