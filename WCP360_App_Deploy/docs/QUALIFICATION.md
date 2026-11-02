# Recipe qualification and delivery order

**Status: planned acceptance gates. Catalogue presence, a valid descriptor and a parser pass do not establish deployability.**

The initial catalogue has 410 candidate references. Qualification is attached to an exact application/edition/version, recipe version, topology/runtime/provider, target/architecture and supported lifecycle/features. A green Core test or another product's catalogue does not qualify that tuple.

## Ordered work

1. Preserve the original documents and review [catalogue interpretation](../catalogue/README.md). Resolve identity/edition aliases without silently merging entries or changing source categories.
2. Review the [candidate recipe schema](../schemas/app-recipe.schema.json) and [authoring template](../_templates/application/README.md). Descriptor validation only checks the candidate format; production admission remains a separate contract.
3. Pin upstream artifact/build inputs and dependencies. Establish provenance, integrity, licensing/redistribution, supported versions, vulnerability handling and provider capabilities.
4. Implement and admit the necessary Core resource/provider operations. Establish identity, current authorization, isolation, ingress, secrets, limits and durable recovery before exposing installation.
5. Prove installation and typed configuration for one exact pilot tuple, then update from an explicitly supported version and verify backup/clean-target restoration.
6. Exercise uninstall with retained data, separate purge authorization, dependency refusal, concurrency, revocation and failure recovery.
7. Record operational/performance evidence, support limits, exact source/tool/provider revisions and release approval for each claimed tuple. Expand application/version/OS coverage explicitly.

Steps for a commercial integration apply only when its product requires them. A premium license service or optional installer plugin is not a prerequisite for essential Core hosting, local administration or recovery.

## Initial pilots

| Candidate | Purpose | Remaining gate |
| :--- | :--- | :--- |
| [WordPress](../App_one_click/sites-blogs-et-cms/wordpress/README.md) | First PHP application recipe with data, routing, updates and recovery. | Pin versions/dependencies, admit the selected hosting topology and prove the full lifecycle. |
| [Discourse](../DEPLOY/forums-modernes/discourse/README.md) | Application-specific deployment behind a private HTTP backend. | Qualify its pinned upstream container workflow, dependencies, builds/rebuilds and data-aware recovery. |

These folders prepare drafts only. Do not advertise installability or create placeholder folders for the other 408 entries. Additional PHP, project-template, service, extension, GPU/CPU, cluster, lab and VM families need their own explicit capability decisions.

The target Nginx → Apache event → FPM default is not one of the current six PHP previews. Preserve current Classic/Advanced and plan schemas; distinguish existing candidate runtime evidence from the new target's missing implementation.

## Evidence checklist

| Area | Evidence required |
| :--- | :--- |
| Identity and distribution | Exact upstream/edition/version, immutable artifacts/build inputs, source/license obligations and trusted publisher scope. |
| Compatibility | Application/runtime/extension/dependency/provider/OS/architecture matrix; no unsupported fallback or automatic Worker conversion. |
| Authority and isolation | Anonymous/wrong role/tenant, revoked credentials, stale approvals/revisions, cross-site files/data/network and forbidden host access. |
| Lifecycle | Install/configure/start/update/inspect, actual health/routing, retained-data uninstall and separate purge where supported. |
| Data and failure | Consistent backup, clean-target restore, failed migration, no blind replay after unknown effects, crash/reboot and loss of dependencies. |
| Application behavior | Login/admin, sessions, uploads, jobs, URLs/query strings, protected/static content, streaming and app-specific features. |
| Resources and performance | Enforced CPU/RAM/PIDs/storage/queue/network budgets; measured workload, latency/error budget, contention, overload and endurance. |
| Privacy and continuity | No secrets in descriptors/logs/exports/prompts; vendor/plugin failure preserves essential Core and retained-resource management. |

Use passed, failed, not_run, not_configured and not_applicable with an actual reason. A missing validator/provider remains unavailable, not passed. PHP-FPM syntax may require provisioned identities; Caddy adaptation does not prove live module/application acceptance.

Core's declared targets are Debian 12/13 and Ubuntu 22.04/24.04/26.04 LTS. Each provider/application may support a narrower reviewed set. Native host, container userspace and privileged VM results are different evidence.

## Publication boundary

A future release includes a reviewed immutable descriptor, artifact/dependency references, configuration contract, admitted lifecycle operations, private qualification evidence references and public support/recovery limits. Publisher trust, commercial rights and current actor permission remain separate. No production signing protocol, canonical API identifier or blanket support claim is introduced here.

The [catalogue audit](CATALOGUE-AUDIT.md) is static source inspection. No application, service, local test suite, load test or host operation was executed for this preparation.
