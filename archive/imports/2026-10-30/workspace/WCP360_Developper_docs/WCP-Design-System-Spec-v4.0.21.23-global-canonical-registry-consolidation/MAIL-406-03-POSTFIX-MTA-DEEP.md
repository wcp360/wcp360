# V4.0.6 --- Postfix / MTA Deep Contract

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

## Rôles

Inbound MX, local delivery handoff, authenticated submission et outbound
delivery. Aucun rôle ne doit implicitement créer un open relay.

## Configuration

WCP projette des maps/policies structurées via adapter. Pas de
`main.cf`/`master.cf` textarea WCP-USER.

## Relay policy

Relay autorisé uniquement pour local domains, authenticated submission
ou explicit trusted infrastructure. Réseaux de confiance minimaux.

## Routing

Virtual domains/mailboxes/aliases, transport maps futures et recipient
validation sont des ressources contrôlées.

## Reload

Render → syntax/semantic checks → stage → reload → SMTP probes → queue
observation.

## Security

VRFY/EXPN policy, banner/info minimization, command limits, connection
limits et anti-abuse profiles.

## V4.0.14 normative deepening

- `BOOT-4014-12-MAIL-STACK-BOOTSTRAP.md`
- `BOOT-4014-13-MAIL-SECURITY-ANTISPAM.md`

Ces contrats approfondissent le bootstrap, la finalisation ou le lifecycle opérationnel correspondant ; les exigences historiques compatibles restent applicables.
