# V4.0.9 --- API-first Canonical Contract

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

## Principe

Les capacités WCP exposables suivent les mêmes services métier que
WCP-USER/WCP-ADMIN. L'API n'est pas une seconde logique métier.

## Surface

Resources, collections, actions explicites, Jobs pour opérations
longues, Events pour faits vérifiés et Problem/Error model commun.

## Versioning

Version majeure de contrat explicite ; compatibilité additive
privilégiée. Breaking changes suivent deprecation window/policy.

## Representation

IDs WCP opaques, timestamps avec timezone, enums documentés,
pagination/cursors et champs unknown-safe pour clients.

## Parité

UI peut composer plusieurs APIs, mais aucune action sensible ne doit
exister uniquement via endpoint caché non gouverné.
