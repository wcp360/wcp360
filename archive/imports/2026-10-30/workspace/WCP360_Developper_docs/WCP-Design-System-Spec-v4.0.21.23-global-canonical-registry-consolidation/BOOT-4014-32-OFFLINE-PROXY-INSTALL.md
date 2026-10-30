# V4.0.14 --- Offline, Proxy & Mirror Installation

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

## Modes

Direct internet, authenticated proxy, internal mirror, signed offline
bundle.

## Trust

Transport source never replaces artifact verification.

## Proxy

Credentials secret-safe and absent from logs/process arguments.

## Offline

Bundle includes installer/release manifest, artifacts,
signatures/provenance and compatibility metadata.

## Finalization

External checks unavailable offline become UNKNOWN/WARNING according
policy, not false PASS.
