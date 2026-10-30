# V4.0.6 --- Mail Domain Lifecycle & Bootstrap

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

## Provision

Scope/entitlement → Domain ownership → mail engine capability → create
MailDomain → storage namespace → routing tables → DKIM key → DNS
recommendations/managed records → probes → active/degraded.

## External DNS

Le mail peut fonctionner avec DNS externe. WCP affiche les
enregistrements attendus et observe leur publication sans les prétendre
gérés.

## Suspension

Distinguer réception, livraison locale, login IMAP, submission et
outbound. La policy décide quels flux restent permis.

## Delete

Impact preview sur mailboxes, aliases, queue, DKIM, DNS, backups et
webmail sessions. Delete logique et purge storage sont séparés.

## Move

Migration inter-account exige ownership, quotas, storage, credentials,
aliases, DNS/DKIM et audit.

## V4.0.12 normative deepening

- `LIFE-4012-15-DEPRECATION-EOL.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
