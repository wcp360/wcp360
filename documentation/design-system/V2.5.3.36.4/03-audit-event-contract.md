# Audit Event Contract

## Objective

Every security-relevant or state-changing action must be attributable and correlatable.

## Canonical audit envelope

Every audit event SHALL include:

- event identifier;
- event version;
- timestamp;
- correlation ID;
- actor identity;
- real actor identity when impersonating;
- effective identity;
- authentication context;
- privilege context;
- scope;
- resource type/id;
- action;
- result;
- reason/comment when required;
- approval reference if any;
- job reference if any;
- source channel: UI / API / automation / system;
- client metadata safe subset;
- before/after diff reference when applicable;
- error class when failed.

## Event naming

Pattern:

`<domain>.<resource_or_action>.<past_tense_event>`

Examples:
- `dns.record.updated`
- `server.reboot.requested`
- `server.reboot.completed`
- `security.privileged_session.started`
- `security.impersonation.started`
- `approval.request.approved`
- `backup.restore.started`
- `backup.restore.completed`
- `firewall.policy.applied`

## Sensitive data

Audit logs must never contain:
- plaintext passwords;
- API token secrets;
- private keys;
- session cookies;
- decrypted secret values.

## Retention

Retention is deployment policy, but:
- security events;
- privilege events;
- impersonation;
- break-glass;
- destructive changes

must support longer retention than ordinary informational activity.

## Immutability model

Application users may not edit historical audit events.

Correction or annotation must be appended as a new event, never by mutation of historical evidence.