# 102 --- System Users & Groups

## Statut et origine

\[SOURCE\] Users & Groups + Account/Hosting management.

## Objectif

Administrer comptes système lorsqu'ils relèvent du scope WCP-ADMIN.

## Pages / sous-vues

Users; Groups; User detail; Memberships; Shell/access.

## Ressources et données

uid/gid, username, groups, shell, home, lock state, source/owner.

## Actions opérateur

Create where policy allows; lock/unlock; memberships; shell; delete with
impact.

## Monitoring, santé et logs

Login/activity only if backend provides trustworthy data.

## Sécurité, permissions et audit

`system.users.*`; protéger root/system accounts; typed confirmations for
destructive changes.

## Contrat backend attendu

Backend enforces reserved accounts, UID/GID rules and object
authorization.

## États UI obligatoires

Loading, skeleton, empty, no-results, partial data, stale telemetry,
permission denied, service unavailable, maintenance, action pending,
action failed, success confirmé.

## Règles UX communes

-   Afficher serveur/nœud, scope et cible avant toute mutation.
-   Distinguer `Save`, `Apply`, `Reload` et `Restart`.
-   Une modification de configuration sensible présente un diff si
    possible.
-   Toute opération longue crée un job dans Task Center.
-   Ne jamais afficher un pourcentage de progression artificiel.
-   Les commandes/configurations proposées par l'UI restent validées et
    autorisées côté backend.
-   Les secrets sont redacted dans logs, audit et notifications.

## Critères d'acceptation

Impossible de supprimer/modifier un compte protégé via simple
manipulation UI.
