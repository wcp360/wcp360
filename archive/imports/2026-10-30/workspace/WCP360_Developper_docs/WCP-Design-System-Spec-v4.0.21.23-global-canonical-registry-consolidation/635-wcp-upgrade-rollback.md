# 635 --- WCP Upgrade / Rollback

## Statut de provenance

**\[V3.5 --- architecture proposée à partir des lacunes de la V3.4\]**
Ce contrat n'était pas spécifié en profondeur dans les documents
précédents. Il doit être validé par l'architecture technique avant
implémentation.

## Workflow

Check release/signature → compatibility/preflight → backup control-plane
state → drain/maintenance as needed → migrate → deploy → health checks →
commit.

## Rolling upgrade

Uniquement si versions N/N+1 et schéma/protocoles sont déclarés
compatibles.

## Rollback

Distinguer rollback binaire, rollback configuration et rollback données.
Aucun bouton `Rollback` générique si les trois ne sont pas sûrs.

## V4.0.12 normative deepening

- `LIFE-4012-16-ROLLBACK-RECOVERY-BOUNDARY.md`
- `LIFE-4012-06-SINGLE-NODE-UPGRADE.md`
- `LIFE-4012-07-CLUSTER-ROLLING-UPGRADE.md`

Ces contrats approfondissent cette surface ; les exigences historiques compatibles restent applicables sauf contradiction explicitement arbitrée par ADR.
