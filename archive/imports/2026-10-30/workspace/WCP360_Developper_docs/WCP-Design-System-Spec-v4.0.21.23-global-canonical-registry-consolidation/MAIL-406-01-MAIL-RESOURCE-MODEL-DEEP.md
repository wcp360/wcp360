# V4.0.6 --- Mail Resource Model --- Deep Contract

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

## Graphe

`MailDomain` appartient à un HostingAccount et référence un Domain. Il
contient Mailboxes, Aliases/Forwarders, CatchAllPolicy, DKIM bindings,
filtering policy et service bindings.

## Mailbox

Adresse canonique, local-part normalisé, storage reference, quota, login
policy, credential reference, status et usage observé.

## Alias / Forwarder

Source address → une ou plusieurs destinations validées. Alias local,
forward externe et catch-all sont distingués.

## Etats

provisioning, active, suspended, over_quota, degraded, deleting,
deleted, unknown.

## Ownership

L'adresse n'est pas l'identité interne. Les IDs WCP restent stables lors
d'un changement de display name ou migration de backend.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
