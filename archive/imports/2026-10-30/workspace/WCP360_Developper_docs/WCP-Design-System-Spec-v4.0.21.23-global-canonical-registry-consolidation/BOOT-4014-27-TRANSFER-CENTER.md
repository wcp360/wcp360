# V4.0.14 --- Transfer & Migration Center

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

## Sources

WCP, cPanel/WHM, Plesk, DirectAdmin, HestiaCP and Generic Linux via
dedicated adapters when implemented/certified.

## Pipeline

Discover → inventory → compatibility report → mapping → pre-copy →
incremental/delta where supported → maintenance/cutover → final sync →
activate → verify → rollback window.

## Objects

Accounts, domains, DNS, web files, databases, mailboxes/mail, SSL, cron,
FTP/SFTP, PHP/app settings and supported metadata.

## Truth

Unsupported source feature is reported, never silently dropped.
