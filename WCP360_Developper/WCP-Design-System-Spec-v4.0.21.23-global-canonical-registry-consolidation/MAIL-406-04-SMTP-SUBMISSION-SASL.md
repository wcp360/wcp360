# V4.0.6 --- SMTP Submission, SASL & Client Authentication

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

## Endpoints

Port 587 submission baseline ; 465 implicit TLS optionnel/certifié. Port
25 n'est pas l'endpoint client authentifié par défaut.

## Auth

SASL via Dovecot/auth provider certifié. PLAIN/LOGIN uniquement sous TLS
selon policy. Credentials séparés de mailbox resource.

## Authorization

Authenticated identity ne peut envoyer arbitrairement comme un autre
tenant. Sender-login maps/policies vérifient les identities autorisées.

## Limits

Rate, concurrent connections, recipients/message, message size et
daily/hourly thresholds par account/mailbox/plan.

## Failure

Brute force, credential stuffing, auth backend down et account suspended
produisent des erreurs distinctes et auditables sans password.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
