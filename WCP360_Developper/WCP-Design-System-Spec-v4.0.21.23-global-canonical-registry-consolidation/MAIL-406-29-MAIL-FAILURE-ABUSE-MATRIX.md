# V4.0.6 --- Mail Failure & Abuse-case Matrix

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

## Cas obligatoires

Open relay regression, auth bypass, sender spoof cross-tenant, mailbox
path escape, quota full, host disk full, Postfix reload failure, Dovecot
auth down, DKIM DNS stale, DKIM private key permission error, SPF
multiple records, DMARC malformed, MTA-STS bad cert, ClamAV down, Rspamd
down, queue explosion, bounce storm, compromised mailbox, forwarding
loop, catch-all spam flood, webmail SSO replay, brute-force, cert
expired, migration interrupted, backup partial.

## Pour chaque cas

Trigger/evidence → impacted resources → allowed/blocked actions → Job
state → retry/reconcile → notification → audit → remediation →
acceptance test.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
