# V4.0.21.5 — CLI Option & Safety Contract

## Statut
**NORMATIVE — V1 STANDALONE — NO CODE — REPOSITORY BLOCKED**

Cette famille transforme les options d’isolation et de commandes de V4.0.21.4.1 en contrats B1/B2 explicites.

## Options globales
`--output table|json|yaml`, `--quiet`, `--verbose`, `--request-id`, `--timeout`, `--wait|--no-wait`, `--dry-run`, `--confirm`, `--reason`, `--scope`, `--account`, `--reseller`, `--idempotency-key`.

## Règles
- les sélecteurs sont des IDs/aliases résolus côté control-plane ;
- les secrets sont lus via prompt sécurisé/stdin secret reference, jamais argument visible ;
- aucune option `--command`, `--shell`, `--exec-root` ;
- `--force` n’est pas générique : seules des opérations explicitement conçues peuvent avoir une variante de confirmation renforcée ;
- JSON/YAML output ne révèle jamais secrets/tokens ;
- destructive actions exigent reason + confirmation/step-up selon policy ;
- automation utilise les mêmes Operation IDs et ne bénéficie pas d’un bypass interactif.

## Exit classes
`0 SUCCESS`, `2 VALIDATION`, `3 AUTHN`, `4 AUTHZ`, `5 CONFLICT`, `6 PRECONDITION`, `7 NOT_AVAILABLE`, `8 JOB_FAILED`, `9 PARTIAL/DEGRADED`, `10 INTERNAL`. Les détails machine-readable viennent du contrat d’erreur API.
