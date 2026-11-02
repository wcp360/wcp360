# Observability resource register

**Status: documentation reservation. No additional runtime or production capability is implemented here.**

Owner: [Observability](../../src/Module/Observability/README.md), coordinating Platform and Operations for provider health and bounded diagnostics.

Production logs, metrics, traces, alerting and incident delivery remain unfinished; existing audit inspection stays in its module. Future profiles describe signal sources, permitted readers/recipients, redaction, tenant labels, endpoints, queue/cardinality/storage budgets, retention and unavailable-provider behavior. This README selects no collection backend or required vendor.

| Existing resource boundary | Responsibility |
| :--- | :--- |
| [journald](../services/journald/README.md) | System journal identity, retention and bounded access. |
| [logrotate](../services/logrotate/README.md) | File rotation/reopen; distinct guarantees from journal/business audit. |
| [Backup tooling](../services/backup/README.md) | Recovery and restore verification under BackupRestore. |
| [Toolbox](../toolbox/README.md) | Bounded diagnostic descriptions; code stays in modules/diagnostics. |

Real logs, customer payloads, secrets and support bundles never become source examples. Audit integrity/durability remain application contracts, not a collector feature. Bound labels and collection so monitoring cannot exhaust the host; keep baseline alerts and recovery local.

Provider selection requires endpoint/authentication, transport, secret and resource tests. [OpenTelemetry security guidance](https://opentelemetry.io/docs/security/config-best-practices/) is a reference, not a product selection.

See the [resource index](../README.md) and [Core structure](../../STRUCTURE.md). Add concrete resource files only with their reviewed contract and consuming implementation; no fake configuration or empty template tree is needed.
