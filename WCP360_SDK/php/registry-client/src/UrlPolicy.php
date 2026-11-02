<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Registry;

final class UrlPolicy
{
    public static function validate(string $url, bool $allowLoopbackHttp = false): void
    {
        $p = parse_url($url);
        if (strlen($url) > 4096 || preg_match('/[\x00-\x20\x7f\\\\]/', $url) || $p === false || empty($p['host']) || isset($p['user']) || isset($p['pass']) || isset($p['fragment'])) {
            throw new ClientException('invalid_endpoint');
        }
        $https = ($p['scheme'] ?? '') === 'https';
        $loopback = $allowLoopbackHttp && ($p['scheme'] ?? '') === 'http' && in_array($p['host'], ['localhost','127.0.0.1','[::1]'], true);
        if (!$https && !$loopback) throw new ClientException('https_required');
    }
}
