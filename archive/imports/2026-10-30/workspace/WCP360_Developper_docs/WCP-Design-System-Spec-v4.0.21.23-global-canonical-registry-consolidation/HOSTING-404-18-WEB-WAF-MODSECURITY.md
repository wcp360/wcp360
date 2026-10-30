# V4.0.4 --- Web WAF / ModSecurity Integration Boundary

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

## Capability

ModSecurity/compatible WAF est optionnel et détecté/certifié par engine.

## Policies

Off, monitor/detection, blocking selon admin policy ; rule sets
versionnés (ex. CRS si choisi ultérieurement).

## Tenant

WCP-USER peut éventuellement choisir un profil autorisé et consulter
événements scopés ; il ne charge pas de règles root arbitraires.

## Exclusions

Exclusions structurées, ciblées et auditées ; expiration optionnelle.
Éviter disable-global pour résoudre un faux positif.

## Updates

Rule-set update = package/content lifecycle avec validation,
canary/rollback si nécessaire.

## Privacy

WAF logs peuvent capturer payloads/secrets ; minimisation/redaction
obligatoires.
