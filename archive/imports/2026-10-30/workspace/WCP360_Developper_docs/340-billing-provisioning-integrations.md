# 340 --- Billing & Provisioning Integrations

## Origine

**\[V3.1\]** Intégration/API pour WHMCS, Blesta, HostBill et autres
systèmes de billing/provisioning.

## Objectif

WCP fournit un **Provisioning API** et des connecteurs ; le moteur de
facturation reste externe sauf décision produit ultérieure.

## Événements

Create account; update package; suspend; unsuspend; terminate; change
password/credential flow; usage query; service status.

## Pages WCP-ADMIN

Integrations; Credentials; Product/Package Mapping; Webhooks;
Provisioning Jobs; Failures; Audit.

## Idempotence

Chaque commande externe doit accepter une clé/idempotency strategy afin
d'éviter les doubles créations lors de retries.

## Sécurité

API scopes dédiés, IP/network policy optionnelle, secret rotation,
webhook signatures, rate limiting, audit.

## Connecteurs

WHMCS, Blesta et HostBill sont des adaptateurs distincts. Leur présence
ne doit pas être codée en dur dans le cœur.

## États UI obligatoires

Loading, skeleton, empty, no-results, permission denied, partial data,
stale data, unavailable, maintenance, pending, failed et success
confirmé par le backend.

## Règles transversales

-   WCP-ADMIN administre la capacité globale, les services, politiques
    et limites.
-   WCP-USER n'accède qu'aux ressources et fonctions explicitement
    attribuées à son compte/revendeur.
-   Toute mutation sensible est autorisée côté backend, auditée et
    reliée à un correlation/job ID.
-   Toute opération longue ou massive passe par Task Center.
-   Les secrets ne sont jamais écrits dans les logs, notifications,
    audit ou sorties IA.
-   Les modules sont pilotés par capabilities : une technologie non
    installée n'est pas présentée comme disponible.
