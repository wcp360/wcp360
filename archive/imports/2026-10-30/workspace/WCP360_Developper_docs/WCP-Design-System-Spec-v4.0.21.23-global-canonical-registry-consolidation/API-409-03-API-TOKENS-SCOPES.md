# V4.0.9 --- API Tokens, Scopes & Credential Lifecycle

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

## Token

Secret affiché une fois lorsque possible ; hash/reference côté WCP ;
prefix/fingerprint non secret pour identification.

## Scopes

Scopes métier granulaires (`domains.read`, `accounts.provision`, etc.)
combinés au RBAC/ownership ; scope seul ne confère pas ownership.

## Expiration

Expiry recommandée, last-used, source metadata raisonnable,
revoke/rotate.

## Rotation

Overlap contrôlé entre ancien/nouveau token puis révocation ; pas de
changement brutal obligatoire pour intégrations critiques.

## Logs

Token jamais loggé, même dans headers/error dumps.
