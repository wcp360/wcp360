# V4.0.21.13 --- Dependency Edge Registry

Chaque dépendance devient une arête typée : `REQUIRES`,
`OPTIONALLY_USES`, `PROVIDES_CAPABILITY_TO`, `OBSERVES`, `BACKS_UP`,
`RESTORES`, `SECURES`, `AUTHORIZES`, `LIMITS`.

Une arête porte source module, target module/resource, required state,
failure behavior and source document.

## Gate

Les cycles `REQUIRES` sont interdits sauf cycle de bootstrap
explicitement résolu par phase installer. Les dépendances optionnelles
ne doivent pas rendre le module principal faussement unhealthy.
