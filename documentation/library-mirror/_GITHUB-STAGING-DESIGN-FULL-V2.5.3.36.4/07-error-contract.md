# Canonical Error Contract

## Error classes

- `VALIDATION_ERROR`
- `PERMISSION_DENIED`
- `POLICY_BLOCKED`
- `APPROVAL_REQUIRED`
- `REAUTH_REQUIRED`
- `CONFLICT`
- `DEPENDENCY_UNAVAILABLE`
- `LOCKOUT_RISK`
- `CAPACITY_EXCEEDED`
- `RESOURCE_BUSY`
- `NOT_FOUND`
- `ALREADY_EXISTS`
- `TIMEOUT`
- `UNKNOWN_OUTCOME`
- `UNSUPPORTED`
- `INTERNAL_ERROR`

## Required error fields

- error code;
- error class;
- safe human message;
- correlation ID;
- retryable boolean/classification;
- impacted resource;
- remediation hint;
- required action/privilege if known;
- related job/incident/audit links if available.

## Security rules

Do not expose:
- stack traces;
- SQL statements containing secrets;
- filesystem paths outside permitted user context;
- private keys;
- token values;
- internal daemon arguments that reveal security-sensitive implementation details.

## Unknown outcome rule

A timeout is not automatically a failed operation.

If execution may have occurred, return `UNKNOWN_OUTCOME` and reconcile before retry.