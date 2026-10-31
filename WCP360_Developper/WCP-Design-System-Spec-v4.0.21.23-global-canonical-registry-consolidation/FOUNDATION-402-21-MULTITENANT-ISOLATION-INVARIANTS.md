# V4.0.2 --- Multi-tenant Isolation Invariants

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Invariants

Un tenant ne lit, ne modifie, ne référence ni ne déduit l'existence
d'une ressource hors scope sans permission globale explicite.

## Couches

UI filtering n'est pas sécurité. Vérification obligatoire dans
application/policy, repositories/queries scopées, filesystem roots, Unix
identities, services/runtimes, DB/cache/mail et backup.

## IDs

Les IDs opaques réduisent l'énumération mais ne remplacent jamais
l'autorisation.

## Tests

Chaque module multi-tenant possède tests négatifs cross-tenant : read,
mutate, search, export, Job lookup, logs/audit exposés et références
indirectes.
