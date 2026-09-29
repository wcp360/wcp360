# Next Phase Gate

## Current decision

V2.5.3.34 remains a documentation/specification phase.

The cancelled V4.1 “Executable Contracts” phase is **not** reactivated by this work.

## Required review before executable artifacts

The following must be approved as complete:
1. permission/action vocabulary;
2. daemon operation allowlist;
3. resource scope hierarchy;
4. job state/idempotency/concurrency model;
5. audit envelope;
6. error envelope;
7. service configuration schemas;
8. filesystem/storage boundaries;
9. WCP-USER/WCP-ADMIN privilege separation;
10. traceability matrix.

## Only after approval

A later phase may produce:
- OpenAPI;
- JSON Schema;
- protobuf/typed contracts;
- repository skeleton;
- code interfaces;
- SQL migrations;
- daemon protocol messages.

Until then, all output remains descriptive and non-executable.