# V4.0.6 --- Mail Health, Metrics & Degraded Modes

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

## Health domains

MTA, submission/auth, Dovecot, storage/quota, anti-spam, antivirus, DNS
auth, TLS, queue et outbound reputation signals.

## Aggregation

`healthy/degraded/critical/unknown` conserve les causes. Rspamd/ClamAV
down ne doit pas disparaître derrière Postfix active.

## Metrics

Accepted/rejected/deferred, auth failures, queue size/age, delivery
latency, spam/virus counts, mailbox storage, outbound rate/bounces.

## Privacy/cardinality

Pas de message body ; adresses hash/redact selon use case ; limiter
labels par mailbox/domain si nécessaire.

## V4.0.10 normative deepening

- `OPS-4010-01-OBSERVABILITY-CANONICAL-MODEL.md`
- `OPS-4010-03-METRICS-MODEL.md`
- `OPS-4010-06-LOGGING-CANONICAL.md`
- `OPS-4010-02-HEALTH-STATE-MODEL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
