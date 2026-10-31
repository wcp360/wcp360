# Sauvegardes et planification — 30 octobre 2026

Cette passe remplace les vues statiques de sauvegardes, de cron applicatif et de planification ROOT par des workflows locaux persistants. Le shell et les tokens de la baseline V2.5.3.12 sont conservés.

## Centres et catégories

| Centre | Périmètre | Responsabilité |
| --- | --- | --- |
| `backups.html` | USER | Points de restauration du compte, politique, destinations attribuées, tests isolés et activité |
| `cron.html` | USER | Commandes applicatives, prévisualisation des occurrences et historique du compte |
| `scheduled-tasks.html` | ROOT | Opérations de plateforme choisies dans un catalogue fermé, UTC et plafonds du cron USER |

Les onglets du centre de sauvegardes sont Overview, Restore points, Policy, Storage, Restore tests et Activity. Les tests isolés USER restent séparés de `restore-tests.html`, qui conserve sa responsabilité ROOT. Les infrastructures de sauvegarde restent dans `admin-backups.html` et les paramètres de fournisseurs ROOT.

Sept pages historiques sont conservées comme redirections :

| Ancienne route | Destination |
| --- | --- |
| `backup-policy.html` | `backups.html#policy` |
| `backup-storage.html` | `backups.html#storage` |
| `backup-activity.html` | `backups.html#activity` |
| `restore-points.html` | `backups.html#points` |
| `restore-wizard.html` | `backups.html?action=restore` |
| `restore-backup.html` | `backups.html?action=restore` |
| `restore-review.html` | `backups.html?action=restore` |

Les paramètres et fragments explicites sont conservés. Le formulaire de restauration consomme `action=restore` après ouverture et accepte `point` ou `id`. Un identifiant inexistant ou étranger est refusé ; il ne sélectionne pas silencieusement un autre point. Le registre de navigation central fournit ces destinations aux sidebars et à la recherche.

## Sauvegardes

- DataGrid commun pour les points, destinations, tests et opérations : recherche, filtres, tri, exports, pagination et préférences.
- Formulaires communs pour la création, la restauration, la politique de rétention et les destinations attribuées par ROOT. Aucun endpoint ou secret de fournisseur n’est saisi dans le portail USER.
- Un job de création doit se terminer avant l’apparition du point. Une annulation ou un échec ne crée aucun point. Le résultat est appliqué une seule fois, y compris après rechargement.
- La création peut simuler la vérification dans le même job. Un point non vérifié dispose aussi d’une action Verify séparée ; il ne devient restaurable qu’après la fin de ce job.
- Une restauration exige un point vérifié, non expiré, appartenant à `example.com`, ainsi que des ressources présentes dans ce point. Staging est la valeur initiale. Production exige la saisie exacte de `example.com`.
- Les tests de restauration utilisent une cible isolée et une confirmation. Leur résultat apparaît dans l’onglet USER dédié.
- La rétention immuable, une rétention légale ou un job actif empêchent de retirer un point. La suppression d’un enregistrement local demande `DELETE <identifiant>`.
- Les changements de politique s’appliquent aux futurs points. Les dates d’expiration et d’immutabilité des points existants sont conservées. Une destination principale ou encore utilisée ne peut pas être détachée.

État métier : `wcp360-backup-center-v1`. Les préférences des grilles sont isolées sous USER / `example.com`.

## Planification

Le même contrôleur et les mêmes schémas servent aux deux portails ; les données restent séparées sous `wcp360-account-cron-v1` et `wcp360-platform-schedules-v1`. L’ancienne définition statique du module Scheduled Tasks dans `hosting-parity.js` a été retirée.

- Création et édition persistantes, activation/pause confirmées, suppression avec confirmation saisie et historique conservé.
- Commandes USER enregistrées comme texte de démonstration. ROOT utilise uniquement les opérations du catalogue et le fuseau UTC.
- Expression cron à cinq champs numériques : minute, heure, jour, mois et jour de semaine. Listes, plages et pas sont acceptés. Dimanche vaut 0. Un seul des champs jour de mois / jour de semaine peut être contraint à la fois. Noms de mois, macros, secondes, `L`, `W` et `#` ne sont pas pris en charge.
- Validation des bornes, dates impossibles, noms en double, fuseaux IANA, cadence et durée maximales. Les plafonds USER sont relus depuis la politique ROOT avant une exécution ou une activation ; un ancien cron incompatible affiche Policy review required.
- La cadence minimale est calculée entre les créneaux du calendrier local. La prévisualisation utilise les règles du fuseau pour les instants UTC : les heures absentes sont sautées et une heure répétée produit deux occurrences distinctes.
- Prévisualisation de trois occurrences dans une fenêtre de 31 jours. Une expression annuelle reste valide même si elle n’a pas d’occurrence dans cette fenêtre. Prepare next est alors indisponible avec une explication.
- Run once crée un job immédiat dans le périmètre courant. Prepare next crée un job Scheduled qui attend un démarrage manuel dans le Job Center ; la date calculée est conservée dans son historique.
- Un seul job en attente par planning et un plafond de concurrence sont contrôlés. Pause concerne les futurs déclenchements ; les jobs déjà préparés ou lancés restent visibles. Un planning avec un job actif ne peut pas être supprimé.
- L’historique capture l’expression, le fuseau et l’opération au moment de la demande. Modifier un planning ne réécrit pas ses anciennes exécutions.

Ce prototype ne lance aucune commande et ne possède aucun ordonnanceur autonome. Les plafonds de durée sont des validations de configuration ; les jobs du runtime restent des simulations de progression.

## Composants et contrats

`assets/operations-core.js` fournit aux deux modules le chargement local, la persistance, le cycle de formulaire partagé et le raccordement au Job Center. `assets/protection-schemas.js` contient les contrats métier et le calcul des occurrences. Foundation reste propriétaire de DataGrid, des champs, de la validation et des dialogues génériques.

Les grilles sont détruites avant remplacement de leur onglet. Les onglets exposent leurs relations ARIA et la navigation par flèches, Home et End. Les styles propres aux contenus se trouvent dans `assets/operations.css`.

## Vérification

```powershell
node tools/audit_wcp360_protection.mjs
node tools/audit_wcp360_workspace.mjs
node tools/audit_wcp360_hosting.mjs
node tools/audit_wcp360_foundation.mjs
node tools/audit_wcp360_integration.mjs
node tools/audit_wcp360_design_system.mjs WCP360_Design_System
```

La suite protection contient **42 tests de comportement** : ressources et rétention, propriétaires, restauration, annulations, résultats appliqués une seule fois, rechargement, cron, concurrence, politique ROOT, fuseaux, décalage de 45 minutes et changements d’heure de printemps/automne. Elle exécute les vrais contrôleurs et schémas avec un adaptateur aux frontières des formulaires et grilles.

Les suites précédentes apportent 94 tests supplémentaires, soit **136 tests de comportement réussis**. L’intégration contrôle **232 pages, 44 scripts JavaScript, 3 127 références locales et 44 redirections**. Les trois pages et leurs contrôleurs répondent en HTTP 200 sur le serveur de prévisualisation local.

La vérification visuelle de cette passe n’a pas pu être menée à terme : Computer Use a interrompu le pilotage de Chrome car l’URL courante ne pouvait plus être déterminée avec assez de certitude. Le rendu, le focus natif, les exports et les petits écrans de ces trois nouveaux centres restent à vérifier dans un navigateur.
