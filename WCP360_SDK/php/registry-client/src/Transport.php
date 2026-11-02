<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Registry;

interface Transport
{
    /** Implementations must disable redirects, verify TLS and bound response sizes/time. */
    public function get(string $url, array $headers = []): TransportResponse;
}
