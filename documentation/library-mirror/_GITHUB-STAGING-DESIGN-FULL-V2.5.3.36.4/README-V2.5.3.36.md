# WCP360 V2.5.3.36 — System Boundary & Installation Specification

## Status

**SPECIFICATION-ONLY / NON-EXECUTABLE / V4.0 ACTIVE**

This version defines concrete system placement, service identities, installation lifecycle and recovery boundaries.

It does not introduce:
- code;
- repository skeleton;
- package build;
- systemd units;
- shell scripts;
- OpenAPI;
- executable schemas.

## Objectives

1. freeze the server filesystem layout;
2. freeze Unix users/groups and ownership boundaries;
3. freeze socket/runtime/log/state/cache/secrets placement;
4. define generated service-config locations;
5. define supported OS baseline policy;
6. define prerequisites and conflict detection;
7. define installer phases;
8. define first-boot/finalization workflow;
9. define recovery/rescue mode;
10. define upgrade and uninstall/decommission rules.

## Architectural rule

WCP360 is split into:
- **WCP web/control layer**: unprivileged web/API/UI processes;
- **WCP daemon**: privileged, finite-operation system executor;
- **managed hosting identities**: per-account or per-application Unix identities;
- **system services**: Nginx, PHP-FPM, DB, mail, Redis, firewall, etc.

No writable shared root between these boundaries is allowed without an explicit contract.