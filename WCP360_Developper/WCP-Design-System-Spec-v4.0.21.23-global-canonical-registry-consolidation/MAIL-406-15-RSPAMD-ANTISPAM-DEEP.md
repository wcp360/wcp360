# V4.0.6 --- Rspamd / Anti-spam Deep Contract

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

## Baseline

Rspamd recommandé si ADR existant le confirme ; SpamAssassin peut rester
adapter alternatif selon corpus.

## Pipeline

Message metadata/content → scoring/modules → action profile : no action,
add header, greylist/tempfail, quarantine/reject selon policy.

## Tenant

WCP-USER peut gérer allow/block lists et sensibilité dans bornes
autorisées, jamais des expressions/code root arbitraires.

## Learning

Bayes/learn actions sont scopées et rate-limited ; poisoning/abuse
considéré.

## Logs

Ne pas exposer body complet par défaut. Scores/symbols et evidence
redacted.

## Updates

Rules/config lifecycle avec validation et rollback.

## V4.0.14 normative deepening

- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`
- `BOOT-4014-14-MAIL-ABUSE-REPUTATION.md`
- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
