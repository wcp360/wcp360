# V4.0.2 --- Locking & Concurrency Model

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Objectif

Éviter deux mutations incompatibles sur la même ressource sans
transformer WCP en système globalement sérialisé.

## Lock scopes

Resource lock, parent/child lock, service/configuration lock, node-level
maintenance lock. Les locks globaux sont exceptionnels.

## Ordre

Définir un ordre canonique d'acquisition pour empêcher les deadlocks.
Aucun Job ne conserve un lock pendant une attente externe longue sauf
justification.

## Optimistic concurrency

Les modifications UI/API doivent pouvoir détecter une génération/ETag
obsolète plutôt que d'écraser silencieusement le travail d'un autre
administrateur.

## Crash

Les locks possèdent owner, lease/TTL lorsque pertinent et mécanisme de
récupération ; pas de lock éternel basé sur un simple fichier PID non
vérifié.
