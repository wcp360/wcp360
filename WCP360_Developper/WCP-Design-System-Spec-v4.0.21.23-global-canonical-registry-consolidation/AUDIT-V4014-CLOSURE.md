# V4.0.14 --- Installation & Server Lifecycle Closure Audit

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

## Base

La passe part de V4.0.13 et n'annule pas son gel fonctionnel : elle
traite une exception justifiée par l'audit, à savoir le lifecycle
complet d'installation/finalisation/réparation qui n'avait pas de
famille canonique suffisamment profonde.

## Résultat

-   **41 contrats normatifs V4.0.14** ajoutés.
-   **126 documents historiques existants** reliés par deepening.
-   Mail antispam existant consolidé dans un control-plane plus large au
    lieu d'être dupliqué.
-   Wizard/First Boot/Production Readiness/Repair/Rescue deviennent des
    ressources et state machines explicites.

## Règle

Cette famille est la dernière extension fonctionnelle autorisée avant
V4.0.15. Toute autre proposition doit d'abord démontrer qu'elle est
indispensable et absente, et non simplement une capability intéressante.
