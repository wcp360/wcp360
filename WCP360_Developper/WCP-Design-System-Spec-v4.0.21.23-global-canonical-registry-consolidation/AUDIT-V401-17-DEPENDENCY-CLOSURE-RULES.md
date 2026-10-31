# V4.0.1 --- Dependency Closure Rules

Une famille devient `READY` seulement si ses dépendances V1 obligatoires
sont READY ou exposent un contrat stable.

Types : hard runtime, capability, adapter, policy, optional integration,
future.

Chaque module doit déclarer : `Depends on`, `Provides`,
`Consumes events`, `Emits events`, `Privileged actions`, `Secrets`,
`Ports/sockets`, `Data ownership`, `Backup scope`, `Observability`,
`Failure dependencies`.
