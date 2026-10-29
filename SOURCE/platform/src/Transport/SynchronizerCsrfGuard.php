<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Contracts\ApiContractDefinition;

final readonly class SynchronizerCsrfGuard implements CsrfGuard
{
    public const AJAX_HEADER = 'X-WCP-CSRF';

    public function __construct(
        private ExpectedOriginResolver $origins,
        private SessionCsrfTokenVerifier $tokens,
    ) {
    }

    public function allows(ApiRequestEnvelope $request, ApiContractDefinition $contract): bool
    {
        if (!$request->browserRequest || $request->origin === null || $request->csrfToken === null) {
            return false;
        }

        $expected = $this->origins->expectedOrigin($request->authorization->surface);
        if ($expected === '' || $request->origin !== $expected) {
            return false;
        }

        return $this->tokens->verify($request->authorization, $request->csrfToken);
    }
}
