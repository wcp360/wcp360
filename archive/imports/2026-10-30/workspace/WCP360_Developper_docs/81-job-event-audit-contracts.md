# 81 --- Contrats Jobs, événements et audit

## Job

`id`, type, scope, target, state, progress si mesurable, steps,
created/started/finished, actor, result, error, retryability.

## Event

Événement métier ou système horodaté, non confondu avec une notification
UI.

## Audit

Actor réel, impersonated actor éventuel, action, target, scope,
timestamp, outcome, correlation id, changements pertinents redacted.

## Corrélation

Une mutation UI doit permettre de relier : requête → job → événements →
audit → notification éventuelle.

## Progress

Ne pas afficher un pourcentage artificiel si le backend ne sait pas
mesurer la progression.
