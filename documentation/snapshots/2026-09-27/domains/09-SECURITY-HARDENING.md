# WCP360 — Security / Hardening

## Privileged execution

Mandatory target:

`backend → wcpd(non-root) → bounded authenticated IPC → wcp-privd(root minimal)`

Forbidden:
- `shell.exec`;
- arbitrary command execution;
- arbitrary sudo;
- arbitrary systemd control;
- caller-selected executable;
- caller-selected root command;
- Web/PHP direct access to privileged broker.

## Filesystem security

- resolve paths from registered resource IDs;
- no caller-supplied arbitrary absolute paths;
- prevent traversal and symlink escape;
- protect against TOCTOU;
- generated config must have bounded destinations;
- tenant identities must not access WCP internal state;
- secrets must not be web-readable by default.

## Tenant security

Tenant isolation must exist across:
- authorization;
- service/repository layer;
- DB constraints where appropriate;
- IPC;
- filesystem/system binding;
- backup/restore.

## Secrets

Logs, traces, metrics, audit payloads and error surfaces must never expose:
- passwords;
- private keys;
- full API tokens;
- sensitive cookies;
- DB credentials;
- MFA secrets;
- recovery codes.

## Remaining security blockers
- exact privileged IPC physical contract;
- final Unix identities/groups/ACLs;
- complete typed permission/action cleanup;
- full tenant cardinality/ownership enforcement;
- audit durability details.
