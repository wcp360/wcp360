<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Platform;

/** Checks prerequisites only; performs no migrations or application writes. */
final class RuntimeReadiness
{
    private const EXTENSIONS = ['ctype', 'iconv', 'pdo', 'sodium', 'intl', 'mbstring', 'openssl', 'dom', 'pdo_pgsql', 'pdo_sqlite', 'SimpleXML'];

    public static function phpVersionSupported(int $version): bool
    {
        return $version >= 80400 && $version < 80500;
    }

    public static function postgresqlMajor(mixed $version): ?int
    {
        if (!is_string($version) || preg_match('/\A[1-9][0-9]{4,5}\z/D', $version) !== 1) {
            return null;
        }

        return intdiv((int) $version, 10000);
    }

    /**
     * Strict URI-to-PDO conversion. Credentials remain local and never enter a report.
     * @internal
     * @return array{dsn: string, user: string, password: string}|null
     */
    public static function connectionParameters(string $url): ?array
    {
        if (strlen($url) > 8192 || preg_match('/[\x00-\x20\x7F]/', $url) === 1) {
            return null;
        }
        try {
            $parts = parse_url($url);
        } catch (\ValueError) {
            return null;
        }
        if (!is_array($parts)
            || !in_array($parts['scheme'] ?? '', ['postgresql', 'postgres'], true)
            || isset($parts['fragment'])
            || !isset($parts['host'], $parts['path'], $parts['user'])
            || preg_match('/\A(?:[a-zA-Z0-9_.-]+|\[[a-fA-F0-9:]+\])\z/D', $parts['host']) !== 1) {
            return null;
        }
        $database = rawurldecode(substr($parts['path'], 1));
        $user = rawurldecode($parts['user']);
        $password = rawurldecode($parts['pass'] ?? '');
        if (!str_starts_with($parts['path'], '/')
            || preg_match('/\A[a-zA-Z0-9_.-]{1,63}\z/D', $database) !== 1
            || preg_match('/\A[a-zA-Z0-9_.@-]{1,63}\z/D', $user) !== 1
            || preg_match('/[\x00-\x1F\x7F]/', $password) === 1) {
            return null;
        }
        $port = $parts['port'] ?? 5432;
        // parse_url already rejects ports above 65535; explicitly reject port zero.
        if ($port < 1) {
            return null;
        }
        $query = [];
        if (isset($parts['query']) && $parts['query'] !== '') {
            foreach (explode('&', $parts['query']) as $entry) {
                $pair = explode('=', $entry, 2);
                if (count($pair) !== 2 || !in_array($pair[0], ['serverVersion', 'charset', 'sslmode'], true) || isset($query[$pair[0]])) {
                    return null;
                }
                $query[$pair[0]] = rawurldecode($pair[1]);
            }
        }
        // Doctrine's hint is accepted as metadata and deliberately never used to qualify a server.
        if (isset($query['serverVersion']) && preg_match('/\A[0-9]+(?:\.[0-9]+){0,2}\z/D', $query['serverVersion']) !== 1) {
            return null;
        }
        if (isset($query['charset']) && !in_array(strtolower($query['charset']), ['utf8', 'utf-8'], true)) {
            return null;
        }
        $sslmode = $query['sslmode'] ?? 'prefer';
        if (!in_array($sslmode, ['disable', 'allow', 'prefer', 'require', 'verify-ca', 'verify-full'], true)) {
            return null;
        }

        return [
            'dsn' => 'pgsql:host='.trim($parts['host'], '[]').';port='.$port.';dbname='.$database.';connect_timeout=3;sslmode='.$sslmode.';options=\'-c statement_timeout=3000 -c default_transaction_read_only=on\'',
            'user' => $user,
            'password' => $password,
        ];
    }

    /** @return array{mode: string, checks: array<string, bool>, observed_postgresql_major: int|null, ready: bool, qualified_release: bool} */
    public static function check(?string $databaseUrl): array
    {
        $checks = ['php_8_4' => self::phpVersionSupported(PHP_VERSION_ID)];
        foreach (self::EXTENSIONS as $extension) {
            $checks['ext_'.strtolower($extension)] = extension_loaded($extension);
        }
        $connection = $databaseUrl === null ? null : self::connectionParameters($databaseUrl);
        $checks['database_url_supported'] = $connection !== null;
        $checks['postgresql_connected'] = false;
        $checks['postgresql_17'] = false;
        $major = null;
        if ($connection !== null && $checks['ext_pdo_pgsql']) {
            try {
                $pdo = new \PDO($connection['dsn'], $connection['user'], $connection['password'], [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_PERSISTENT => false,
                    \PDO::ATTR_TIMEOUT => 3,
                ]);
                $statement = $pdo->query('SHOW server_version_num');
                $version = $statement === false ? false : $statement->fetchColumn();
                $major = self::postgresqlMajor($version);
                $checks['postgresql_connected'] = true;
                $checks['postgresql_17'] = $major === 17;
            } catch (\PDOException) {
                // Neither connection strings, server messages nor credentials belong in diagnostics.
            }
        }

        return [
            'mode' => 'candidate-runtime-readiness',
            'checks' => $checks,
            'observed_postgresql_major' => $major,
            'ready' => !in_array(false, $checks, true),
            'qualified_release' => false,
        ];
    }
}
