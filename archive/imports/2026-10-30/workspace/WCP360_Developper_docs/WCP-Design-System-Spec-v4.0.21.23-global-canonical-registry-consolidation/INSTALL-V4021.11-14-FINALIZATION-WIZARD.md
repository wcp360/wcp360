# V4.0.21.11 --- Finalization & First-Run Wizard

## Étapes

1.  créer/valider WCP-ADMIN principal ;
2.  MFA obligatoire pour admin privilégié ;
3.  hostname/timezone/NTP ;
4.  management URL/TLS ;
5.  network/listening/firewall ;
6.  sélectionner web stack certifié ;
7.  sélectionner DB/cache capabilities ;
8.  mail stack optionnel + hostname/rDNS guidance ;
9.  DNS backend/provider ;
10. backup target + first verified backup ;
11. malware scanner/security profile ;
12. update channel/maintenance window ;
13. notification channels ;
14. telemetry/privacy policy ;
15. default hosting plan/isolation profile ;
16. run Doctor + readiness suite ;
17. invalidate bootstrap access ;
18. declare server state.

## États finaux

`SETUP_REQUIRED`, `CONFIGURING`, `VERIFYING`, `READY_WITH_WARNINGS`,
`READY`, `BLOCKED`.

`READY` exige que tous les P0 readiness checks requis par le host
profile soient validés.
