# Authoring a detached premium business module

This template is documentation only. Do not copy empty code classes, a fake Composer manifest or an invented Core installation manifest into a proposal package.

A proposal currently contains README.md, package.proposal.json and docs/ACCEPTANCE.md. Its authoritative object lives in [CATALOG.json](../CATALOG.json) and follows the [closed planning schema](../schemas/package-proposal.schema.json). Keep the exact materialized object synchronized and retain design / installable:false / source_model:undecided until a separately reviewed implementation process changes the contract.

## Future implementation locations

| Location | Preparation documented here |
| :--- | :--- |
| [src](src/README.md) | Detached business workflows, private state and thin integration adapters. |
| [config](config/README.md) | Reviewed process configuration and secret references. |
| [resources](resources/README.md) | Owned UI assets, declarative recipes and versioned package data. |
| [tests](tests/README.md) | Evidence for behavior, refusal, resource budgets and exit/recovery. |

These four directories exist only in this documentation template. Add real source/configuration/resource/test files to a package with its first reviewed implementation. Introduce its own actual manifests, locks and toolchain only then; do not reuse Core's generated dependencies or register package code in Core's container.

## Contract and ownership

- Keep authoritative hosting/account/database/mail/file ownership with the existing thirteen Core modules.
- Own only the optional workflow metadata, integration mappings, review records and bounded results identified by the proposal.
- Use accepted scoped APIs/events/jobs; planning IDs and catalogue keys do not define endpoints, grants or effect operation IDs.
- Keep PRO entitlement distinct from trust/compatibility and current Core permissions; no entitlement can widen tenant scope.
- Qualify disablement, expiry, provider failure, update and removal while workloads and independent Core recovery remain usable.
- Resolve package licensing/source model, third-party dependencies and upstream rights explicitly; Core's AGPL-3.0-only decision does not select package terms.

Follow [Core ownership](../../WCP360_Core/STRUCTURE.md), [editions/premium strategy](../../documentation/development/EDITIONS-AND-PREMIUM.md) and [Core licensing](../../WCP360_Core/LICENSING.md). This template creates no installed runtime or authority.
