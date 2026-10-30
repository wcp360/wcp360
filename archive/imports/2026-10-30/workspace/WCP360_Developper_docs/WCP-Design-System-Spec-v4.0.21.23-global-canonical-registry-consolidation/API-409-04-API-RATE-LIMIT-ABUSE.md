# V4.0.9 --- API Rate Limits, Quotas & Abuse Controls

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

## Dimensions

Identity, tenant/reseller, IP/network signal, endpoint/action class et
global protection.

## Responses

Limite atteinte produit erreur normalisée et retry hints lorsque
possible.

## Mutations

Rate limiting ne remplace pas idempotency.

## Protection

Burst, sustained abuse, expensive searches, login/token brute force et
webhook flood.

## Fairness

Un tenant ne doit pas épuiser les workers/API capacity des autres
tenants.
