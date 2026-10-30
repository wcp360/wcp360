# ADR-011 --- Firewall / nftables Strategy

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

**nftables natif** est la baseline V1. WCP ne construit pas son modèle
canonique autour de commandes iptables legacy.

## Ownership

Tables/chains WCP dans namespace dédié ; coexistence avec règles
opérateur détectée et respectée.

## Model

Desired service exposure → compiled ruleset → syntax/check → atomic
apply → verify.

## Defaults

Inbound deny-by-default pour installation contrôlée, established/related
autorisé, loopback, admin access et ports des services activés.

## Lockout protection

Avant mutation SSH/panel/network : preserve current admin path + timed
recovery/console guidance.

## Cluster/cloud

Security groups/provider firewalls sont couches externes et ne sont pas
confondus avec nftables.
