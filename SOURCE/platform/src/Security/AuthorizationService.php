<?php

declare(strict_types=1);

namespace Wcp360\Security;

use Wcp360\Contracts\PermissionRegistry;
use Wcp360\Shared\CanonicalErrorCode;
use Wcp360\Shared\ContractViolation;

final readonly class AuthorizationService
{
    public function __construct(
        private PermissionRegistry $permissions,
        private ScopeGuard $scopeGuard,
        private PolicyGuard $policyGuard,
    ) {
    }

    public function decide(AuthorizationContext $context, AuthorizationRequest $request): AuthorizationDecision
    {
        try {
            $permission = $this->permissions->require($request->permissionId);
        } catch (ContractViolation) {
            return AuthorizationDecision::deny(
                CanonicalErrorCode::AuthorizationDenied,
                'unknown permission fails closed',
            );
        }

        if (!$permission->allowsSurface($context->surface)) {
            return AuthorizationDecision::deny(
                CanonicalErrorCode::AuthorizationDenied,
                'permission not valid on caller surface',
            );
        }

        if (!$context->hasPermission($request->permissionId)) {
            return AuthorizationDecision::deny(
                CanonicalErrorCode::AuthorizationDenied,
                'permission not granted to principal',
            );
        }

        if (!$this->scopeGuard->allows($context, $request->resourceType, $request->resourceId)) {
            return AuthorizationDecision::deny(
                CanonicalErrorCode::AuthorizationDenied,
                'tenant/resource scope denied',
            );
        }

        return $this->policyGuard->evaluate($context, $request);
    }
}
