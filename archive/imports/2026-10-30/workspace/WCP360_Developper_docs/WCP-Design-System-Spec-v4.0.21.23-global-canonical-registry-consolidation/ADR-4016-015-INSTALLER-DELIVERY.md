# ADR-015 --- Installer & Bootstrap Delivery

## Statut

**ACCEPTED FOR V1 --- V4.0.16 --- NO CODE**

## Autorité

Cet ADR ferme un choix P0 identifié par `ADR-V4015-07-REGISTER.md`. Il
concrétise la baseline V4.0.15 sans élargir le scope fonctionnel.

## Règles

-   Toute implémentation incompatible exige un ADR de supersession.
-   Les secrets ne sont jamais placés en clair dans les arguments de
    processus, logs ou artefacts exportés.
-   Toute mutation privilégiée reste typée, auditable,
    idempotente/reconciliable et vérifiée par état observé.
-   Le V1 reste single-node-first tout en conservant des interfaces qui
    n'interdisent pas le cluster futur.

## Décision

Un petit bootstrap officiel est distribué comme artefact
versionné/signé. Il ne contient pas le panel complet et ne fait pas
confiance au transport seul.

## Flow

Platform preflight → trust bootstrap → fetch manifest → verify → fetch
packages → verify → install package set → initialize
PostgreSQL/control-plane/root key → start services → First Boot Wizard.

## Modes

Online repository, proxy, internal mirror, signed offline bundle.

## Credentials

Aucun mot de passe admin par défaut. Le premier admin est créé dans le
First Boot flow avec MFA/recovery policy.

## Re-run

State durable et idempotence ; reprise après interruption.
