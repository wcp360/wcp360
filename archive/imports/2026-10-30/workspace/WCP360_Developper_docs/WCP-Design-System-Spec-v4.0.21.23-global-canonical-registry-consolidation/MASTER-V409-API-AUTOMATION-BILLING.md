# MASTER V4.0.9 --- API, Automation, Billing & Provisioning

## Architecture

Billing/External Client → API/Inbound Webhook Gateway →
Auth/Verification → Idempotency/Dedup → ProvisioningRequest → Job/Saga →
WCP Resources → Verification → ProvisioningBinding → External
result/event.

WCP Event → Outbound Webhook Subscription → Signed Delivery →
Retry/Backoff → Delivered or DLQ.

## Principes

-   API et UI utilisent les mêmes règles métier.
-   External IDs sont namespacés et mappés aux IDs WCP.
-   Retry ne crée jamais un second account.
-   Suspend n'efface pas les données.
-   Downgrade n'efface pas silencieusement les données dépassant le
    nouveau quota.
-   Termination est un workflow destructif distinct.
-   Webhooks entrants ne lancent jamais directement une primitive
    privilégiée.
-   Webhooks sortants sont protégés contre SSRF/replay.
-   Billing state et technical state sont reconciliés, pas confondus.

## Prochaine passe

V4.0.10 : **Observability, Operations, Notifications & Incident
Management** --- metrics, logs, traces, health, alert rules, SLO/SLI,
incident lifecycle, notification routing, status/maintenance,
diagnostics/support bundles et capacity forecasting.
