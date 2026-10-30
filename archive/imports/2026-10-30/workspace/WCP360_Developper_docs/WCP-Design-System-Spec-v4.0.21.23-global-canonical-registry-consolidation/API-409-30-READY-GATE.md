# V4.0.9 --- V4.0.9 READY Gate

## Statut

**NORMATIVE --- V4.0.9 API, AUTOMATION, BILLING & PROVISIONING DEEP
SPECIFICATION --- NO CODE**

Cette passe approfondit les interfaces d'automatisation externes de WCP,
les identités machine, l'idempotence, les webhooks, les intégrations
billing et le lifecycle de provisioning. Elle ne transforme pas WCP en
système comptable : le billing externe et le control-plane WCP
conservent des responsabilités distinctes.

## Invariants

-   Toute action réalisable via API conserve les mêmes contrôles RBAC,
    entitlement, validation et audit que l'interface.
-   `APIClient`, `ServiceAccount`, `APIToken`, `WebhookEndpoint`,
    `ExternalBillingAccount`, `ProvisioningRequest` et `HostingAccount`
    sont des ressources distinctes.
-   Un identifiant externe n'est jamais utilisé comme unique clé interne
    WCP.
-   Toute mutation externe retryable possède une stratégie d'idempotence
    et de reconciliation.
-   Les secrets/token/webhook signing keys ne sont jamais journalisés.
-   Billing `paid/unpaid` n'est pas directement équivalent à l'état
    technique d'un compte sans policy de provisioning.
-   Un timeout externe n'autorise jamais un double provisioning aveugle.

## SPECIFIED-DEEP

API-first, machine identities/tokens/scopes, rate limits, idempotency,
concurrency, errors/audit, inbound/outbound webhooks,
signatures/retries/DLQ, WHMCS/Blesta/FOSSBilling/HostBill adapters,
provisioning saga, suspend/terminate, package changes, reconciliation,
placement boundary, credential delivery, integration secrets, automation
and contract testing.

## Critères

No duplicate provisioning after retry, no secret in logs, no webhook
SSRF/replay, cross-tenant negatives, downgrade non-destructive,
billing/technical state separation and explicit
compensation/reconciliation.

## Statut

**V4.0.9 = SPECIFIED-DEEP --- NO CODE --- NOT GLOBAL READY.**

Observability/telemetry, notifications/incident operations,
multi-node/cluster/HA, lifecycle/update orchestration and final
corpus-wide dependency/decision audit remain before global closure.
