# V4.0.6 --- Mail Backup, Restore & Consistency

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

## Contenu

Mailbox messages/metadata selon backend, Sieve, aliases/forwarders,
quotas/config manifests et DKIM private keys uniquement via secret
backup policy.

## Consistency

Méthode backend-aware ; live copy doit documenter consistency. Indexes
Dovecot peuvent être reconstruits si policy.

## Restore

Mailbox-only, domain-level ou full account ; target mapping, ownership,
quota, UID/storage refs et conflict policy.

## Verification

Message counts/samples/checksums lorsque possible, mailbox login/access
et index rebuild status.

## Security

Backups chiffrés selon global backup contract ; contenu mail est
hautement sensible.

## V4.0.9 normative deepening

- `API-409-01-API-FIRST-CANONICAL-CONTRACT.md`
- `API-409-02-API-AUTH-SERVICE-ACCOUNTS.md`
- `API-409-05-IDEMPOTENCY-MUTATIONS.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`
- `BOOT-4014-18-BACKUP-BOOTSTRAP.md`
- `BOOT-4014-26-CONTROL-PLANE-RESTORE.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
