# 176 --- Reverse DNS / PTR

## Statut et origine

\[SOURCE\] Reverse DNS/PTR.

## Objectif

Gérer PTR uniquement pour plages dont WCP possède la capability.

## Pages / sous-vues

IP list; PTR; provider/delegation status.

## Ressources et données

IP, reverse name, ownership/capability, effective state.

## Actions opérateur

Set/clear/recheck.

## Monitoring, santé et logs

Propagation/validation timestamp.

## Sécurité, permissions et audit

Authorization by IP resource.

## Contrat backend attendu

Provider/IPAM adapter.

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

Pas de bouton Edit si WCP n'a pas autorité sur le PTR.
