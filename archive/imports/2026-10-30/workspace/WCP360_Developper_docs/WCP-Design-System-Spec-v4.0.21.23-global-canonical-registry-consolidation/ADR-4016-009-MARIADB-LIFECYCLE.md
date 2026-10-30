# ADR-009 --- MariaDB/MySQL Hosting Lifecycle & Privilege Model

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

**MariaDB** est l'engine SQL client de référence V1. Compatibilité MySQL
n'est déclarée que par matrice/adapters certifiés.

## Isolation

Chaque DB/app utilise des comptes dédiés ; pas de partage du compte
root. Host grants minimaux ; remote access désactivé par défaut.

## Administration

WCP utilise un credential de provisioning protégé par secret store,
distinct des credentials clients.

## Naming

Noms DB/users dérivés d'identifiants canoniques avec contraintes de
longueur et collision ; le display name n'est pas directement un
identifiant système.

## Lifecycle

Create → grants → verify → rotate/reset → backup/restore → suspend
policy → delete avec retention/recovery policy.

## SSO admin

phpMyAdmin/Adminer est intégré via mécanisme de session/token borné ;
aucun mot de passe root DB n'est transmis au navigateur.
