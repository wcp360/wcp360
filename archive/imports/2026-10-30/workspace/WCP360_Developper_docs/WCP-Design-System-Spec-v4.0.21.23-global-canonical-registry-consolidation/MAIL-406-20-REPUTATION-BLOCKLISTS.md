# V4.0.6 --- IP/Domain Reputation & Blocklist Observability

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

## Principe

WCP observe des signaux ; il ne peut garantir inbox placement ni
réputation.

## Signals

PTR/FCrDNS, SPF/DKIM/DMARC, queue/bounce rates, complaint signals
disponibles, selected public/provider reputation sources via adapters
futurs.

## Blocklists

Listings sont evidence externe avec timestamp/provider ; absence de
listing ≠ bonne réputation.

## Shared IP

Attribution tenant et blast radius documentés. Un tenant compromis peut
affecter tous les domaines partageant l'IP.

## Remediation

Checklist factuelle : stop abuse, fix auth/DNS, clean queue, rotate
credentials, request delisting via provider workflows si disponibles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
