# V4.0.21.10 --- Composer / npm / pip / Bundler Dependency Security

## Managers

Composer, npm/yarn/pnpm, pip, Bundler sont des adapters non privilégiés.

## Contrats

lockfile detection, immutable/frozen install option, registry/index
allow policy, network egress policy, timeout, disk budget, process
budget, cache ownership, audit metadata, dependency vulnerability scan
integration.

## Secrets

Registry credentials sont injectés via SecretStore/runtime-safe
mechanism, jamais ajoutés durablement à un repository ou ligne de
commande visible.

## Scripts

Package-manager lifecycle scripts sont une surface de code arbitraire du
projet : ils s'exécutent uniquement sous l'identité isolée du compte,
jamais via le daemon root, avec limites CPU/RAM/PIDs/time/network
définies.
