<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Infrastructure;

use Symfony\Component\Process\Process;

/** Native adaptation only: no provisioning, listener activation or runtime admission. */
final readonly class CaddySyntaxProbe
{
    public function __construct(private SyntaxProcessRunner $runner, private NativeSyntaxTools $tools) {}

    public function check(string $content, bool $frankenphp): string
    {
        if ($content === '' || strlen($content) > 65536
            || str_contains($content, '{$')
            || preg_match('/(?:^|[\s{}])import(?:[\s{}]|$)/', $content) === 1) {
            return 'failed';
        }

        try {
            $binary = $this->tools->binary($frankenphp ? 'frankenphp' : 'caddy');
            if ($binary === null) return 'not_configured';

            $process = new Process([$binary, 'adapt', '--config', '-', '--adapter', 'caddyfile'],
                '/', input: $content, timeout: 5.0);
            $result = $this->runner->run($process);
            if ($result->status !== 'passed' || strlen($result->stdout) > 262144) return 'failed';

            $document = json_decode($result->stdout, false, 64, JSON_THROW_ON_ERROR);
            if (!$document instanceof \stdClass) return 'failed';
            $apps = $document->apps ?? null;
            if (!$apps instanceof \stdClass) return 'failed';
            $http = $apps->http ?? null;
            if (!$http instanceof \stdClass) return 'failed';
            $servers = $http->servers ?? null;
            if (!$servers instanceof \stdClass || get_object_vars($servers) === []) return 'failed';
            foreach (get_object_vars($servers) as $server) {
                if (!$server instanceof \stdClass) return 'failed';
            }
            return 'passed';
        } catch (\Throwable) {
            return 'failed';
        }
    }
}
