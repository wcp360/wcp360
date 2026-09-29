# Installer Phase Model

## Installer state machine

`PRECHECK`
→ `PLAN`
→ `CONFIRM`
→ `BASE_INSTALL`
→ `SERVICE_PREP`
→ `WCP_INSTALL`
→ `SECURITY_INIT`
→ `FIRST_BOOT`
→ `FINALIZATION_REQUIRED`
→ `READY`

Failure state:
`INSTALL_FAILED`

Recovery:
`RESUME_AVAILABLE` where safe.

## Phase 1 — Precheck

Collect:
- OS;
- resources;
- hostname;
- network;
- existing services;
- conflicts.

No destructive changes.

## Phase 2 — Plan

Show exactly:
- packages to install;
- services affected;
- ports used;
- filesystem paths created;
- users/groups created.

## Phase 3 — Base install

Install only approved dependencies.

## Phase 4 — Service preparation

Create dedicated include dirs and integration points.

Do not erase unrelated configuration.

## Phase 5 — WCP install

Place:
- binaries/assets;
- configuration;
- state directories;
- log directories;
- runtime definitions.

## Phase 6 — Security init

Generate:
- internal secrets;
- installer bootstrap credentials/tokens;
- initial trust material.

## Phase 7 — First boot

Start WCP components and run self-check.

## Phase 8 — Finalization required

Web wizard completes:
- admin account;
- MFA;
- hostname/network confirmation;
- service choices;
- mail settings;
- backup repository;
- notification provider;
- security policy.

## Phase 9 — Ready

Only after finalization passes.