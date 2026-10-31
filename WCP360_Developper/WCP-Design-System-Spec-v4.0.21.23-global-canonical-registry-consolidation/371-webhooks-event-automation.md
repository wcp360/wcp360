# 371 --- Webhooks & Event Automation

## Origine

**\[V3.2\]** Automatisations déclenchées par événements.

## Event Bus logique

Exemples : disk.threshold.reached, backup.failed, account.suspended,
certificate.expiring, malware.detected, deployment.completed,
git.push.received.

## Actions

Webhook HTTP signé; notification Slack/Discord/email via connecteurs;
lancement d'un playbook autorisé; déclenchement de déploiement Git.

## Rule Builder

WHEN event + filters → optional conditions → actions. Preview du scope
et anti-loop protections.

## Sécurité

Webhook signing, secret rotation, retry policy, timeout, destination
validation/SSRF protection, rate limits. Les automatisations mutantes
nécessitent permissions et policy explicites.

## Contrat WCP

-   **WCP-ADMIN** configure moteurs, catalogues, politiques, capacités
    et limites.
-   **WCP-USER** expose uniquement les fonctions attribuées au
    client/revendeur.
-   Toute mutation sensible est vérifiée côté backend, auditée et
    corrélée à un job/événement.
-   Les fonctionnalités sont pilotées par `capabilities`; aucune
    intégration tierce n'est supposée installée.
-   États obligatoires : loading, empty, denied, unavailable, partial,
    stale, pending, failed, success confirmé.

## V4.0.9 normative deepening

- `API-409-09-WEBHOOK-OUTBOUND-MODEL.md`
- `API-409-10-WEBHOOK-SIGNATURE-REPLAY.md`
- `API-409-11-WEBHOOK-DELIVERY-RETRY-DLQ.md`
- `API-409-24-AUTOMATION-RULES-BOUNDARY.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables.
