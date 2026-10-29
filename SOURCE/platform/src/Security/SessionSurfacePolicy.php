<?php

declare(strict_types=1);

namespace Wcp360\Security;

use JsonException;
use Wcp360\Shared\ContractViolation;
use Wcp360\Shared\Surface;

final readonly class SessionSurfacePolicy
{
    /** @param list<string> $requiredCookieAttributes @param array<string,array{user:string,admin:string}> $policies */
    private function __construct(
        public string $version,
        public string $invariant,
        private string $userCookie,
        private string $adminCookie,
        public array $requiredCookieAttributes,
        private array $policies,
    ) {
        if ($version !== 'v1.0' || $invariant !== 'WCP-USER session != WCP-ADMIN session') {
            throw new ContractViolation('session surface policy version/invariant mismatch');
        }
        if ($userCookie !== '__Host-wcp_user_session' || $adminCookie !== '__Host-wcp_admin_session') {
            throw new ContractViolation('canonical session cookie names mismatch');
        }
        if ($userCookie === $adminCookie) {
            throw new ContractViolation('USER and ADMIN cookies must be distinct');
        }
        $required = ['Secure', 'HttpOnly', 'SameSite=Strict', 'Path=/', 'host-only', 'no Domain attribute'];
        $actual = $requiredCookieAttributes;
        sort($required);
        sort($actual);
        if ($actual !== $required) {
            throw new ContractViolation('canonical cookie attributes mismatch');
        }
    }

    public static function fromJsonFile(string $path): self
    {
        if (!is_file($path)) {
            throw new ContractViolation('session policy file missing');
        }
        try {
            /** @var array<string,mixed> $data */
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ContractViolation('session policy JSON invalid', 0, $e);
        }
        $policies = [];
        foreach (($data['policies'] ?? []) as $row) {
            if (!is_array($row) || !isset($row['policy'], $row['wcp_user'], $row['wcp_admin'])) {
                throw new ContractViolation('invalid session policy row');
            }
            $policies[(string) $row['policy']] = [
                'user' => (string) $row['wcp_user'],
                'admin' => (string) $row['wcp_admin'],
            ];
        }
        if (count($policies) !== 8) {
            throw new ContractViolation('session policy row count mismatch');
        }
        return new self(
            (string) ($data['version'] ?? ''),
            (string) ($data['invariant'] ?? ''),
            (string) ($data['cookie_user'] ?? ''),
            (string) ($data['cookie_admin'] ?? ''),
            array_values(array_map('strval', $data['required_cookie_attributes'] ?? [])),
            $policies,
        );
    }

    public function cookieName(Surface $surface): string
    {
        return match ($surface) {
            Surface::User => $this->userCookie,
            Surface::Admin => $this->adminCookie,
            Surface::Policy => throw new ContractViolation('POLICY surface has no browser session cookie'),
        };
    }

    public function assertCookieName(Surface $surface, string $cookieName): void
    {
        if (!hash_equals($this->cookieName($surface), $cookieName)) {
            throw new ContractViolation('wrong session cookie for surface');
        }
    }

    public function policy(string $name, Surface $surface): string
    {
        $row = $this->policies[$name] ?? throw new ContractViolation('unknown session policy');
        return match ($surface) {
            Surface::User => $row['user'],
            Surface::Admin => $row['admin'],
            Surface::Policy => throw new ContractViolation('POLICY surface has no browser session policy'),
        };
    }
}
