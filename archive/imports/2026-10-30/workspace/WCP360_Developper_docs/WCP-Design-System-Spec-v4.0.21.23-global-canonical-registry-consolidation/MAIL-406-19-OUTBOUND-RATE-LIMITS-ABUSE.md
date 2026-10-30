# V4.0.6 --- Outbound Rate Limits, Abuse & Compromise Response

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

## Dimensions

Messages/time, recipients/time, concurrent SMTP, bytes/time, per
mailbox/account/domain/IP.

## Enforcement

Policy hierarchy admin → reseller → plan → account/mailbox. Valeurs
effectives et origine visibles.

## Abuse

Spike detection, auth failures, unusual fan-out, repeated bounces et
malware signal peuvent déclencher throttle/suspend outbound selon
policy.

## Compromise response

Suspend submission/outbound, preserve inbound/mailbox access si policy,
revoke credentials, inspect evidence, rotate secret, gradual restore.

## No silent punishment

Toute suspension automatique a reason/evidence/audit et mécanisme
opérateur.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
