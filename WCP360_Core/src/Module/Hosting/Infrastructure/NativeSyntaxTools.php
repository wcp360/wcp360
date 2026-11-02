<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Infrastructure;

/** Fixed local parser locations; presence is not provider provenance or deployment admission. */
class NativeSyntaxTools
{
    public function binary(string $tool): ?string
    {
        $paths = match ($tool) {
            'nginx' => ['/usr/sbin/nginx'],
            'caddy' => ['/usr/bin/caddy', '/usr/local/bin/caddy'],
            'frankenphp' => ['/usr/local/bin/frankenphp', '/usr/bin/frankenphp'],
            default => throw new \InvalidArgumentException('Unknown syntax tool.'),
        };
        if (PHP_OS_FAMILY !== 'Linux' || !function_exists('posix_geteuid') || posix_geteuid() === 0) return null;
        foreach ($paths as $path) {
            $resolved = realpath($path);
            if ($resolved === false || !is_file($resolved) || !is_executable($resolved)) continue;
            $current = $resolved;
            do {
                $metadata = @stat($current);
                if ($metadata === false || $metadata['uid'] !== 0 || ($metadata['mode'] & 0022) !== 0) continue 2;
                $parent = dirname($current);
                if ($parent === $current) break;
                $current = $parent;
            } while (true);
            return $resolved;
        }
        return null;
    }
}
