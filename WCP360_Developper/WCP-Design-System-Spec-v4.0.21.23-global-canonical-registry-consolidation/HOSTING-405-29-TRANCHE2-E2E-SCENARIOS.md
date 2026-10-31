# V4.0.5 --- Hosting Core Tranche 2 End-to-End Scenarios

## Statut

**NORMATIVE --- V4.0.5 HOSTING CORE DEEP SPECIFICATION --- TRANCHE 2 ---
NO CODE**

Portée : DNS authoritative/providers/DNSSEC → TLS/ACME/certificats →
MySQL/MariaDB → PostgreSQL → Redis/Memcached → outils DB/SSO.

## Invariants

-   `Domain`, `DNSZone`, `RegistrarDomain`, `Certificate`, `Database`,
    `DatabaseUser`, `CacheInstance` restent des ressources distinctes.
-   WCP distingue toujours control-plane desired state, état réellement
    observé et état externe.
-   Les credentials ne sont ni des identifiants de ressource ni des
    données de log/audit.
-   Les adapters externes et moteurs locaux n'acceptent que des
    opérations typées et bornées.
-   Toute opération destructive, rotation de secret ou mutation
    DNS/TLS/DB sensible possède verify/reconcile.
-   WCP-USER n'obtient jamais de primitives root, superuser DB, accès
    inter-tenant ou configuration fournisseur arbitraire.

## Scénarios

1.  Domain → local DNS zone → A/AAAA/MX/TXT.
2.  External provider zone with API timeout.
3.  Delegation mismatch.
4.  DNSSEC enable and DS publication.
5.  DNSSEC rollover interrupted.
6.  PTR on dedicated vs shared IP.
7.  Website → ACME HTTP-01 → deploy → live verify.
8.  Wildcard → DNS-01.
9.  Renewal failure while old cert remains valid.
10. Imported cert key mismatch.
11. MySQL DB/user/grant provision.
12. PostgreSQL DB/role provision.
13. Remote DB allowlist/TLS.
14. Credential rotation failure.
15. Backup then destructive restore.
16. Major DB migration preflight failure.
17. Redis isolated instance with memory cap.
18. Memcached private ephemeral instance.
19. phpMyAdmin SSO without URL credential.
20. Cross-tenant DB reference rejected.
21. Worker crash after DB CREATE.
22. Disk full during DB backup.
23. DNS provider credential revoked.
24. ACME issuer rate-limited.
25. Clock drift affects TLS validation.

Chaque scénario doit produire expected
resources/states/jobs/audit/evidence/recovery.
