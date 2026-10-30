# ADR-003 --- Control-plane Database

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

## Options

SQLite ; MariaDB/MySQL ; PostgreSQL.

## Décision

**PostgreSQL est la base de données canonique du control-plane WCP.**
MariaDB/MySQL reste la baseline de base de données d'hébergement client
et constitue une frontière différente.

## Raisons

Transactions fortes, contraintes, JSONB lorsque justifié, primitives de
concurrence et trajectoire HA mature. Séparer la DB du panel des DB
clients réduit les couplages de cycle de vie.

## Règles

Instance/database dédiée WCP ; compte applicatif non-superuser ;
migrations versionnées ; aucune modification manuelle de schéma en
production ; sauvegarde control-plane obligatoire.

## V1

Local PostgreSQL sur single-node. L'interface de connexion/configuration
n'interdit pas une topologie externe/HA future.

## Rollback

Migration destructive irréversible interdite sans stratégie
expand/migrate/contract et recovery validée.
