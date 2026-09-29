# Installation Prerequisites & Conflict Detection

## Preconditions

Installer checks:
- root/elevated installer execution;
- supported OS;
- minimum disk/RAM/CPU policy;
- valid hostname;
- package manager health;
- clock synchronization;
- DNS resolution;
- required ports;
- filesystem writeability;
- no active conflicting installation lock.

## Existing control panels

Detect likely conflicts with:
- cPanel/WHM;
- Plesk;
- DirectAdmin;
- HestiaCP;
- Virtualmin/Webmin managed stacks;
- other panels altering the same service files.

Default policy:
**do not install over an actively managed third-party panel.**

## Existing services

WCP should distinguish:
- clean server;
- existing unmanaged Nginx/PHP/DB stack;
- conflicting managed environment.

Installation policy must state whether brownfield adoption is supported.

Initial safest policy:
- clean/fresh supported server recommended;
- brownfield import/migration is a separate workflow.

## Port conflicts

Check at least:
- HTTP/HTTPS;
- panel/API port if dedicated;
- DB/mail ports where relevant;
- internal daemon socket conflicts.

## Package manager conflicts

Abort or repair-required if:
- dpkg/rpm database locked or inconsistent;
- interrupted package transaction detected.