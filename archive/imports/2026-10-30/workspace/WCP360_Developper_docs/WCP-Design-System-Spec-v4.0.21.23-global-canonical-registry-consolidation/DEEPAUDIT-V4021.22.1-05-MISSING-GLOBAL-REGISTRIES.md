# V4.0.21.22.1 --- Registres globaux manquants

V4.0.21.22 listait six gaps. Le deep audit élargit la fermeture
nécessaire.

## Registres obligatoires

1.  `GLOBAL-RESOURCE-REGISTRY`
2.  `GLOBAL-OPERATION-API-REGISTRY`
3.  `GLOBAL-PERMISSION-SCOPE-REGISTRY`
4.  `GLOBAL-JOB-RPC-ADAPTER-REGISTRY`
5.  `GLOBAL-PRIVILEGED-ACTION-RPC-AUTHZ-REGISTRY`
6.  `GLOBAL-LIFECYCLE-TRANSITION-REGISTRY`
7.  `GLOBAL-AUDIT-EVENT-REGISTRY`
8.  `GLOBAL-ERROR-REGISTRY`
9.  `GLOBAL-UI-API-STATE-REGISTRY`
10. `GLOBAL-RETRY-RECOVERY-REGISTRY`
11. `GLOBAL-VERSION-COMPATIBILITY-REGISTRY`
12. `GLOBAL-SECURITY-ACCEPTANCE-LEDGER`
13. `GLOBAL-E2E-TRACEABILITY-LEDGER`
14. `GLOBAL-P0-ACCEPTANCE-LEDGER`

## Pourquoi

Une famille peut être `READY FOR CROSS-FAMILY VALIDATION` tout en
laissant plusieurs décisions action-par-action implicites. Ces registres
sont le point où l'on prouve qu'aucun développeur n'aura à inventer une
permission, un retry, un RPC, un état, un code d'erreur ou une preuve
d'acceptation.
