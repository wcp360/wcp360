# V4.0.14 --- Installation & Server Lifecycle E2E Matrix

## Statut

**NORMATIVE --- V4.0.14 INSTALLATION, BOOTSTRAP, FIRST-RUN,
FINALIZATION, REPAIR & SERVER LIFECYCLE COMPLETENESS --- NO CODE**

Cette passe ferme la dernière grande surface opérationnelle identifiée
après V4.0.13 : faire passer une machine supportée d'un état brut à un
WCP exploitable, vérifié, récupérable et administrable sans dépendre
d'actions root opaques.

## Invariants

-   `installed` ≠ `configured` ≠ `healthy` ≠ `production-ready`.
-   L'installateur et le Repair Center utilisent les mêmes contrats
    typés, validation, audit et reconciliation que le reste de WCP.
-   Aucun wizard ne contourne RBAC, secrets, Jobs, audit ou health
    verification.
-   Une détection automatique n'est jamais traitée comme vérité
    irrévocable : elle produit un état observé vérifiable.
-   Les profils simplifient la configuration sans cacher les
    conséquences.
-   Les opérations destructives, de réparation ou de récupération sont
    prévisualisées et auditées.
-   L'absence volontaire d'un service (mail, DNS local, DB locale, etc.)
    n'est pas une erreur si le profil le permet.
-   Un serveur ne peut pas être déclaré READY si un finding bloquant
    subsiste.

## Scénarios minimum

1.  Clean supported install.
2.  Unsupported OS blocked.
3.  Existing conflicting panel detected.
4.  Install interrupted and resumed.
5.  First-run concurrent admin session.
6.  Hostname/PTR mismatch warning.
7.  NAT + public IP mapping.
8.  IPv6 not mail-ready.
9.  Firewall self-lockout prevented.
10. External DNS mode.
11. Web-only profile without mail.
12. Full mail + Rspamd + DKIM/SPF/DMARC.
13. RBL-listed outbound IP.
14. Compromised mailbox throttled.
15. Quarantine tenant isolation.
16. Backup provider test failure.
17. Readiness score high but blocker present.
18. Repair drifted Nginx config.
19. Repair timeout/reconcile.
20. UI broken → rescue mode.
21. Admin MFA lockout recovery.
22. Control-plane restore to fresh install.
23. cPanel/Plesk/DirectAdmin/Hestia discovery adapter unsupported field.
24. Migration cutover with DNS propagation.
25. Mail delta sync/cutover.
26. Reconfigure mail local→external.
27. Offline signed install.
28. Proxy credential redaction.
29. Disk full emergency.
30. Inode exhaustion.
31. OOM/control-plane degradation.
32. Read-only filesystem.
33. Broken resolver/NTP/panel certificate.
34. Package DB corruption.
35. Uninstall without customer-data deletion.
36. Automated bootstrap uses same validation as GUI.
