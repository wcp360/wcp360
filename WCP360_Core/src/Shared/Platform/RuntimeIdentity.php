<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Platform;

/**
 * Read-only current-namespace diagnostic, never runtime admission or privilege authority.
 * Account names follow Security baseline section 4. No setup/root-shell profile exists.
 *
 * @phpstan-type ProcessIdentity array{uids:list<int>,gids:list<int>,groups:list<int>,capabilities:array<string,string>,no_new_privileges:bool}
 * @phpstan-type AccountIdentity array{uid:int,gid:int,no_login:bool}
 * @phpstan-type IdentityReport array{mode:string,scope:string,profile:string,expected:array{account:string,group:string,supplementary:list<string>},accounts:array<string,AccountIdentity|null>,groups:array<string,int|null>,observed:ProcessIdentity|null,checks:array<string,bool|null>,matches_profile:bool,service_context_verified:false,filesystem_access_verified:false,database_isolation_verified:false,minimal_root_qualified:false,runtime_enforced:false,privileged_effects_allowed:false,qualified_release:false,host_changes:false}
 */
final class RuntimeIdentity
{
    private const MAX_STATUS_BYTES = 65536;
    private const MAX_ID = 4294967294;
    private const PROFILES = [
        'user' => ['account' => 'wcp360-user', 'group' => 'wcp360-user', 'supplementary' => ['wcp360-control']],
        'admin' => ['account' => 'wcp360-admin', 'group' => 'wcp360-admin', 'supplementary' => ['wcp360-control']],
        'worker' => ['account' => 'wcp360-worker', 'group' => 'wcp360-worker', 'supplementary' => []],
        'wcpd' => ['account' => 'wcpd', 'group' => 'wcpd', 'supplementary' => ['wcp360-priv']],
        'wcp-privd' => ['account' => 'root', 'group' => 'root', 'supplementary' => []],
    ];

    /** @return list<string> */
    public static function profiles(): array
    {
        return array_keys(self::PROFILES);
    }

    /** @return IdentityReport */
    public static function inspect(string $profile): array
    {
        self::profile($profile);
        $accounts = [];
        $groups = [];
        $linux = PHP_OS_FAMILY === 'Linux';
        foreach (self::PROFILES as $expected) {
            $name = $expected['account'];
            $record = $linux && function_exists('posix_getpwnam') ? posix_getpwnam($name) : false;
            // Never retain passwd, home, gecos or unrelated NSS attributes.
            $accounts[$name] = is_array($record) && $record['name'] === $name
                ? ['uid' => $record['uid'], 'gid' => $record['gid'], 'no_login' => in_array($record['shell'], ['/usr/sbin/nologin', '/sbin/nologin', '/usr/bin/nologin', '/bin/false', '/usr/bin/false'], true)] : null;
            unset($record);
        }
        foreach (['wcp360-user', 'wcp360-admin', 'wcp360-worker', 'wcpd', 'root', 'wcp360-control', 'wcp360-priv'] as $name) {
            $record = $linux && function_exists('posix_getgrnam') ? posix_getgrnam($name) : false;
            $groups[$name] = is_array($record) && $record['name'] === $name ? $record['gid'] : null;
            unset($record);
        }
        $status = $linux ? self::readStatus() : null;

        return self::assess($profile, $linux, $status === null ? null : self::parseStatus($status), $accounts, $groups);
    }

    /**
     * Pure comparison of supplied observations; callers must not treat this as authentication.
     * @internal
     * @param ProcessIdentity|null $process
     * @param array<string,AccountIdentity|null> $accounts
     * @param array<string,int|null> $groups
     * @return IdentityReport
     */
    public static function assess(string $profile, bool $linux, ?array $process, array $accounts, array $groups): array
    {
        $expected = self::profile($profile);
        $resolvedAccounts = [];
        $resolvedGroups = [];
        foreach (self::PROFILES as $entry) {
            $name = $entry['account'];
            $account = $accounts[$name] ?? null;
            $resolvedAccounts[$name] = $account !== null && self::validId($account['uid']) && self::validId($account['gid'])
                ? ['uid' => $account['uid'], 'gid' => $account['gid'], 'no_login' => $account['no_login']] : null;
        }
        foreach (['wcp360-user', 'wcp360-admin', 'wcp360-worker', 'wcpd', 'root', 'wcp360-control', 'wcp360-priv'] as $name) {
            $gid = $groups[$name] ?? null;
            $resolvedGroups[$name] = $gid !== null && self::validId($gid) ? $gid : null;
        }
        $complete = !in_array(null, $resolvedAccounts, true) && !in_array(null, $resolvedGroups, true);
        $uids = [];
        $primaryGroups = true;
        $accountKinds = true;
        foreach ($resolvedAccounts as $name => $account) {
            $uids[] = $account['uid'] ?? null;
            $primaryGroups = $primaryGroups && $account !== null && $account['gid'] === $resolvedGroups[$name];
            $accountKinds = $accountKinds && $account !== null && ($name === 'root' ? $account['uid'] === 0 && $account['gid'] === 0 : $account['uid'] > 0 && $account['gid'] > 0 && $account['no_login']);
        }
        $selected = $resolvedAccounts[$expected['account']];
        $required = array_map(static fn (string $name): ?int => $resolvedGroups[$name], $expected['supplementary']);
        $allowed = [...$required, $selected['gid'] ?? null];
        $privileged = $profile === 'wcp-privd';
        $checks = [
            'linux' => $linux,
            'process_observed' => $process !== null,
            'canonical_accounts_and_groups_resolved' => $complete,
            'canonical_uids_distinct' => $complete && count(array_unique($uids, SORT_REGULAR)) === count(self::PROFILES),
            'canonical_gids_distinct' => $complete && count(array_unique($resolvedGroups, SORT_REGULAR)) === count($resolvedGroups),
            'primary_groups_match' => $primaryGroups,
            'non_root_service_accounts_no_login' => $accountKinds,
            // All four credentials matter: a non-root effective UID alone is insufficient.
            'uid_tuple_matches' => $process !== null && $selected !== null && $process['uids'] === array_fill(0, 4, $selected['uid']),
            'gid_tuple_matches' => $process !== null && $selected !== null && $process['gids'] === array_fill(0, 4, $selected['gid']),
            'supplementary_groups_match' => $complete && $process !== null && array_diff($process['groups'], $allowed) === [] && array_diff($required, $process['groups']) === [],
            'no_new_privileges' => $process !== null && $process['no_new_privileges'],
            // CapBnd is observed but is not an active capability set; not a sandbox proof.
            'non_root_capabilities_clear' => $privileged ? null : ($process !== null && self::capabilitiesClear($process['capabilities'])),
        ];

        return [
            'mode' => 'candidate-runtime-identity-inspection', 'scope' => 'current_process_namespace',
            'profile' => $profile, 'expected' => $expected, 'accounts' => $resolvedAccounts, 'groups' => $resolvedGroups,
            'observed' => $process, 'checks' => $checks, 'matches_profile' => !in_array(false, $checks, true),
            'service_context_verified' => false, 'filesystem_access_verified' => false, 'database_isolation_verified' => false,
            'minimal_root_qualified' => false, 'runtime_enforced' => false, 'privileged_effects_allowed' => false,
            'qualified_release' => false, 'host_changes' => false,
        ];
    }

