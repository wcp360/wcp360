# 711 --- System Jobs / Events / Audit

## Statut

**CANONICAL --- IMPLEMENTATION CONTRACT --- V3.7 Phase 1**

Ce document transforme les décisions V3.6 déjà acceptées en contrat de
développement. Il ne remplace pas les règles de
`MASTER-SECURE-DEVELOPMENT-STANDARD.md`.

## Jobs

`package.install`, `package.remove`, `package.update`, `service.start`,
`service.stop`, `service.reload`, `service.restart`, `system.reboot`,
`system.shutdown`, `maintenance.enter/exit`.

## Registry

`service_id` et package capability proviennent des registries/adapters ;
aucune unité systemd ou commande arbitraire depuis l'API.

## Events

`service.state_changed`, `package.changed`,
`system.maintenance_changed`, `system.reboot_requested`.

## Audit

Avant/après lorsque pertinent, node cible, opérateur, impact et
résultat.
