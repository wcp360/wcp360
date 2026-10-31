# V4.0.2 --- Idempotency, Retry & Delivery Semantics

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Idempotency

Les opérations mutantes exposées à retry doivent définir une clé ou
identité d'opération stable dans un scope déterminé. Même clé + même
intent retourne le résultat existant ou son état ; même clé + intent
différent = conflit.

## Retry

Classifier : safe immediate, safe after verify, external retry with
backoff, non-retryable, manual reconciliation.

## Backoff

Backoff exponentiel borné + jitter pour services externes. Respecter
Retry-After lorsque fiable.

## Side effects

Après timeout réseau/IPC, considérer l'état **ambiguous** jusqu'à
verify/reconcile. Ne jamais rejouer une action destructive simplement
parce que la réponse manque.
