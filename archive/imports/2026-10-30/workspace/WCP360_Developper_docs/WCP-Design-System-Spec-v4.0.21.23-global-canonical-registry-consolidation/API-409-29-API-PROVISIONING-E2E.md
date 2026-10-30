# V4.0.9 --- API/Billing/Provisioning End-to-End Scenarios

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

## Scénarios

1.  API service account creates account with idempotency key.
2.  Duplicate request returns same binding.
3.  Timeout after account actually created.
4.  WHMCS create/suspend/unsuspend/terminate.
5.  Blesta/FOSSBilling/HostBill capability differences.
6.  Package upgrade.
7.  Downgrade while disk usage exceeds quota.
8.  Billing active vs WCP suspended drift.
9.  External cancelled vs WCP active.
10. Webhook signed delivery/retry/DLQ.
11. Webhook SSRF to metadata endpoint rejected.
12. Inbound billing webhook replay rejected.
13. Token rotation with overlap.
14. Rate-limit handling.
15. Concurrent account mutations with stale ETag.
16. Provisioning capacity race.
17. Partial provisioning compensation.
18. Credential activation link expiry.
19. Integration credential revoked.
20. API cross-tenant access rejected.
21. Bulk partial success.
22. Event out-of-order consumer scenario.
23. Automation loop prevented.
24. Termination with backup/grace.
25. Provider adapter version revalidation.
