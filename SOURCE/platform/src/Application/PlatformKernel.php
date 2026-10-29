<?php

declare(strict_types=1);

namespace Wcp360\Application;

use Throwable;
use Wcp360\Contracts\ApiContractRegistry;
use Wcp360\Contracts\RequestSchema;
use Wcp360\Security\AuthenticationService;
use Wcp360\Security\AuthorizationRequest;
use Wcp360\Security\AuthorizationContext;
use Wcp360\Security\AuthorizationService;
use Wcp360\Shared\CanonicalErrorCode;
use Wcp360\Shared\ContractViolation;

final readonly class PlatformKernel
{
    public function __construct(
        private ApiContractRegistry $contracts,
        private AuthenticationService $authentication,
        private AuthorizationService $authorization,
        private ActionRegistry $actions,
    ) {
    }

    /** @param array<string,mixed> $payload */
    public function execute(string $contractId, array $payload, AuthorizationContext $context): OperationResult
    {
        try {
            $contract = $this->contracts->require($contractId);
        } catch (ContractViolation) {
            return OperationResult::failure(CanonicalErrorCode::ValidationFailed);
        }

        if (!$contract->allowsSurface($context->surface)) {
            return OperationResult::failure(CanonicalErrorCode::AuthorizationDenied);
        }

        $authentication = $this->authentication->decide($contract, $context);
        if (!$authentication->allowed) {
            return OperationResult::failure($authentication->errorCode ?? CanonicalErrorCode::AuthenticationRequired);
        }

        try {
            RequestSchema::validateFieldNames($contract, $payload);
        } catch (ContractViolation) {
            return OperationResult::failure(CanonicalErrorCode::ValidationFailed);
        }

        $decision = $this->authorization->decide(
            $context,
            new AuthorizationRequest(
                $contract->permissionId,
                $contract->actionId,
                $contract->resourceType,
                self::resourceIdFromPayload($payload),
            ),
        );
        if (!$decision->allowed) {
            return OperationResult::failure($decision->errorCode ?? CanonicalErrorCode::AuthorizationDenied);
        }

        $handler = $this->actions->find($contract->actionId);
        if ($handler === null) {
            return OperationResult::failure(CanonicalErrorCode::ServiceUnavailable);
        }

        try {
            return OperationResult::success($handler->handle($payload, $context));
        } catch (ContractViolation) {
            return OperationResult::failure(CanonicalErrorCode::ValidationFailed);
        } catch (Throwable) {
            return OperationResult::failure(CanonicalErrorCode::InternalError);
        }
    }

    /** @param array<string,mixed> $payload */
    private static function resourceIdFromPayload(array $payload): ?string
    {
        foreach (['resource_id', 'application_id', 'domain_id', 'database_id', 'server_id', 'job_id'] as $field) {
            $value = $payload[$field] ?? null;
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }
        return null;
    }
}
