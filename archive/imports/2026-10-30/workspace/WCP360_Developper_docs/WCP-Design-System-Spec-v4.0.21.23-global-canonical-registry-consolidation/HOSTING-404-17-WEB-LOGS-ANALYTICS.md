# V4.0.4 --- Per-site Web Logs, Rotation & Access

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

## Logs

Access/error séparés par Website/account avec path géré et ownership
correct.

## Formats

Profils de format versionnés. Custom format tenant seulement via schéma
borné si retenu.

## Access

WCP-USER ne lit que ses logs ; pagination/time range ; téléchargements
bornés ; redaction des données sensibles lorsque possible.

## Rotation

Size/time/retention/compression et disk quota interaction. Rotation ne
casse pas file descriptors/reload semantics.

## Analytics

Les statistiques agrégées sont distinctes des logs bruts.
Source/freshness affichées.

## Privacy

IP/User-Agent/URLs peuvent contenir données personnelles/secrets
query-string ; politique de rétention et masquage nécessaire.

## V4.0.10 normative deepening

- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-07-LOG-PIPELINE-RETENTION.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
