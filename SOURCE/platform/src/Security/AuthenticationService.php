<?php

declare(strict_types=1);

namespace Wcp360\Security;

use DateTimeImmutable;
use Wcp360\Contracts\ApiContractDefinition;
use Wcp360\Shared\CanonicalErrorCode;
use Wcp360\Shared\Surface;

final class AuthenticationService
{
    public function decide(ApiContractDefinition $contract, AuthorizationContext $context): AuthorizationDecision
    {
        if (!$context->authenticated) {
            return AuthorizationDecision::deny(CanonicalErrorCode::AuthenticationRequired, 'principal is not authenticated');
        }

        $profile = AuthenticationProfile::from($contract->authentication);

        return match ($profile) {
            AuthenticationProfile::User => $context->surface === Surface::User
                ? AuthorizationDecision::allow()
                : AuthorizationDecision::deny(CanonicalErrorCode::AuthenticationRequired, 'AUTH-U requires WCP-USER identity'),
            AuthenticationProfile::Admin => $context->surface === Surface::Admin
                ? AuthorizationDecision::allow()
                : AuthorizationDecision::deny(CanonicalErrorCode::AuthenticationRequired, 'AUTH-A requires WCP-ADMIN identity'),
            AuthenticationProfile::Both => in_array($context->surface, [Surface::User, Surface::Admin], true)
                ? AuthorizationDecision::allow()
                : AuthorizationDecision::deny(CanonicalErrorCode::AuthenticationRequired, 'AUTH-B preserves USER/ADMIN identity'),
            AuthenticationProfile::Policy => $this->policy($context),
            AuthenticationProfile::Privileged => $this->privileged($context),
            AuthenticationProfile::Emergency => $this->emergency($context),
        };
    }

    private function policy(AuthorizationContext $context): AuthorizationDecision
    {
        if ($context->surface !== Surface::Policy) {
            return AuthorizationDecision::deny(CanonicalErrorCode::AuthenticationRequired, 'AUTH-P requires POLICY identity');
        }
        if (!$context->policyEligible) {
            return AuthorizationDecision::deny(CanonicalErrorCode::PolicyDenied, 'policy eligibility required');
        }
        return AuthorizationDecision::allow();
    }

    private function privileged(AuthorizationContext $context): AuthorizationDecision
    {
        if ($context->surface !== Surface::Admin) {
            return AuthorizationDecision::deny(CanonicalErrorCode::AuthenticationRequired, 'AUTH-PRIV requires WCP-ADMIN identity');
        }
        if (!$context->privilegedContext) {
            return AuthorizationDecision::deny(CanonicalErrorCode::AuthorizationDenied, 'current privileged session required');
        }
        if (!$context->mfaSatisfied) {
            return AuthorizationDecision::deny(CanonicalErrorCode::MfaRequired, 'MFA required for privileged session');
        }
        if (!$context->reauthenticated) {
            return AuthorizationDecision::deny(CanonicalErrorCode::ReauthenticationRequired, 'fresh reauthentication required');
        }
        return AuthorizationDecision::allow();
    }

    private function emergency(AuthorizationContext $context): AuthorizationDecision
    {
        if ($context->surface !== Surface::Emergency) {
            return AuthorizationDecision::deny(CanonicalErrorCode::AuthenticationRequired, 'AUTH-E requires emergency principal');
        }
        if (!$context->mfaSatisfied) {
            return AuthorizationDecision::deny(CanonicalErrorCode::MfaRequired, 'emergency principal requires MFA');
        }
        if (!$context->breakGlassEntitled) {
            return AuthorizationDecision::deny(CanonicalErrorCode::EntitlementRequired, 'break-glass entitlement required');
        }
        if (!$context->breakGlassReasonReferencePresent) {
            return AuthorizationDecision::deny(CanonicalErrorCode::PolicyDenied, 'break-glass reason/reference required');
        }
        if ($context->breakGlassExpiresAt === null || $context->breakGlassExpiresAt <= new DateTimeImmutable('now')) {
            return AuthorizationDecision::deny(CanonicalErrorCode::PolicyDenied, 'break-glass context expired or missing hard expiry');
        }
        return AuthorizationDecision::allow();
    }
}
