# V4.0.9 --- External Notifications: Email, Slack/Discord-style Webhooks

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

## Boundary

Notification channels are integrations, not trusted command channels.

## Payload

Minimal event/finding summary, deep-link/resource ID, severity and
timestamp; secrets/PII minimized.

## Webhook destinations

Use outbound webhook SSRF/TLS protections.

## Delivery

Retry/failure state observable; notification failure doesn't roll back
original business action.

## Tenant branding

Reseller/tenant templates only within safe templating/escaping
constraints.

## V4.0.10 normative deepening

- `OPS-4010-19-NOTIFICATION-ROUTING.md`
- `OPS-4010-20-NOTIFICATION-TEMPLATES.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
