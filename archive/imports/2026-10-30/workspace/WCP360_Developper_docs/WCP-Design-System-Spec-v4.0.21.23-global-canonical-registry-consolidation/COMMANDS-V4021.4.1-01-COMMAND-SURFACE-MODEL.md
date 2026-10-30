# V4.0.21.4.1 — Command Surface Model

## Architecture
`UI / REST / wcpctl / billing / automation → Operation ID → AuthZ + Entitlement → Job/NO_JOB → Action Registry → RPC/Adapter → verify → audit`.

`wcpd` est un daemon privilégié, jamais une CLI publique. `wcp-installer` reste limité au bootstrap/install/finalisation.

## Syntaxe CLI
`wcpctl <resource> <verb> [selector] [options]`

Options communes à prévoir : `--output table|json|yaml`, `--quiet`, `--verbose`, `--request-id`, `--idempotency-key`, `--timeout`, `--wait|--no-wait`, `--dry-run`, `--plan`, `--confirm`, `--reason`, `--scope`, `--account`, `--reseller`.

Aucun argument utilisateur ne devient une commande shell, un chemin root arbitraire, un nom d'unité systemd arbitraire ou une liste d'arguments root.
