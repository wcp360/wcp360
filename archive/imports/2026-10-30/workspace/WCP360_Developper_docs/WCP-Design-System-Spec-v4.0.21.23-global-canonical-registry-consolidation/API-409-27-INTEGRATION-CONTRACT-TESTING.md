# V4.0.9 --- Integration Contract Testing & Certification

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

## Test layers

Schema tests, auth/scopes, idempotency, retry/timeouts, webhook
signatures, lifecycle semantics, sandbox provider tests and negative
security tests.

## Billing adapters

Create → retry create → suspend → retry → unsuspend → package change →
terminate with state verification.

## Fault injection

Timeout after provider/WCP write, duplicate callback, reordered events,
expired token, rate limit and partial outage.

## Certification

Adapter/version matrix with last tested provider version/date and known
limitations.

## Drift

Provider API/module update can mark adapter `needs_revalidation`.
