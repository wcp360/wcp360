<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Contracts\ApiContractDefinition;
use Wcp360\Security\AuthorizationDecision;
use Wcp360\Shared\CanonicalErrorCode;

final readonly class RequestPolicyService
{
    public function __construct(
        private CsrfGuard $csrf,
        private RateLimitGuard $rateLimit,
        private IdempotencyGuard $idempotency,
    ) {
    }

    public function decide(ApiRequestEnvelope $request, ApiContractDefinition $contract): AuthorizationDecision
    {
        if (!$this->rateLimit->allows($request, $contract)) {
            return AuthorizationDecision::deny(CanonicalErrorCode::RateLimited, 'rate limit denied request');
        }

        if ($request->browserRequest && $request->mutation && !$this->csrf->allows($request, $contract)) {
            return AuthorizationDecision::deny(CanonicalErrorCode::AuthorizationDenied, 'browser mutation requires CSRF verification');
        }

        $requiresKey = $contract->idempotency === 'key-required'
            || ($contract->idempotency === 'key when mutating' && $request->mutation);
        if ($requiresKey && $request->idempotencyKey === null) {
            return AuthorizationDecision::deny(CanonicalErrorCode::ValidationFailed, 'idempotency key required');
        }

        if ($request->mutation && $request->idempotencyKey !== null && !$this->idempotency->allows($request, $contract)) {
            return AuthorizationDecision::deny(CanonicalErrorCode::IdempotencyConflict, 'semantic request conflicts with existing idempotency key');
        }

        return AuthorizationDecision::allow();
    }
}
