# V4.0.4 --- Hosting Core End-to-End Scenario Matrix

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

## Scénarios obligatoires

1.  Créer reseller → plan → account → domain → website → PHP pool.
2.  Créer account sans reseller.
3.  Quota parent insuffisant.
4.  Downgrade sous usage.
5.  Suspend/unsuspend account avec website actif.
6.  Terminate puis recover avant purge.
7.  Domain avec DNS externe.
8.  Domain provisioning où TLS/mail futur échoue sans détruire web.
9.  Move domain entre accounts.
10. Alias avec TLS manquant.
11. Nginx config invalide.
12. Reload échoue après activation.
13. Apache module conflict.
14. Nginx→Apache backend down.
15. LiteSpeed capability/licence degraded.
16. PHP version EOL.
17. Runtime removal encore référencée.
18. FPM socket collision.
19. Pool dépasse PID/RAM limit.
20. INI override interdit.
21. Extension partagée removal.
22. Cross-tenant document-root attempt.
23. Concurrent Website edit.
24. Worker crash pendant config apply.
25. Disk/inodes full pendant generation.

Chaque scénario documente state, Job, locks, audit, evidence, recovery
et expected UX.
