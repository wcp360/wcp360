# V4.0.6 --- Mail DNS Bootstrap: MX, SPF, DKIM, DMARC & Client Records

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

## Bootstrap

Pour un MailDomain : MX, mail host A/AAAA, SPF, DKIM, DMARC
recommendation/managed records, autoconfig/autodiscover records selon
architecture.

## Ownership

Chaque record porte owner/module. Ne pas écraser custom record non
possédé.

## Dependencies

MX cible doit résoudre ; DKIM attend observation avant activation selon
policy ; DMARC readiness dépend SPF/DKIM alignment.

## External DNS

Produire exact desired records + status observed. Copy UX ne transforme
pas le record en managed.

## Verification

Authoritative query, public resolver optional evidence, mail listener
probe et alignment diagnostics.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
