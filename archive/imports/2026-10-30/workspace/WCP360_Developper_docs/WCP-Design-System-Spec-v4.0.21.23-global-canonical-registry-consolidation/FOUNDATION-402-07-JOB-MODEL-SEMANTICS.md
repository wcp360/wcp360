# V4.0.2 --- Job Model & Execution Semantics

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Job

Toute mutation longue, privilégiée, distribuée ou potentiellement
retryable devient un Job.

## États

`queued`, `leased`, `running`, `waiting_external`, `retry_scheduled`,
`cancel_requested`, `succeeded`, `failed`, `cancelled`, `timed_out`,
`needs_attention`.

## Données minimales

action, actor, tenant/resource, desired generation,
correlation/causation, attempt, deadline, idempotency context, lock
requirements, progress, result/error class et evidence.

## Livraison

Le modèle doit supporter **at-least-once**. Une implémentation ne doit
jamais supposer exactly-once pour des effets externes.

## Lease

Un worker obtient un lease borné et heartbeat. À expiration, le Job
n'est repris qu'après règles de recovery/reconciliation.

## Cancellation

Annuler une demande n'annule pas magiquement un side effect déjà
produit. Chaque action déclare ses safe cancellation points.
