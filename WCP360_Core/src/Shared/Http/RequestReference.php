<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\HttpFoundation\Request;
use Wcp360\Core\Shared\Correlation\RequestId;

/** Stateless across worker requests. Client-provided trace/reference headers are ignored. */
final class RequestReference
{
    public const HEADER = 'X-WCP360-Request-Id';
    private const ATTRIBUTE = '_wcp_candidate_request_reference';

    public function begin(Request $request): string
    {
        $reference = RequestId::generate();
        $request->attributes->set(self::ATTRIBUTE, $reference);

        return $reference;
    }

    public function get(Request $request): string
    {
        $reference = $request->attributes->get(self::ATTRIBUTE);

        return is_string($reference) && RequestId::valid($reference)
            ? $reference : $this->begin($request);
    }
}
