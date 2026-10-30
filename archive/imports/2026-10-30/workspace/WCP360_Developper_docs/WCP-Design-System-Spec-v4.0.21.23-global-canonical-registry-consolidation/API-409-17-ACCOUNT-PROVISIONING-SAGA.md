# V4.0.9 --- Transactional Account Provisioning Saga

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

## Phases

Validate integration/product → resolve plan → capacity/node placement →
reserve identity/resources → create account → filesystem/quotas →
service defaults → DNS/mail/etc optional → verify → bind external
service → active.

## Saga

Each phase declares compensation where safe. Not every side effect is
transactionally reversible.

## Failure

Stop at failure, preserve evidence, compensate safe reservations, mark
degraded/failed rather than pretend rollback complete.

## Duplicate

Unique external binding + idempotency prevents second account.

## Manual recovery

Operator can resume/reconcile from known phase.
