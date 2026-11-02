# Candidate recipe authoring contract

**Status: local authoring draft, non-executable. No installer consumes these documents.**

[app-recipe.schema.json](app-recipe.schema.json) uses JSON Schema 2020-12 to describe bounded recipe metadata. It is not the approved SDK, API, IPC, package-signature, entitlement or provider-admission contract. Parsing or schema validation does not verify a publisher, resolve a secret, qualify an application or authorize effects.

## Fixed draft boundary

The schema closes the top-level and declared data objects. Every recipe must keep status draft, installable false, releases empty, application/runtime/provider versions and artifact pins unresolved, lifecycle implementations unavailable and qualification claims false. It cannot describe an executable release. The recipe version is an actual authoring revision, not a fabricated upstream application version.

recipe_id values are local dev.app-deploy names. They are never canonical resource IDs, permissions, operation IDs or public API identifiers. Template source_entry may be null; an application pilot must bind the exact CSV tuple name/category/nature/proposed_profile. Do not bind by Markdown position or row number.

## Fields and future release requirements

| Field group | Meaning and admission still required |
| :--- | :--- |
| Source and identity | Exact catalogue tuple, recipe revision and upstream documentation checked at a recorded date; no support inferred from catalogue presence. |
| Artifact and releases | Unresolved in this draft. A separately reviewed release format must pin exact application version, artifact size/digest, source/publisher trust and distribution rights. |
| Runtime and dependencies | Family php, native_http or docker; selected provider/version/digest remain null. A future release must pin runtime, extensions, database/cache and build/plugin inputs as applicable. No latest/nightly wildcard may silently resolve at deployment. |
| Hosting | Topology selection unresolved, private ingress required, native .htaccess requirement explicit and Worker disabled/unqualified. Core owns listener/TLS/backend admission. |
| Configuration and secrets | Application-specific closed schemas use opaque resource/secret references; Core must look up ownership, current permission and actual existence. A string matching the reference pattern is not proof. |
| Data | Owned persistent roles, retention on uninstall and separate explicitly authorized purge; shared/unowned resources cannot be deleted. |
| Lifecycle | Eight human phase documents and null provider-operation references. No shell, hooks, arbitrary Compose, command arguments or executable expression language. |
| Qualification | Upstream documentation only; no qualified target/version/architecture tuple, native installer or performance claim. |

Configuration schema string/array/object bounds are authoring constraints, not complete domain validation. JSON Schema format fields may be annotations; future consuming code must validate email/domain/resource semantics itself. Runtime release admission must also bound file sizes/depth, reject duplicate/unknown keys, verify trust/revocation, and handle archive paths/links/collisions through the approved contracts.

The current Core reads its existing candidate inputs; it does not discover or execute these recipes. See [Core application preparation](../../WCP360_Core/src/Module/Hosting/design/applications/README.md) and [provider preparation](../../WCP360_Core/src/Module/Platform/design/containers/README.md).

Start with the [template](../_templates/application/README.md), [WordPress pilot](../App_one_click/sites-blogs-et-cms/wordpress/README.md) or [Discourse pilot](../DEPLOY/forums-modernes/discourse/README.md). Qualification, signatures and a general lifecycle need separate implementation and evidence.
