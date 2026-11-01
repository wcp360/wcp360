<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\HttpFoundation\Request;

/** Stateless across worker requests. Client-provided trace/reference headers are ignored. */
final class RequestReference
{
    public const HEADER = 'X-WCP360-Request-Id';
    private const ATTRIBUTE = '_wcp_candidate_request_reference';

    public function begin(Request $request): string
    {
        $reference = bin2hex(random_bytes(16));
        $request->attributes->set(self::ATTRIBUTE, $reference);

        return $reference;
    }

    public function get(Request $request): string
    {
        $reference = $request->attributes->get(self::ATTRIBUTE);

        return is_string($reference) && preg_match('/\A[a-f0-9]{32}\z/D', $reference) === 1
            ? $reference : $this->begin($request);
    }
}
