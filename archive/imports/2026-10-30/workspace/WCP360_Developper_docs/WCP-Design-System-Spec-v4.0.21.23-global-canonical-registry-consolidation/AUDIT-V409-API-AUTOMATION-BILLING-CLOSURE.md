# V4.0.9 --- API, Automation, Billing & Provisioning Closure Audit

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

## Méthode

Contrairement à un mapping par noms supposés, cette passe a relié les
documents historiques par noms réellement présents contenant les
familles API/REST/GraphQL, webhook,
billing/WHMCS/Blesta/FOSSBilling/HostBill, provisioning et automation.

## Résultat

30 contrats normatifs V4.0.9 ajoutés. **60 documents historiques
existants** ont reçu un lien de deepening lorsque leur nom indiquait
directement cette famille.

## Fermetures majeures

API parity/RBAC, service accounts/tokens/scopes,
idempotency/concurrency, outbound/inbound webhook security, retries/DLQ,
billing boundary, adapters, provisioning saga, suspend/terminate,
package change, external-state reconciliation, credential delivery,
automation safety et contract certification.

## Point architectural

WCP reste le système d'autorité technique. Les outils billing restent
l'autorité commerciale pour les données qu'ils possèdent. Le lien est un
mapping/reconciliation explicite, jamais une fusion des modèles.
