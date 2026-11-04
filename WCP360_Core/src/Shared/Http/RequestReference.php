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
        // An early error or another listener may have already minted this
        // Request's reference. Never rotate it within the same request.
        $reference = $this->issued[$request] ?? null;
        if (!is_string($reference) || !RequestId::valid($reference)) {
            $reference = RequestId::generate();
            $this->issued[$request] = $reference;
        }

        // Inbound headers and valid-looking attributes have no authority.
        // Restore the server-issued reference after any attribute tampering.
        $request->attributes->set(self::ATTRIBUTE, $reference);

        return $reference;
    }

    public function get(Request $request): string
    {
        return $this->begin($request);
    }
}
