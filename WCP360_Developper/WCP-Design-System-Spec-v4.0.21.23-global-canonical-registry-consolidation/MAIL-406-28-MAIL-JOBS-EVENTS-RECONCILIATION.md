# V4.0.6 --- Mail Jobs, Events & Reconciliation

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

## Jobs

maildomain.provision/suspend/delete,
mailbox.create/resize/suspend/delete, credential.rotate, alias.apply,
sieve.apply, dkim.rotate, queue.action, mailconfig.apply, migration.run.

## Events

Facts après vérification : mailbox.created, quota.changed,
dkim.activated, maildomain.degraded, queue.threshold_exceeded, etc.

## Reconcile

Control-plane ↔ Postfix maps ↔ Dovecot/auth ↔ filesystem/storage ↔ DNS ↔
TLS bindings.

## Timeout

Après action ambiguë, lire provider/service state avant retry.

## Locks

Domain/account/mailbox locks plus shared mail-config lock lorsque
génération globale nécessaire.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
