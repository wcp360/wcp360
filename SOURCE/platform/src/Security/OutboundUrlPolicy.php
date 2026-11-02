<?php

declare(strict_types=1);

namespace Wcp360\Security;

use Wcp360\Shared\ContractViolation;

final class OutboundUrlPolicy
{
    public const MAX_REDIRECTS = 3;

    /**
     * Validates syntax before DNS. The adapter must then resolve the returned
     * host and call assertResolvedAddresses() immediately before connection.
     * Every redirect repeats the same parse/resolve/address checks.
     *
     * @return array{scheme:string,host:string,port:int|null}
     */
    public static function validateCandidate(string $url): array
    {
        if ($url === '' || preg_match('/[\\x00-\\x1f\\x7f\\\\]/', $url)) {
            throw new ContractViolation('invalid outbound URL');
        }
        $parts = parse_url($url);
        if (!is_array($parts)) {
            throw new ContractViolation('invalid outbound URL');
        }
        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true) || $host === '') {
            throw new ContractViolation('outbound URL scheme/host not allowed');
        }
        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new ContractViolation('outbound URL credentials are forbidden');
        }
        $port = isset($parts['port']) ? (int) $parts['port'] : null;
        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw new ContractViolation('outbound URL port invalid');
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            GlobalAddressPolicy::assertGlobal($host);
        }

        return ['scheme' => $scheme, 'host' => $host, 'port' => $port];
    }

    /** @param list<string> $addresses */
    public static function assertResolvedAddresses(array $addresses): void
    {
        GlobalAddressPolicy::assertAllGlobal($addresses);
    }

    public static function assertRedirectCount(int $redirectCount): void
    {
        if ($redirectCount < 0 || $redirectCount > self::MAX_REDIRECTS) {
            throw new ContractViolation('outbound redirect budget exceeded');
        }
    }
}
