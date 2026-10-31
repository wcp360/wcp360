# MASTER CANONICAL SOURCE MAP

## But

Éviter plusieurs sources de vérité concurrentes.

  ---------------------------------------------------------------------------------------------------------------------------
  Sujet             Document canonique                           Documents complémentaires      Décision
  ----------------- -------------------------------------------- ------------------------------ -----------------------------
  Backup            `531-backup-destinations.md`                 `212-backup-destinations.md`   531 = contrat
  destinations                                                                                  architecture/adapters ; 212 =
                                                                                                surface UI historique

  Backup            `530-backup-engine-architecture.md`          `210–214`, `420–422`,          530 porte le modèle
  architecture                                                   `531–533`                      moteur/destination/recovery

  SSH server        `550-ssh-server-manager.md`                  `190`, `551–554`, `194`        190 devient overview d'accès

  Telemetry         `520-telemetry-architecture.md`              `65`, `260`, `521–525`         520 = modèle/pipeline ; 65 =
                                                                                                health model ; 260 = UX
                                                                                                alerting

  API-first         `368-api-first-rest-contract.md`             `80`, `270`, `369`, `570–572`  368 = règle de parité métier

  Secrets           `37-secrets-credentials-sensitive-data.md`   `202`, `270`, `560`, `570`     37 = règles UI ; Secret Store
                                                                                                interne défini en V3.5

  Cluster           `230-cluster-manager.md`                     `231–232`                      couvre ressources gérées, pas
                                                                                                HA du control plane WCP

  WCP control plane `630-wcp-control-plane-architecture.md`      `631–641`                      nouvelle source canonique
                                                                                                V3.5
  ---------------------------------------------------------------------------------------------------------------------------

## Convention

Chaque futur document doit déclarer `Canonique`, `Sous-spécification`,
`Overview`, `Adapter`, ou `Historique`.
