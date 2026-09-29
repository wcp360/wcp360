# System Boundary Audit

## Closed

- concrete filesystem layout: DEFINED;
- Unix system identities: DEFINED;
- daemon socket location and mode: DEFINED;
- state/log/cache/temp separation: DEFINED;
- secret placement: DEFINED;
- generated service-config placement: DEFINED;
- supported OS policy: DEFINED;
- installer prerequisites/conflicts: DEFINED;
- installer state machine: DEFINED;
- finalization wizard: DEFINED;
- rescue model: DEFINED;
- upgrade policy: DEFINED;
- uninstall/decommission policy: DEFINED.

## Still descriptive

Exact distro-specific package names and service paths remain adapter-level details.

They should not be frozen globally because Debian/Ubuntu versions may differ.

## Remaining specification gaps before executable contracts

1. exact naming convention and length limits for Unix usernames/groups;
2. exact socket protocol field table;
3. exact daemon operation input/output fields;
4. exact timeout defaults;
5. exact installer minimum resources;
6. exact supported OS version table;
7. specific mail-stack product choice;
8. supported DB engine/version matrix;
9. backup repository/provider adapters;
10. migration/import from other panels.

## Status

**SYSTEM BOUNDARIES STRONG / ADAPTER MATRICES STILL REQUIRED**