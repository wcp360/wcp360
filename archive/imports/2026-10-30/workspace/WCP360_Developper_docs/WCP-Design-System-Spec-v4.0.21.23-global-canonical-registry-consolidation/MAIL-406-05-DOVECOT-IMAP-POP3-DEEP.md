# V4.0.6 --- Dovecot IMAP/POP3 & Mail Delivery Contract

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

## Protocoles

IMAP/IMAPS baseline ; POP3 optionnel selon policy. TLS requis pour auth
distante.

## Namespace/storage

Maildir ou backend certifié défini par ADR/profile. Paths issus du
Mailbox ID/account mapping, jamais d'un path tenant arbitraire.

## Auth

Mailbox principal → credential backend → account status/quota/policy.
Root/system accounts exclus.

## Delivery

LMTP/LDA selon architecture certifiée. MTA et Dovecot partagent un
contrat d'adresse/recipient sans dupliquer la source de vérité.

## Sessions

Observer connexions sans exposer contenu. Kill session est action
séparée, scopée et auditable.

## Health

Service + auth probe + mailbox access probe contrôlé ; `active` seul ne
suffit pas.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
