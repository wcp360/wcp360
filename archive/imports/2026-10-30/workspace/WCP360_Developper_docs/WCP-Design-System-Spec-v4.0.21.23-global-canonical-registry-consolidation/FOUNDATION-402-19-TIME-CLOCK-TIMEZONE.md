# V4.0.2 --- Time, Clock & Timezone Contract

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Canonique

Persist timestamps in UTC avec timezone-aware representation ; affichage
dans timezone utilisateur/opérateur.

## Monotonicité

Les durations/timeouts utilisent une clock monotonic lorsque disponible
; les dates métier utilisent wall clock.

## DST

Cron/scheduler doit définir le comportement des heures inexistantes ou
répétées.

## Clock drift

Une dérive affecte TLS, TOTP, sessions, audit, scheduler et signatures.
Le node health doit pouvoir signaler une synchronisation temps dégradée.
