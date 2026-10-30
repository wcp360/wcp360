# 03 --- Modèle produit et taxonomie des ressources

## Objectif

Éviter que chaque module invente son propre vocabulaire et son propre
cycle de vie.

## Ressources de premier niveau

### WCP-USER \[SOURCE\]

Compte, hébergement, domaine, zone DNS, boîte mail, alias, filtre,
fichier, sauvegarde, base de données, utilisateur DB, certificat, clé
SSH, token API, application, tâche Cron.

### WCP-ADMIN \[SOURCE\]

Serveur, nœud, service, compte, revendeur, package, IP, zone DNS,
certificat, règle firewall/WAF, processus, tâche, sauvegarde, backend de
stockage, cluster, réseau, VM, conteneur, mission IA.

## Identité d'une ressource \[V2\]

Toute ressource possède si applicable : `id`, `display_name`, `type`,
`scope`, `owner`, `status`, `health`, `created_at`, `updated_at`,
`capabilities`, `labels`, `risk_level`.

## Cycle de vie standard

`provisioning → active → degraded → suspended/maintenance → deleting → deleted`
Les ressources qui ne suivent pas ce cycle documentent explicitement
leur propre machine d'états.

## Règle UI

Liste → page ressource → sous-vues. Le nom, le scope, le statut et les
actions principales restent prévisibles d'un module à l'autre.

## Interdits

Ne pas confondre `status` (cycle de vie), `health` (santé), `severity`
(gravité d'un événement) et `job_state` (état d'une opération).
