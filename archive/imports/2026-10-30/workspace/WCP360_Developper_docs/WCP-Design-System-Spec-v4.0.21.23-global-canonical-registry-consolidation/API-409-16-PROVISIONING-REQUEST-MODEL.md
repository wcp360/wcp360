# V4.0.9 --- Provisioning Request & Binding Model

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

## Request

External integration, external request/service ref, requested plan,
customer/account metadata, target node constraints, idempotency identity
and state.

## Binding

Immutable relation between provider/service namespace and resulting WCP
account/resource IDs, with lifecycle status.

## States

received, validating, queued, provisioning, verifying, active,
failed_retryable, failed_terminal, compensating, cancelled.

## History

State transitions append evidence; external retry doesn't erase previous
attempt.

## Sensitive

Customer billing data minimized; WCP stores only fields required for
technical provisioning/mapping.
