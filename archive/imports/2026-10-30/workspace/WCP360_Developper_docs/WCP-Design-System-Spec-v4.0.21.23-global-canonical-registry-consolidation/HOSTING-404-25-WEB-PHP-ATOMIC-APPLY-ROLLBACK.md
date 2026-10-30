# V4.0.4 --- Web/PHP Atomic Apply, Reload & Rollback

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

## Pipeline

Desired generation → render in staging → static semantic validation →
engine/PHP syntax validation → conflict checks → snapshot
last-known-good metadata → atomic activate → reload affected services →
probes → observed generation.

## Failure avant activation

Aucun changement actif ; Job failed avec diagnostics sûrs.

## Failure après activation

Tenter rollback vers génération connue valide si sûr → reload → verify.
Si résultat ambigu : degraded/needs_attention, pas faux succès.

## Multi-file

Une génération regroupe les fragments liés afin d'éviter vhost nouveau +
pool ancien ou inversement.

## Concurrency

Locks resource + shared service config selon besoin ; optimistic
generation sur edits.

## V4.0.12 normative deepening

- `LIFE-4012-16-ROLLBACK-RECOVERY-BOUNDARY.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
