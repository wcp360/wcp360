# Privileged Daemon / Unix Socket Contract

## Status

Specification-only. No daemon implementation is introduced here.

## Security objective

The Go daemon may run with elevated system privileges, but it MUST expose a finite, explicit operation allowlist over an internal Unix socket.

## Forbidden architecture

The daemon MUST NOT expose:
- arbitrary shell execution;
- arbitrary command strings;
- arbitrary file write anywhere on filesystem;
- arbitrary systemd unit control;
- arbitrary package manager arguments;
- generic root terminal;
- generic `sudo` proxy.

## Operation contract

Each daemon operation SHALL define:

- `operation_id`
- input fields
- strict field validation
- caller capability mapping
- target scope
- filesystem bounds
- expected subprocess/builtin mechanism
- timeout
- idempotency behavior
- rollback/reconciliation behavior
- safe output schema
- audit/job correlation requirements

## Example operation families

### Accounts
- `linux_user.create`
- `linux_user.lock`
- `linux_user.delete`

### Web
- `nginx_vhost.apply`
- `nginx_vhost.remove`
- `nginx.reload`

### Runtime
- `php_pool.apply`
- `php_pool.remove`
- `php_fpm.reload`

### Certificates
- `certificate.deploy`
- `certificate.remove`

### Database
- `database.create`
- `database.drop`
- `database_user.create`
- `database_grant.apply`

### Firewall
- `firewall.policy.validate`
- `firewall.policy.apply`

### Services
Only allowlisted service identifiers may be controlled:
- `service.reload`
- `service.restart`

with `service_id` constrained to a registry managed by WCP360.

## Socket security

The socket contract must specify:
- owner/group;
- filesystem permissions;
- peer credential verification where supported;
- message size limits;
- request timeout;
- protocol version;
- replay/idempotency rules;
- structured response;
- no secret echo.

## Execution rule

Web/UI code must never invoke privileged OS commands directly.

All privileged mutation goes through:
`UI/API → authorization → job → daemon operation → result → audit`.