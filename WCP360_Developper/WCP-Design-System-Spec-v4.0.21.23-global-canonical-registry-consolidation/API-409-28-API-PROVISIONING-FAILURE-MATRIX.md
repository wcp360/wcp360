# V4.0.9 --- API, Billing & Provisioning Failure Matrix

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

## Cas

Duplicate create request, same idempotency key/different payload,
timeout after account creation, billing callback replay,
suspend/unsuspend race, terminate/package-change race, provider
credential revoked, webhook SSRF attempt, webhook replay, external 429,
event delivery DLQ, capacity reservation leak, account partially
provisioned, downgrade below usage, external service missing, WCP orphan
account, token leaked/revoked.

## Traitement

Evidence → state → retryability → reconciliation → compensation boundary
→ notification → audit → operator action → acceptance test.
