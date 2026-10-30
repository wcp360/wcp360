# V4.0.6 --- Mail End-to-End Acceptance & READY Gate

## Statut

**NORMATIVE --- V4.0.6 MAIL & DELIVERABILITY DEEP SPECIFICATION --- NO
CODE**

Cette passe approfondit la famille Mail existante du corpus V4.0.5. Elle
conserve les décisions antérieures et les rend suffisamment précises
pour les futurs contrats d'implémentation.

## Invariants communs

-   `MailDomain`, `Mailbox`, `Alias`, `Forwarder`, `MailCredential`,
    `MessageQueueItem`, `DkimKey`, `DeliverabilityPolicy` sont des
    ressources distinctes.
-   WCP-USER ne reçoit jamais de primitive MTA/MDA root, de fichier de
    configuration libre, de relay ouvert ni d'accès inter-tenant.
-   SMTP entrant, SMTP submission authentifié et relay sortant sont des
    rôles distincts.
-   Une configuration DNS publiée n'est pas considérée effective avant
    observation.
-   Aucun secret SMTP/IMAP, clé DKIM privée, contenu de message ou token
    webmail ne doit apparaître dans audit/events.
-   Les opérations suivent desired/observed state, Job, verify,
    reconciliation et evidence des fondations V4.0.2.

## E2E

1.  MailDomain bootstrap with managed DNS.
2.  External DNS bootstrap.
3.  Mailbox create/login/send/receive.
4.  Alias and external forward.
5.  Catch-all disabled/enabled policy.
6.  Sieve vacation loop-safe.
7.  DKIM enable and rotation.
8.  SPF/DMARC observation.
9.  ACME certificate deploy to SMTP/IMAP.
10. Submission auth sender authorization.
11. Spam classification.
12. Virus scanner unavailable.
13. Queue deferred/retry/delete scoped.
14. Mailbox over quota.
15. Host disk full.
16. Account outbound compromise suspension/recovery.
17. Webmail SSO.
18. Autoconfig fetch.
19. Backup/restore mailbox.
20. IMAP migration with delta/cutover.
21. Cross-tenant negative tests.
22. Worker crash during mailbox create.
23. MTA reload failure/rollback.
24. DNSSEC/DNS outage affecting deliverability diagnostics.
25. Shared-IP reputation incident.

## Gate

Mail devient **SPECIFIED-DEEP** lorsque toutes les surfaces historiques
sont reliées et que les invariants/negative tests sont cohérents. Cela
ne signifie pas encore GLOBAL READY pour l'ensemble WCP.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
