# journald and system-log resources

**Status: documentation reservation. No production configuration or new runtime capability is implemented here.**

Owner: [Observability](../../../src/Module/Observability/README.md), with Platform for service/identity boundaries.

Future profiles describe journal storage, disk/time budgets, rate limits, read identities, export references and unavailable-storage behavior. Resolve package/version-specific drop-ins before defining managed paths.

journald, [file-log rotation](../logrotate/README.md), application telemetry and durable business audit have different guarantees. Journal retention is not mandatory-audit retention or transactional delivery. Observe dropped-log behavior, authorized reads and resource exhaustion explicitly.

Separate panel/system and tenant visibility. Redact or avoid sensitive fields at ingestion; never commit real journals, support bundles, tokens, sessions or private requests. Exports need authorized scope, recipients and retention. Local baseline operations must remain usable without a vendor service.

Qualification covers restart/reboot, retention and permitted/refused readers. Syntax alone does not prove useful confidential logging. See the [observability register](../../observability/README.md) and [service lifecycle](../README.md#configuration-change-lifecycle).

See the [resource index](../../README.md), [Core structure](../../../STRUCTURE.md) and existing owning use cases before adding real source. This README does not select a package, create a machine-path allowlist or authorize privileged operations.
