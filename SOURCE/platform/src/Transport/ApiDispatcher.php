<?php

declare(strict_types=1);

namespace Wcp360\Transport;

use Wcp360\Application\PlatformKernel;
use Wcp360\Contracts\ApiContractRegistry;
use Wcp360\Shared\CanonicalErrorCode;
use Wcp360\Shared\ContractViolation;

final readonly class ApiDispatcher
{
    public function __construct(
        private ApiContractRegistry $contracts,
        private RequestPolicyService $requestPolicy,
        private PlatformKernel $kernel,
    ) {
    }

    public function dispatch(ApiRequestEnvelope $request): ApiResponseEnvelope
    {
        try {
            $contract = $this->contracts->require($request->contractId);
        } catch (ContractViolation) {
            return ApiResponseEnvelope::failure($request, CanonicalErrorCode::ValidationFailed);
        }

        $policy = $this->requestPolicy->decide($request, $contract);
        if (!$policy->allowed) {
            return ApiResponseEnvelope::failure(
                $request,
                $policy->errorCode ?? CanonicalErrorCode::AuthorizationDenied,
            );
        }

        return ApiResponseEnvelope::fromOperation(
            $request,
            $this->kernel->execute($request->contractId, $request->payload, $request->authorization),
        );
    }
}
