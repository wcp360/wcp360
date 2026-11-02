# HashiCorp Vault secrets connector

This is an optional premium connector proposal, not an implemented or installable package. Its current state is **design**, **not installable**, with the source distribution model **undecided**.

Prepare an optional secret-reference resolver for selected Vault namespaces, mounts and narrowly scoped workloads.

## Proposal record

| Field | Value |
| :--- | :--- |
| Local proposal ID | `plugin.hashicorp-vault-secrets-pro` |
| Kind / tier | `plugin` / `pro` |
| Category | `identity-secrets` |
| Core owners | [IdentityAccess](../../../WCP360_Core/src/Module/IdentityAccess/README.md), [Operations](../../../WCP360_Core/src/Module/Operations/README.md), [ExtensionManagement](../../../WCP360_Core/src/Module/ExtensionManagement/README.md) |
| Existing catalogue keys | `identity.vaults-kms`, `delivery.deployment-secrets` |
| Package dependencies | None selected; Core owners are contract boundaries, not package dependencies. |
| Authority | [CATALOG.json](../CATALOG.json); [this JSON mirror](package.proposal.json) must remain identical to its entry. |

Catalogue keys refer to the [existing 149-entry design catalogue](../../../PLUGIN_CATALOG.md). A local proposal ID is not a production manifest ID, provider qualification, publisher identity or Core admission record.

## Core boundary

Core owns credential use and redaction; a secret reference never becomes an arbitrary path, executable action or root capability. This is a design-only connector proposal. Essential hosting, current actor and tenant authorization, native service drivers and recovery remain in the free Core. No production interface, publisher trust, installation or effectful capability is established by this metadata.

## Intended scope and evidence

1. Pin an admitted Vault deployment, authentication method, namespace, mount and per-workload read policy.
2. Qualify lease expiry, renewal, rotation, revocation and unavailable-provider behavior without persisting plaintext secrets in proposals or logs.
3. Reject cross-tenant secret references and unapproved destinations; audit use metadata without recording secret values.

The complete [acceptance record](docs/ACCEPTANCE.md) defines evidence still required. No checks are marked complete, and no SDK/provider version, target compatibility, release artifact or production entitlement is selected.

## Packaging and licensing decisions

Only this README, the [proposal metadata](package.proposal.json) and [acceptance document](docs/ACCEPTANCE.md) belong to this draft. Executable source, configuration, lifecycle providers and a production manifest must accompany a concrete reviewed implementation later.

The Core license is [AGPL-3.0-only](../../../WCP360_Core/LICENSING.md). This proposal does not choose the connector's license, price, source distribution model, key or vendor URL. A process/API boundary does not automatically permit a proprietary derivative. Review [editions and premium boundaries](../../../documentation/development/EDITIONS-AND-PREMIUM.md) and [plugin lifecycle requirements](../../../documentation/development/PLUGIN-LIFECYCLE.md).

[Catalogue overview](../README.md) · [Readable inventory](../CATALOG.md) · [Shared proposal schema](../../../WCP360_Modules/schemas/package-proposal.schema.json)
