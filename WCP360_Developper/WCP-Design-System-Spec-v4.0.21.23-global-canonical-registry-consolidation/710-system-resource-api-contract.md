# 710 --- System/OS Resource & API Contract

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Ressources

`HostIdentity`, `OperatingSystem`, `Kernel`, `SystemUser`,
`SystemGroup`, `Package`, `Process`, `SystemService`, `Filesystem`,
`ScheduledTask`, `SystemLogSource`, `MaintenanceState`.

## API

Lecture : `/api/v1/nodes/{nodeId}/system/*`. Mutations
package/service/maintenance/reboot/shutdown retournent des Jobs.

## Règle

La mention historique `API: No` est définitivement remplacée par
**Scoped/Admin API** : seules les primitives internes root restent
privées.

## Permissions

`system.read`, `system.users.manage`, `system.packages.manage`,
`system.services.manage`, `system.scheduler.manage`,
`system.maintenance`, `system.reboot`, `system.shutdown`.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
