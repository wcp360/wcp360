# V4.0.6 --- Aliases, Forwarders & Catch-all Deep Contract

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

## Types

Local alias, multi-destination alias, external forward, role address et
catch-all.

## Validation

Boucles directes/indirectes, self-forward, excessive fan-out,
destination syntax et cross-tenant ownership.

## Catch-all

Désactivé par défaut recommandé ; impact spam/backscatter expliqué. Une
seule policy effective par MailDomain.

## External forwarding

Risques SPF/DMARC et réputation ; policy peut imposer SRS capability si
supportée.

## Delete

Supprimer mailbox avec aliases entrants nécessite impact preview ;
dangling aliases deviennent invalid/degraded.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
