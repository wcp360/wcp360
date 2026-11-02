<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

/** One bounded hostname rule for candidate requests; not public-domain provisioning. */
final class CandidateHostname
{
    public static function valid(string $host): bool
    {
        return strlen($host) <= 253 && $host === strtolower($host) && str_ends_with($host, '.test')
            && !filter_var($host, FILTER_VALIDATE_IP)
            && preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+test$/D', $host) === 1;
    }
}
