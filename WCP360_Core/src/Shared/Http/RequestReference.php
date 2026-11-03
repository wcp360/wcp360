<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\HttpFoundation\Request;
use Wcp360\Core\Shared\Correlation\RequestId;

/** A reference belongs to one Request; weak keys never retain finished worker requests. */
final class RequestReference
{
    public const HEADER = 'X-WCP360-Request-Id';
    private const ATTRIBUTE = '_wcp_candidate_request_reference';

    /** @var \WeakMap<Request, string> */
    private \WeakMap $issued;

    public function __construct()
    {
        $this->issued = new \WeakMap();
    }

    public function begin(Request $request): string
    {
        $reference = RequestId::generate();
        $this->issued[$request] = $reference;
        $request->attributes->set(self::ATTRIBUTE, $reference);

        return $reference;
    }

    public function get(Request $request): string
    {
        // A valid-looking attribute or inbound header is not evidence that this
        // service generated the reference for this Request.
        $reference = $this->issued[$request] ?? null;
        if (!is_string($reference) || !RequestId::valid($reference)) {
            return $this->begin($request);
        }

        // Restore the issued value if downstream request attributes were changed.
        $request->attributes->set(self::ATTRIBUTE, $reference);

        return $reference;
    }
}
