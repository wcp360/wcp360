# V4.0.2 --- Reconciliation & Drift Management

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Boucle

Observe → compare desired/observed → classify drift → plan → authorize
automated remediation → apply → verify → update evidence.

## Classes

Benign/external, recoverable, security-sensitive, destructive-risk,
unsupported/manual.

## Politique

Le drift n'est pas toujours corrigé automatiquement. Une modification
externe d'un fichier critique peut nécessiter alerte + blocage plutôt
qu'écrasement.

## Ownership

Seules les ressources/configurations déclarées managed par WCP sont
candidates à reconciliation automatique.

## Evidence

Chaque reconcile produit cause, avant/après, action, résultat et
correlation.
