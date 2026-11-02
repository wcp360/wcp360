<?php

declare(strict_types=1);

namespace Wcp360\Shared;

enum CanonicalErrorCode: string
{
    case ValidationFailed = 'VALIDATION_FAILED';
    case AuthenticationRequired = 'AUTHENTICATION_REQUIRED';
    case AuthorizationDenied = 'AUTHORIZATION_DENIED';
    case EntitlementRequired = 'ENTITLEMENT_REQUIRED';
    case PolicyDenied = 'POLICY_DENIED';
    case MfaRequired = 'MFA_REQUIRED';
    case ReauthenticationRequired = 'REAUTHENTICATION_REQUIRED';
    case ApprovalRequired = 'APPROVAL_REQUIRED';
    case ResourceNotFound = 'RESOURCE_NOT_FOUND';
    case ResourceConflict = 'RESOURCE_CONFLICT';
    case ConcurrencyConflict = 'CONCURRENCY_CONFLICT';
    case IdempotencyConflict = 'IDEMPOTENCY_CONFLICT';
    case CancellationNotAllowed = 'CANCELLATION_NOT_ALLOWED';
    case ContractVersionUnsupported = 'CONTRACT_VERSION_UNSUPPORTED';
    case RateLimited = 'RATE_LIMITED';
    case OperationTimeout = 'OPERATION_TIMEOUT';
    case DependencyFailure = 'DEPENDENCY_FAILURE';
    case ServiceUnavailable = 'SERVICE_UNAVAILABLE';
    case UnknownOutcome = 'UNKNOWN_OUTCOME';
    case InternalError = 'INTERNAL_ERROR';
    case IpcOperationNotAllowed = 'IPC_OPERATION_NOT_ALLOWED';
    case IpcCallerUntrusted = 'IPC_CALLER_UNTRUSTED';
    case IpcSchemaInvalid = 'IPC_SCHEMA_INVALID';
}
