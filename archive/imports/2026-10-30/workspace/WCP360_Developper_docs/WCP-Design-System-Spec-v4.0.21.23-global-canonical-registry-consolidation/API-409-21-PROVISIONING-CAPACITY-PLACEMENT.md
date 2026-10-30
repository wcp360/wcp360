# V4.0.9 --- Provisioning Capacity & Node Placement Boundary

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

## Inputs

Plan requirements, node health/capacity, service capabilities, storage
class, IP availability, locality/affinity future policies.

## Placement

Deterministic/traceable decision with selected node and
reasons/evidence.

## Reservation

Capacity reservation prevents concurrent over-allocation during
provisioning.

## Failure

Reservation expires/compensates if account creation fails.

## Boundary

Full cluster scheduler/HA remains separate subsystem; single-node
deployments still implement trivial placement contract.

## V4.0.10 normative deepening

- `OPS-4010-12-CAPACITY-FORECASTING.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.11 normative deepening

- `CLUSTER-4011-06-PLACEMENT-SCHEDULER.md`
- `CLUSTER-4011-07-CAPACITY-RESERVATION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
