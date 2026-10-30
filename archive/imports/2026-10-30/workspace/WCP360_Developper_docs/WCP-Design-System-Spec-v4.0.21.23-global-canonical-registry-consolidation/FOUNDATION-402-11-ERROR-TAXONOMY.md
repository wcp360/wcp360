# V4.0.2 --- Error Taxonomy & Failure Semantics

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Classes

Validation, authentication, authorization, entitlement,
conflict/concurrency, precondition, dependency unavailable, timeout,
rate limit, unsupported capability, system execution, verification
failure, security rejection, internal invariant.

## Exposition

L'utilisateur reçoit code stable + message sûr + request/correlation
reference. Les détails internes sensibles restent dans les logs
protégés.

## Retryability

Chaque erreur indique conceptuellement `retryable`, `retry_after`
éventuel et si une verify/reconcile est nécessaire.

## Partiel

Une opération multi-step ne retourne pas un succès global si un
composant obligatoire a échoué. L'état final doit représenter le
résultat réel.
