# V4.0.6 --- ARC, MTA-STS & TLS-RPT Advanced Deliverability

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

## ARC

Capability avancée pour forwarders/intermediaries ; clés/signing
lifecycle séparé de DKIM si activé.

## MTA-STS

Policy HTTPS + DNS TXT `_mta-sts`; hostname/certificat valides, version
policy et cache implications.

## TLS-RPT

TXT `_smtp._tls` et reporting URI avec validation/privacy.

## Deployment

Ces mécanismes sont optionnels/capability-gated et ne doivent pas être
affichés comme indispensables à tout domaine.

## Failure

Policy HTTPS inaccessible, certificat invalide ou MX non conforme peut
casser le strict mode ; preflight obligatoire.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
