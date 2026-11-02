# Nextcloud application WebDAV connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare an authenticated Nextcloud application/WebDAV client profile for explicitly owned remote file references and reviewed transfers.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.nextcloud-webdav-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `application-platforms` |
| Core owners | [Files](../../../WCP360_Core/src/Module/Files/README.md), [Transfers](../../../WCP360_Core/src/Module/Transfers/README.md), [IdentityAccess](../../../WCP360_Core/src/Module/IdentityAccess/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md) |
| Existing catalogue keys | `apps.business-recipes`, `backup.transfers` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Nextcloud is an application and WebDAV service, not a substitute S3 object-storage server. Its optional S3 primary backend does not make it interchangeable with MinIO, Ceph RGW or their administrative APIs. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Qualify a selected Nextcloud/WebDAV profile, account/app-password permissions and owned remote collection; distinguish authenticated account resources from separately authorized public-share links.
2. Review URI/path encoding, traversal, redirects, locks, size limits, quota and conflict handling; no arbitrary destination, Core file authority takeover or remote credential disclosure is allowed.
3. Exercise interrupted transfers, access revocation, concurrent edits and failed cutover with checksums and retained source; deletion/overwrite needs explicit authority and restore evidence.
4. Declare observation-only, import or externally managed ownership before any integration request. Refuse competing writers for Core-owned resources; ownership handoff requires an authorized inventory, admission review and recovery plan.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)

## Upstream evidence and qualification limits

Nextcloud can consume an S3-compatible primary backend. The proposed integration is an application/WebDAV client boundary, not an S3-server replacement or a MinIO/Ceph administrative interface.

Official documentation reviewed for this proposal identifies upstream interfaces and constraints. It does not select an executable artifact, supported release or OS/API matrix, nor establish WCP360 compatibility. Recheck provenance, current maintenance and the selected profile before implementation or admission.

- [Nextcloud WebDAV basics](https://docs.nextcloud.com/server/latest/developer_manual/client_apis/WebDAV/basic.html)
- [Nextcloud primary object-storage backend](https://docs.nextcloud.com/server/latest/admin_manual/configuration_files/primary_storage.html)
