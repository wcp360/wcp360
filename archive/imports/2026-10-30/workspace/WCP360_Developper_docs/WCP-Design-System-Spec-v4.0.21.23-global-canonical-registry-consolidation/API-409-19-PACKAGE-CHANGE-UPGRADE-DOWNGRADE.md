# V4.0.9 --- Package Change, Upgrade & Downgrade

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

## Mapping

External product change resolves new WCP Plan version/entitlements.

## Upgrade

Preflight capacity and dependencies → apply entitlements/quotas →
reconcile services → verify.

## Downgrade

Detect current usage exceeding new limits before apply. Policy: reject,
schedule/grace or restrict future growth; never silently delete data.

## Partial

If some limits apply and others fail, account becomes
degraded/reconcile-required with evidence.

## Billing rollback

WCP cannot silently reverse financial transaction; returns technical
failure for billing system handling.

## V4.0.12 normative deepening

- `LIFE-4012-08-OS-PACKAGE-LIFECYCLE.md`
- `LIFE-4012-06-SINGLE-NODE-UPGRADE.md`
- `LIFE-4012-07-CLUSTER-ROLLING-UPGRADE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
