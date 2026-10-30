# V4.0.6 --- Mail RBAC, Impersonation & Audit

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

## Permissions

mail.domain.*, mailbox.*, alias.*, sieve.*, queue.*, deliverability.*,
dkim.*, webmail.*, mail.service.\*.

## WCP-USER

Aucun accès global queue/service/config, aucune clé DKIM privée, aucune
mailbox d'un autre account.

## Impersonation

Support peut administrer settings autorisés avec real/effective actor.
Accès au contenu d'une boîte n'est pas implicitement accordé par
impersonation et exige une politique distincte si jamais prévu.

## Audit

Config diffs, address/resource IDs, reason, Job, result ; jamais
passwords, private keys ou message bodies.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
