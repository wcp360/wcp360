# V4.0.2 --- Configuration Hierarchy & Ownership

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Hiérarchie conceptuelle

Built-in safe defaults → distribution/OS profile → server/operator
configuration → plan/package policy → tenant/account policy →
resource-specific override.

Une couche basse ne peut pas contourner une contrainte de sécurité
imposée par une couche supérieure non délégable.

## Ownership

Chaque fichier ou fragment généré doit avoir **un propriétaire
fonctionnel unique**. WCP ne modifie pas aveuglément des fichiers dont
il ne maîtrise pas le lifecycle.

## Managed blocks

Si coexistence avec configuration externe : mécanisme de managed block
ou include dédié, détection de drift, validation avant activation et
sauvegarde de la dernière configuration connue valide.

## Secrets

Les secrets ne sont pas des valeurs de configuration ordinaires. Ils
sont référencés par secret reference et injectés uniquement au
consommateur autorisé.

## Reload

Toute configuration déclare : render → validate → stage → atomic
activate → reload/restart → verify → rollback/reconcile.
