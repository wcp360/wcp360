# V4.0.6 --- Roundcube / SnappyMail Integration

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

## Nature

Webmail est une application exposée et patchée séparément du MTA/MDA.

## SSO

Session bootstrap éphémère si intégration certifiée ; aucun password
dans URL, logs ou navigateur.

## Scope

L'identité webmail reste une mailbox réelle et les permissions backend
restent autoritatives.

## Security

CSRF, session fixation, same-site cookies, CSP/headers, brute force/rate
limits, plugin allowlist et update lifecycle.

## Plugins

Pas d'installation arbitraire tenant. Plugins certifiés/versionnés
uniquement.

## Logout

Révoquer session webmail et bootstrap token ; impersonation doit rester
visible/auditable.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
