# ADR-002 --- Control-plane Languages, Runtime & Package Layout

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

La séparation déjà définie dans le corpus est figée : - **Go** : `wcpd`,
daemon système privilégié et agents système typés. - **PHP 8.x supporté
par la matrice WCP** : WCP-ADMIN/WCP-USER, API HTTP et orchestration
applicative non-root. - Frontend : assets web versionnés servis par le
control-plane ; aucun Node.js requis en production uniquement pour faire
tourner le panel.

## Comptes

`wcp` = service web/control-plane non privilégié. `root` n'exécute que
le daemon/units strictement nécessaires. Les workloads tenants
n'utilisent jamais l'identité `wcp`.

## Packaging

Paquets WCP signés installent sous `/opt/wcp`; configuration opérateur
sous `/etc/wcp`; état mutable sous `/var/lib/wcp`. Aucun code applicatif
mutable dans document-root public.

## Interdit

PHP-FPM du panel ne tourne pas root ; aucune UI ne lance
`sudo shell-command`; aucun plugin tiers n'est chargé dans le daemon
root sans contrat explicite.
