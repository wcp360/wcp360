# Deep Contract Audit Findings

## Findings closed

### F-001 Generic privileged command risk
**CLOSED BY DESIGN**

The daemon allowlist explicitly prohibits arbitrary shell/command execution.

### F-002 Arbitrary filesystem target risk
**CLOSED BY DESIGN**

Privileged filesystem operations are resource-ID based and confined to managed roots.

### F-003 Arbitrary systemd unit control
**CLOSED BY DESIGN**

A privileged service registry maps stable service IDs to supported units/adapters.

### F-004 Blind retry after timeout
**CLOSED BY DESIGN**

`UNKNOWN_OUTCOME` and reconciliation are mandatory.

### F-005 Concurrent destructive operations
**CLOSED BY DESIGN**

Resource and lifecycle locks plus an explicit exclusion matrix are defined.

### F-006 Privilege derived from UI visibility
**CLOSED BY DESIGN**

Backend permission checks are mandatory independently of UI visibility.

### F-007 Secret redisplay
**CLOSED BY DESIGN**

`SHOW_ONCE` / `NEVER_REVEAL` classifications are explicit.

## Remaining specification work before executable contracts

1. exact server filesystem paths and ownership/mode table;
2. complete naming convention for Unix users/groups/sockets/services;
3. complete daemon operation input/output field table;
4. per-operation timeout values;
5. supported OS/version capability matrix;
6. exact package-manager abstraction boundaries;
7. database engine adapter differences;
8. DNS provider adapter contract;
9. backup provider/repository adapter contract;
10. install/finalization wizard system-state contract.

## Audit status

**DEEP CONTRACT BASELINE STRONG / SYSTEM-SPEC DETAILS STILL REQUIRED**

Therefore, executable contracts remain blocked.