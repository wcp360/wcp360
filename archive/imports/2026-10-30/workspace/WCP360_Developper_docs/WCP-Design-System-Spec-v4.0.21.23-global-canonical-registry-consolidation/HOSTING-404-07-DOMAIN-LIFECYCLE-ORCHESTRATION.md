# V4.0.4 --- Domain Lifecycle & Orchestration

## Statut

**NORMATIVE --- V4.0.4 HOSTING CORE DEEP SPECIFICATION --- TRANCHE 1 ---
NO CODE**

Cette spécification approfondit Accounts / Plans / Reseller → Domains →
Web Stack → Apache/Nginx/LiteSpeed → PHP/PHP-FPM. Elle instancie V4.0.2
et V4.0.3 sans produire de code.

## Invariants

-   WCP-ADMIN administre hôte, capacités, politiques, moteurs et limites
    maximales.
-   WCP-USER administre uniquement les ressources déléguées de son
    scope.
-   Hiérarchie commerciale et RBAC restent distincts.
-   Toute ressource possède desired/observed state, generation et
    ownership.
-   Toute mutation sensible suit validate → authorize → entitlement →
    preflight → job/lock → apply → verify → reconcile → audit.
-   Les fichiers web/PHP générés sont des projections, jamais la source
    de vérité.
-   Aucun tenant ne fournit de configuration root arbitraire.

## Provision

Request → scope/entitlement → collision check → Domain → options
DNS/Web/TLS/Mail → child Jobs → verify → active/degraded.

## Checkpoints

Chaque sous-système garde son résultat. Un échec TLS n'efface pas un
Website fonctionnel ; un échec mail n'efface pas DNS.

## Delete

Intentions séparées : detach website, delete DNS zone, disable mail,
revoke/delete certificate selon policy, registrar operation. Aucune
suppression registrar implicite.

## Move

Transfert entre HostingAccounts : vérifier permissions des deux scopes,
plan compatibility, filesystem/Unix identity, web/PHP, mail/DB ownership
et downtime expectations.

## Suspension

Domain-level suspension doit être distinguée de account suspension.

## V4.0.12 normative deepening

- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
