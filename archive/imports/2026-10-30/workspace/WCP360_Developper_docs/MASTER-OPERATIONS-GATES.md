# MASTER OPERATIONS GATES

## Gate avant toute mutation système

1.  Capability/backend détecté.
2.  Permission + scope vérifiés.
3.  Configuration actuelle collectée.
4.  Preflight dépendances/ports/capacité.
5.  Diff/impact présenté lorsque pertinent.
6.  Backup/snapshot/recovery path défini pour action risquée.
7.  Mutation via job/transaction.
8.  Validation syntaxique avant reload.
9.  Health/connectivity check après apply.
10. Commit ou rollback/recovery.
11. Audit + correlation ID.
12. Notification adaptée au résultat.

## Gate anti-lockout

Obligatoire pour réseau, firewall, SSH, SSO et changements d'identité
privilégiée.

## Gate secrets

Aucun secret dans CLI args persistants, logs, audit, telemetry, URLs ou
analytics.
