# V4.0.6 --- Mail & Deliverability Closure Audit

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

## Base auditée

La passe a été construite à partir des fichiers Mail réellement présents
dans le corpus V4.0.5, notamment `150-mail-stack.md`, `151-postfix.md`,
`153-dovecot.md`, `154-mail-queue.md`,
`155-mail-deliverability-auth.md`, `156-mail-antispam-relay.md`, les
fichiers 372--375, 400--404, 510--515, 760--765 et
`ADR-012-mail-stack-baseline.md`.

## Résultat

30 contrats normatifs Mail approfondis ont été ajoutés et **27 surfaces
historiques confirmées** ont été reliées.

## Fermeture SPECIFIED-DEEP

Resource model, domain/mailbox lifecycle, Postfix, submission/SASL,
Dovecot, quotas/storage, aliases/forwarding/catch-all, Sieve/vacation,
webmail, autoconfig, DKIM/SPF/DMARC, ARC/MTA-STS/TLS-RPT, Rspamd,
ClamAV, queue/bounces, outbound abuse/rate limits, reputation,
brute-force, TLS mail, DNS bootstrap, backup/restore, migration,
observability, RBAC/audit, jobs/reconciliation et E2E.

## Décisions restant à fermer avant GLOBAL READY

Choix exacts par OS/version des packages/providers, backend de stockage
mail définitif si ADR non conclusif, politique finale POP3, détails de
quarantaine, providers réputation externes et exigences
légales/rétention selon déploiement.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
