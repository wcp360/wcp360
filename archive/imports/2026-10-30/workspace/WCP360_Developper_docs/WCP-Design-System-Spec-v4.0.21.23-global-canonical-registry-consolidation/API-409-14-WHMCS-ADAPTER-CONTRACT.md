# V4.0.9 --- WHMCS Provisioning Adapter Contract

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

## Operations

Create/provision, suspend, unsuspend, terminate, change package,
credential/access metadata and status query according certified module
interface.

## Mapping

WHMCS product/service IDs map to WCP Plan/Reseller/Account intent
through explicit configuration.

## Idempotency

WHMCS retry of create must resolve existing ProvisioningBinding before
creating account.

## Errors

Return normalized actionable provider/module error without leaking WCP
internals/secrets.

## Status

WHMCS status and WCP observed account state remain separately visible
for reconciliation.
