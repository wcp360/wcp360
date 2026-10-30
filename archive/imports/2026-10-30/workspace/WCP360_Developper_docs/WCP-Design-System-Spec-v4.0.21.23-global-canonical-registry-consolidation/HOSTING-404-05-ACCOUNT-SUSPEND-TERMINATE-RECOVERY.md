# V4.0.4 --- Account Suspension, Termination & Recovery

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

## Suspension

Motif : admin, billing connector, abuse/security, quota/policy. La
raison et l'acteur sont conservés.

## Matrice d'effets

Chaque sous-système déclare `suspend`, `read-only`, `deny new sessions`,
`continue receive`, `continue backup` ou `unaffected`. Par exemple,
bloquer web n'implique pas nécessairement supprimer DNS/TLS.

## Unsuspend

Revalidate parent/plan/security state → reverse effects → verify each
subsystem → active/degraded.

## Termination

Deux phases minimales : `terminate` logique puis `purge` après
rétention. Les backups peuvent suivre une rétention indépendante.

## Recovery

Un terminate partiellement exécuté devient `needs_attention/degraded`,
avec inventaire précis des ressources restantes.
