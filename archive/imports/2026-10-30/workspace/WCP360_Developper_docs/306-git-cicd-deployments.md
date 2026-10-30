# 306 --- Git & CI/CD Deployments

## Origine

**\[V3.1\]** Git, webhooks, auto-pull et déploiement sur push.

## Pages

Repositories; Deploy Keys; Branches; Deployments; Webhooks;
Pipeline/Steps; Releases; Rollback; Logs.

## Workflow

Push/webhook → validation → checkout/release → dependencies/build →
pre-deploy → switch release → post-deploy → health check →
success/rollback selon politique.

## Actions

Connect repository; select branch; manual deploy; enable auto-deploy;
regenerate webhook secret; rollback release.

## Sécurité

Deploy keys/secrets masqués; webhook signé; aucune commande arbitraire
non gouvernée; logs redacted; permissions par application.

## WCP-USER

Le client/revendeur ne déploie que dans ses applications et domaines.

## États UI obligatoires

Loading, skeleton, empty, no-results, permission denied, partial data,
stale data, unavailable, maintenance, pending, failed et success
confirmé par le backend.

## Règles transversales

-   WCP-ADMIN administre la capacité globale, les services, politiques
    et limites.
-   WCP-USER n'accède qu'aux ressources et fonctions explicitement
    attribuées à son compte/revendeur.
-   Toute mutation sensible est autorisée côté backend, auditée et
    reliée à un correlation/job ID.
-   Toute opération longue ou massive passe par Task Center.
-   Les secrets ne sont jamais écrits dans les logs, notifications,
    audit ou sorties IA.
-   Les modules sont pilotés par capabilities : une technologie non
    installée n'est pas présentée comme disponible.