    /** @return ProcessIdentity|null */
    public static function parseStatus(string $content): ?array
    {
        if ($content === '' || strlen($content) > self::MAX_STATUS_BYTES || !str_ends_with($content, "\n") || str_contains($content, "\0") || str_contains($content, "\r")) return null;
        $values = [];
        foreach (explode("\n", $content) as $line) {
            if (preg_match('/\A(?:Uid|Gid|Groups|CapInh|CapPrm|CapEff|CapBnd|CapAmb|NoNewPrivs)(?:[: \t]|\z)/D', $line) !== 1) continue;
            if (preg_match('/\A(Uid|Gid|Groups|CapInh|CapPrm|CapEff|CapBnd|CapAmb|NoNewPrivs):[ \t]*(.*)\z/D', $line, $match) !== 1) return null;
            if (array_key_exists($match[1], $values)) return null;
            $values[$match[1]] = trim($match[2], " \t");
        }
        foreach (['Uid', 'Gid', 'Groups', 'CapInh', 'CapPrm', 'CapEff', 'CapBnd', 'CapAmb', 'NoNewPrivs'] as $name) {
            if (!array_key_exists($name, $values)) return null;
        }
        $uids = self::ids($values['Uid']);
        $gids = self::ids($values['Gid']);
        $groups = self::ids($values['Groups']);
        if ($uids === null || count($uids) !== 4 || $gids === null || count($gids) !== 4 || $groups === null || !in_array($values['NoNewPrivs'], ['0', '1'], true)) return null;
        $caps = [];
        foreach (['CapInh', 'CapPrm', 'CapEff', 'CapBnd', 'CapAmb'] as $name) {
            if (preg_match('/\A[a-fA-F0-9]{16}\z/D', $values[$name]) !== 1) return null;
            $caps[$name] = strtolower($values[$name]);
        }
        $groups = array_values(array_unique($groups, SORT_NUMERIC));
        sort($groups, SORT_NUMERIC);

        return ['uids' => $uids, 'gids' => $gids, 'groups' => $groups, 'capabilities' => $caps, 'no_new_privileges' => $values['NoNewPrivs'] === '1'];
    }

    /** @return array{account:string,group:string,supplementary:list<string>} */
    private static function profile(string $profile): array
    {
        if (!isset(self::PROFILES[$profile])) throw new \InvalidArgumentException('Unknown runtime identity profile.');

        return self::PROFILES[$profile];
    }

    private static function validId(int $id): bool
    {
        return $id >= 0 && $id <= self::MAX_ID;
    }

    /** @return list<int>|null */
    private static function ids(string $value): ?array
    {
        if ($value === '') return [];
        $parts = preg_split('/[ \t]+/', $value);
        if (!is_array($parts) || count($parts) > 1024) return null;
        $ids = [];
        foreach ($parts as $part) {
            if (preg_match('/\A(?:0|[1-9][0-9]{0,9})\z/D', $part) !== 1 || strlen($part) === 10 && strcmp($part, (string) self::MAX_ID) > 0) return null;
            $ids[] = (int) $part;
        }

        return $ids;
    }

    /** @param array<string,string> $caps */
    private static function capabilitiesClear(array $caps): bool
    {
        foreach (['CapInh', 'CapPrm', 'CapEff', 'CapAmb'] as $name) {
            if (($caps[$name] ?? null) !== '0000000000000000') return false;
        }

        return true;
    }

    private static function readStatus(): ?string
    {
        // Credentials and no_new_privs are thread-specific, including under ZTS.
        $stream = @fopen('/proc/thread-self/status', 'rb');
        if ($stream === false) return null;
        try {
            $content = stream_get_contents($stream, self::MAX_STATUS_BYTES + 1);

            return is_string($content) && strlen($content) <= self::MAX_STATUS_BYTES && feof($stream) ? $content : null;
        } finally {
            fclose($stream);
        }
    }
}
