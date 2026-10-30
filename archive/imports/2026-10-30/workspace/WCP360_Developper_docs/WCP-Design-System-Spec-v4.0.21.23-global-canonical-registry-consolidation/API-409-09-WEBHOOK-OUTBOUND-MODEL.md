# V4.0.9 --- Outbound Webhook Subscription Model

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

## Endpoint

Owner scope, URL, event filters, secret ref, status, TLS policy,
delivery policy.

## Events

Faits versionnés, event ID stable, occurred_at, resource refs, tenant
scope et payload minimal.

## SSRF

Destination validation, DNS/IP re-resolution policy et blocage des
réseaux metadata/control-plane/internal selon architecture.

## TLS

HTTPS baseline ; certificat vérifié. Pas de disable-TLS-verify tenant.

## Lifecycle

Create → challenge/test optional → active → degraded/disabled.
