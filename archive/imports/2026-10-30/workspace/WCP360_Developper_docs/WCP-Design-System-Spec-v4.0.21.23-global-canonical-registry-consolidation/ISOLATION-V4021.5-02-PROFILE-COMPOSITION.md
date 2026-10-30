# V4.0.21.5 — Isolation Profile Composition & Effective Limits

## Statut
**NORMATIVE — V1 STANDALONE — NO CODE — REPOSITORY BLOCKED**

Cette famille transforme les options d’isolation et de commandes de V4.0.21.4.1 en contrats B1/B2 explicites.

## Résolution
`Platform ceiling → Reseller ceiling → Plan → Account override → Resource override autorisé → IsolationProfile → Backend capability → Security floor`.

### Opérateurs
- numérique : minimum des plafonds applicables ;
- enum/set : intersection ;
- booléen : DENY non-overridable gagne ;
- backend absent : `UNAVAILABLE`, jamais faux `HARD` ;
- security floor : priorité absolue.

## États d’enforcement
`HARD`, `SOFT`, `INHERITED`, `UNLIMITED_WITHIN_PARENT`, `UNAVAILABLE`.

## Profils recommandés
- `shared-standard`: cgroups + quotas + UID/GID + runtime separation ;
- `shared-restricted`: shell disabled, limites plus strictes ;
- `developer`: Git/runtimes/SSH autorisables mais plafonnés ;
- `trial-restricted`: limites et anti-abuse renforcés ;
- `commercial-lve`: mapping optionnel vers LVE/CageFS si adapter certifié.

Ces profils sont des presets administratifs, pas des bypass de RBAC/entitlements.
