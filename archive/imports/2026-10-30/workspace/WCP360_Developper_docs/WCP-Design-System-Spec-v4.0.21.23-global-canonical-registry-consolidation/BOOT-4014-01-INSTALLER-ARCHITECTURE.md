# V4.0.14 --- Installer Architecture & Trust Boundary

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

## Rôle

Bootstrap minimal → vérification de plateforme → acquisition vérifiée
des artefacts → installation des composants WCP → initialisation du
control-plane → handoff au First Run.

## Sécurité

Pas de `curl | sh` comme contrat de confiance. Les artefacts suivent
V4.0.12 : manifest, digest, signature/provenance et canal.

## Privilèges

Les phases root sont minimisées et typées. Les secrets initiaux ne
transitent pas dans logs/process list.

## Etat

L'installateur persiste un état de phase permettant
reprise/reconciliation après interruption.

## Idempotence

Relancer après panne inspecte l'état avant toute nouvelle mutation.
