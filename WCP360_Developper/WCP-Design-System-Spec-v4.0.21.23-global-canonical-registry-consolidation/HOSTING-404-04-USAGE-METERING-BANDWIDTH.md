# V4.0.4 --- Usage Metering, Bandwidth & Reconciliation

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

## UsageCounter

Valeur, unité, scope, source, window, collected_at,
confidence/freshness.

## Mesures

Disk/inodes depuis filesystem/quota provider ; bandwidth depuis source
réseau/web/mail certifiée ; counts depuis control-plane ; CPU/RAM depuis
cgroups/provider.

## Règle

Absence de mesure = `unknown`, jamais zéro. Usage stale ne déclenche pas
automatiquement une suppression/suspension destructive.

## Bandwidth

Définir cycle mensuel/timezone, inbound/outbound inclus, protocoles
comptés, reset, overage policy et source de vérité.

## Reconcile

Comparer compteurs control-plane et providers ; expliquer écarts ;
conserver historique nécessaire à audit/facturation sans transformer WCP
en système comptable.
