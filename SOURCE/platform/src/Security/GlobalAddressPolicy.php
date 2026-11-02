<?php

declare(strict_types=1);

namespace Wcp360\Security;

use Wcp360\Shared\ContractViolation;

final class GlobalAddressPolicy
{
    /**
     * Conservative non-global/special-purpose ranges relevant to SSRF.
     * Public DNS results must avoid every range below before connection.
     *
     * @var list<string>
     */
    private const BLOCKED = [
        // IPv4: unspecified/current network, private, CGNAT, loopback, link-local.
        '0.0.0.0/8', '10.0.0.0/8', '100.64.0.0/10', '127.0.0.0/8', '169.254.0.0/16',
        '172.16.0.0/12', '192.168.0.0/16',
        // IPv4 protocol/documentation/benchmark/non-routable ranges.
        '192.0.0.0/24', '192.0.2.0/24', '192.88.99.0/24', '198.18.0.0/15',
        '198.51.100.0/24', '203.0.113.0/24',
        // IPv4 multicast and reserved.
        '224.0.0.0/4', '240.0.0.0/4',
        // IPv6 unspecified, loopback, IPv4-mapped/tunnelling, discard/docs/ORCHID,
        // unique-local, link-local and multicast.
        '::/128', '::1/128', '::ffff:0:0/96', '100::/64', '2001::/32',
        '2001:db8::/32', '2001:10::/28', '2001:20::/28', '2002::/16',
        'fc00::/7', 'fe80::/10', 'ff00::/8',
    ];

    /** @param list<string> $addresses */
    public static function assertAllGlobal(array $addresses): void
    {
        if ($addresses === []) {
            throw new ContractViolation('SSRF target has no resolved addresses');
        }
        foreach ($addresses as $address) {
            self::assertGlobal($address);
        }
    }

    public static function assertGlobal(string $address): void
    {
        if (filter_var($address, FILTER_VALIDATE_IP) === false) {
            throw new ContractViolation('SSRF resolved address is not a valid IP');
        }
        foreach (self::BLOCKED as $cidr) {
            if (self::contains($cidr, $address)) {
                throw new ContractViolation('SSRF resolved address is not global');
            }
        }
    }

    private static function contains(string $cidr, string $address): bool
    {
        [$network, $prefixText] = explode('/', $cidr, 2);
        $networkBytes = inet_pton($network);
        $addressBytes = inet_pton($address);
        if ($networkBytes === false || $addressBytes === false || strlen($networkBytes) !== strlen($addressBytes)) {
            return false;
        }

        $prefix = (int) $prefixText;
        $bits = strlen($networkBytes) * 8;
        if ($prefix < 0 || $prefix > $bits) {
            throw new ContractViolation('invalid internal CIDR policy');
        }

        $fullBytes = intdiv($prefix, 8);
        $remaining = $prefix % 8;
        if ($fullBytes > 0 && substr($networkBytes, 0, $fullBytes) !== substr($addressBytes, 0, $fullBytes)) {
            return false;
        }
        if ($remaining === 0) {
            return true;
        }
        $mask = (0xff << (8 - $remaining)) & 0xff;
        return (ord($networkBytes[$fullBytes]) & $mask) === (ord($addressBytes[$fullBytes]) & $mask);
    }
}
