<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Security;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

/** Candidate-only context selection. Never reads another runtime's credential. */
final class CandidateDatabaseUrl implements EnvVarProcessorInterface
{
    public const REJECTED = 'Candidate database profile rejected. Check the context and private database configuration.';

    public function getEnv(string $prefix, string $name, \Closure $getEnv): string
    {
        try {
            $mode = $getEnv('WCP_DATABASE_MODE');
            if ($mode === 'shared') {
                $url = $getEnv('resolve:'.$name);
                if (is_string($url) && $url !== '') {
                    return $url;
                }
            } elseif ($mode === 'session-isolated') {
                $context = $getEnv('WCP_CONTEXT');
                if (is_string($context) && in_array($context, ['user', 'admin', 'worker', 'setup'], true)) {
                    $url = $getEnv('WCP_DATABASE_'.strtoupper($context).'_URL');
                    if (is_string($url) && self::validPostgresUrl($url)) {
                        return $url;
                    }
                }
            }
        } catch (\Throwable) {
            // Configuration errors must not retain a URL/password in the exception chain.
        }

        throw new \RuntimeException(self::REJECTED);
    }

    public static function getProvidedTypes(): array
    {
        return ['wcp_database' => 'string'];
    }

    private static function validPostgresUrl(#[\SensitiveParameter] string $url): bool
    {
        if (strlen($url) > 8192 || preg_match('/[\x00-\x20\x7f]/', $url)
            || preg_match('/%(?![a-fA-F0-9]{2})/', $url)) {
            return false;
        }
        $parts = parse_url($url);
        if (!is_array($parts) || !in_array($parts['scheme'] ?? '', ['postgresql', 'postgres'], true)
            || ($parts['host'] ?? '') === '' || ($parts['user'] ?? '') === ''
            || ($parts['pass'] ?? '') === '' || ($parts['path'] ?? '/') === '/'
            || isset($parts['fragment'])) {
            return false;
        }
        $seen = [];
        foreach (isset($parts['query']) ? explode('&', $parts['query']) : [] as $entry) {
            $pair = explode('=', $entry, 2);
            $key = rawurldecode($pair[0]);
            $value = rawurldecode($pair[1] ?? '');
            $allowed = match ($key) {
                'serverVersion' => preg_match('/\A17(?:\.[0-9]+){0,2}\z/D', $value) === 1,
                'charset' => $value === 'utf8',
                'sslmode' => in_array($value, ['disable', 'require', 'verify-ca', 'verify-full'], true),
                default => false,
            };
            if (!$allowed || isset($seen[$key])) {
                return false;
            }
            $seen[$key] = true;
        }

        return true;
    }
}
