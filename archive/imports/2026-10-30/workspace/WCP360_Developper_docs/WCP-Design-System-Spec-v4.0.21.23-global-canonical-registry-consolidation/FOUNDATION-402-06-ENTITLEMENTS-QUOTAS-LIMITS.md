# V4.0.2 --- Entitlements, Quotas & Limits

## Statut

**NORMATIVE --- V4.0.2 FOUNDATIONS DEEP SPECIFICATION --- NO CODE**

Ce document approfondit les fondations transversales de WCP. Il ne
choisit pas de framework, de bibliothèque ni de format exécutable. Toute
implémentation future devra respecter ce contrat.

## Distinction

**Permission** = droit d'essayer une action. **Entitlement** =
fonction/quantité accordée commercialement. **Quota** = limite
mesurable. **Runtime limit** = contrainte technique imposée au
processus/service.

## Dimensions

Disk bytes/inodes, bandwidth, domains/subdomains, DB/users,
mailboxes/storage, backups, CPU, RAM, PIDs, I/O, runtimes/apps,
cron/jobs et limites API.

## Enforcement

Déclarer pour chaque limite : point de mesure, source de vérité,
hard/soft, fréquence de mesure, tolérance, comportement au dépassement,
grace et recovery.

## Downgrade

Un plan inférieur à l'usage actuel ne déclenche jamais une suppression
automatique. Le système passe en état non-compliant/restricted selon
policy et présente les remédiations.

## V4.0.12 normative deepening

- `LIFE-4012-21-ENTITLEMENT-CANONICAL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
