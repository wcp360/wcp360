# V4.0.21.6 --- UI/API/CLI Authentication & Authorization Parity

## Rule

WCP-ADMIN UI, WCP-USER UI, REST API and `wcpctl` resolve to the same
Operation ID and policy engine.

`wcpctl` supports interactive user auth and scoped
service-account/API-token auth where allowed. It never bypasses RBAC
because it is local to the server.

## Local administrator

OS root access does not silently become an application identity.
Break-glass access, if enabled, is explicit, short-lived, strongly
audited and cannot be confused with normal WCP authentication.

## Tokens

Tokens are scoped, expiring where policy requires, revocable and
displayed only once at creation. CLI config files must never receive
secrets through command-line arguments that leak into process listings
when a safer stdin/file-descriptor mechanism is available.
