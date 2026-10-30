# Audit Markdown / Contrats / Spécifications

## 1. Structure documentaire

Le dossier courant contient 88 fichiers `.md`.

Le système de numérotation n’est plus unique.

Exemple :
- cinq fichiers différents commencent par `01-`;
- cinq par `02-`;
- cinq par `03-`;
- cinq par `04-`;
- cinq par `05-`;
- cinq par `06-`.

La raison est que chaque nouveau batch a été copié dans le même dossier racine.

### Risque
Un développeur ne peut pas savoir si `04-api-contract-design.md`, `04-canonical-state-machines.md` ou `04-action-permission-mapping.md` appartient à la même séquence documentaire.

### Recommandation
Créer une structure canonique par domaine, sans perdre les fichiers historiques :

```text
/specs/
  design/
  security/
  permissions/
  jobs/
  api/
  daemon/
  system/
  installation/
  adapters/
  accessibility/
  audit/
/archive/
  v1/
  v2/
```

## 2. Divergence du namespace de permissions — HIGH

### Permissions utilisées dans `04-action-permission-mapping.md` mais absentes du namespace canonique

- `applications.runtime.manage`
- `backups.delete`
- `backups.policy.manage`
- `databases.delete`
- `databases.export`
- `databases.grants.manage`
- `databases.import`
- `domains.create`
- `email.aliases.manage`
- `email.antispam.manage`
- `email.domains.manage`
- `email.mailboxes.manage`
- `files.access.manage`
- `files.delete`
- `iam.account_overrides.manage`
- `iam.features.manage`
- `security.network.manage`

### Permissions canoniques non reprises dans le mapping d’actions

- `api.tokens.manage`
- `approvals.decide`
- `approvals.request`
- `audit.export`
- `audit.read`
- `automations.manage`
- `clusters.read`
- `email.manage`
- `incidents.manage`
- `jobs.cancel`
- `jobs.read`
- `monitoring.read`
- `network.read`
- `notifications.manage`
- `processes.read`
- `service_config.read`
- `settings.profile.manage`
- `ssl.manage`
- `ssl.read`
- `storage.read`
- `support.bundle.create`
- `updates.read`
- `webhooks.manage`

### Décision recommandée
Créer **un seul fichier source canonique** de permissions, puis générer les matrices dérivées à partir de lui lors de la future phase d’implémentation.

## 3. State machine Job divergente — HIGH

`04-canonical-state-machines.md` définit :
- QUEUED
- WAITING_DEPENDENCY
- RUNNING
- PAUSED
- RETRYING
- SUCCEEDED
- FAILED
- CANCELLED
- UNKNOWN_OUTCOME

`02-job-task-contract.md` ajoute :
- BLOCKED

`BLOCKED` doit donc :
- soit être ajouté au canon ;
- soit être remplacé par `WAITING_DEPENDENCY` / un reason code ;
- mais ne doit pas rester dans un état intermédiaire ambigu.

## 4. Hiérarchie source-of-truth insuffisante

Plusieurs documents approfondissent le même sujet :
- permissions ;
- daemon operations ;
- job states ;
- filesystem boundaries ;
- service config.

Cela est normal dans l’historique, mais les documents les plus récents ne déclarent pas systématiquement :
- “supersedes” ;
- “extends” ;
- “canonical source”.

Il faut ajouter une **Document Authority Map**.

## 5. Daemon

Très bon niveau de sécurité conceptuelle :
- pas de shell arbitraire ;
- pas de systemd unit arbitraire ;
- pas de SQL arbitraire ;
- pas de filesystem path arbitraire ;
- allowlist explicite ;
- operation IDs ;
- jobs + audit.

43 opérations daemon identifiées dans l’allowlist actuelle.

### Reste à détailler
Comme le reconnaît déjà le corpus :
- I/O exacts ;
- types/champs ;
- timeouts ;
- codes d’erreur ;
- support OS par opération ;
- contraintes idempotency par opération.

## 6. Filesystem

Les documents récents sont cohérents :
- `/usr/local/wcp360`
- `/etc/wcp360`
- `/var/lib/wcp360`
- `/run/wcp360`
- `/var/log/wcp360`
- `/var/cache/wcp360`
- `/var/tmp/wcp360`
- `/srv/wcp360`

Aucun ancien root `/opt/wcp...` ou `/var/www...` n’a été détecté comme architecture active.

C’est un point positif.

## 7. V4.0 / V4.1

Le corpus récent respecte bien :
- V4.0 active ;
- V4.1 Executable Contracts annulée/non réactivée ;
- code production non autorisé ;
- repository skeleton non autorisé.

Le fichier `12-next-phase-gate.md` mentionne V4.1 dans une phrase de non-réactivation : ce n’est pas une contradiction réelle.

## 8. Terminologie

Aucune occurrence détectée de :
- `WC-CLIENT`
- `WC-ADMIN`

La terminologie WCP-USER / WCP-ADMIN est donc correctement conservée dans le corpus récent.
