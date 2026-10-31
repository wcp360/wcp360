# V4.0.9 --- Billing ↔ WCP State Reconciliation

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

## Problem

External service status and WCP technical state drift due to
outages/manual changes/retries.

## Reconcile

Enumerate bindings → fetch/receive external desired status → compare WCP
desired/observed → classify drift → safe remediation or operator
finding.

## Authority

Per-field/source authority documented. Billing may own commercial
desired state; WCP owns technical observed state.

## Examples

External active/WCP suspended, external cancelled/WCP active, orphan WCP
account, external service without binding.

## Safety

No destructive auto-terminate solely from ambiguous missing provider
record.
