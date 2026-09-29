# API Contract Design

## Status

Design-only. This is not an OpenAPI document.

## Principles

- versioned;
- resource-oriented;
- explicit scopes;
- idempotency support;
- consistent error envelope;
- pagination;
- filtering;
- stable correlation IDs;
- RBAC identical to UI;
- no hidden superuser API path.

## Resource naming

Prefer nouns:
- `/applications`
- `/domains`
- `/dns-zones`
- `/databases`
- `/mailboxes`
- `/backups`
- `/servers`
- `/clusters`
- `/jobs`
- `/incidents`
- `/audit-events`

State-changing operational actions may use explicit subresources/actions when CRUD semantics are insufficient:
- `/servers/{id}/reboot`
- `/clusters/{id}/failover`
- `/backups/{id}/restore`
- `/services/{id}/reload`

## Authentication

Tokens must define:
- owner/service identity;
- scopes;
- optional resource restrictions;
- creation time;
- expiry;
- last use;
- revocation state.

Secret value is `SHOW_ONCE`.

Existing token secrets are `NEVER_REVEAL`.

## Error envelope

Every error response design must include:
- stable error code;
- safe message;
- correlation ID;
- retryability classification;
- optional field-level errors;
- optional remediation hint;
- no sensitive internal stack trace.

## Pagination

Cursor-based pagination preferred for large mutable datasets such as:
- audit;
- jobs;
- logs;
- events;
- notifications.

## Rate limits

Rate limits must distinguish:
- interactive user;
- automation;
- high-volume read integration;
- privileged control operations.

Rate limiting must not silently weaken security logging.