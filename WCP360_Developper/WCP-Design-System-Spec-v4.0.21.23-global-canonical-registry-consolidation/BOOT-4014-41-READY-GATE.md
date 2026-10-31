# V4.0.14 --- V4.0.14 READY Gate

## Statut

**NORMATIVE --- V4.0.14 INSTALLATION, BOOTSTRAP, FIRST-RUN,
FINALIZATION, REPAIR & SERVER LIFECYCLE COMPLETENESS --- NO CODE**

Cette passe ferme la dernière grande surface opérationnelle identifiée
après V4.0.13 : faire passer une machine supportée d'un état brut à un
WCP exploitable, vérifié, récupérable et administrable sans dépendre
d'actions root opaques.

## Invariants

-   `installed` ≠ `configured` ≠ `healthy` ≠ `production-ready`.
-   L'installateur et le Repair Center utilisent les mêmes contrats
    typés, validation, audit et reconciliation que le reste de WCP.
-   Aucun wizard ne contourne RBAC, secrets, Jobs, audit ou health
    verification.
-   Une détection automatique n'est jamais traitée comme vérité
    irrévocable : elle produit un état observé vérifiable.
-   Les profils simplifient la configuration sans cacher les
    conséquences.
-   Les opérations destructives, de réparation ou de récupération sont
    prévisualisées et auditées.
-   L'absence volontaire d'un service (mail, DNS local, DB locale, etc.)
    n'est pas une erreur si le profil le permet.
-   Un serveur ne peut pas être déclaré READY si un finding bloquant
    subsiste.

## Fermé par cette passe

Installer trust/state, preflight/conflicts, first boot, setup wizard,
identity/network/IPAM/firewall, DNS/web/DB/mail bootstrap,
antispam/reputation/quarantine/client setup, hardening,
backup/observability bootstrap, production readiness, final report,
diagnostics/repair/rescue/admin recovery/control-plane restore,
transfer/migration/cutover, reconfigure/external modes, offline/cloud
bootstrap, emergency failures, uninstall and unattended automation.

## Gate

Aucun serveur n'est déclaré READY uniquement parce que l'installation
s'est terminée.

## Statut

**V4.0.14 = SPECIFIED-DEEP --- NO CODE --- FINAL COMPLETENESS RE-AUDIT
REQUIRED.**

La prochaine passe est **V4.0.15 --- Final Completeness Re-Audit & Scope
Classification**, qui doit rechercher une dernière fois les familles
réellement absentes, classifier CORE/OPTIONAL/EXPERIMENTAL/FUTURE et
décider si le corpus peut passer à ADR Closure.
