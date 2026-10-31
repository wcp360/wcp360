# ADR-007 --- Audit Retention & Export

## Statut

**ACCEPTED --- V3.6 Phase 1**

Cette décision est un **choix d'architecture WCP**. Lorsqu'une donnée
externe est citée, elle sert uniquement à vérifier la viabilité du choix
et ne remplace pas la décision produit.

## Décision

Audit WCP conservé **365 jours par défaut**, configurable par politique
avec minimum produit de 90 jours sauf édition/dev explicitement
non-production. Export SIEM/syslog disponible sans désactiver la copie
locale.

## Intégrité

Événements append-only au niveau applicatif ; purge uniquement par job
de rétention privilégié et auditée. Les secrets sont redacted avant
persistence/export.

## Scope

Cette durée est une valeur produit par défaut, pas une affirmation de
conformité universelle ; les obligations légales/contractuelles du
déployeur peuvent imposer une autre durée.
