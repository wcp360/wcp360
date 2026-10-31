# Configuration et exploitation des applications — 30 octobre 2026

Cette passe remplace les réglages applicatifs statiques par des parcours persistants qui utilisent Foundation et `operations-core.js`. Le shell et le langage visuel de V2.5.3.12 sont conservés.

## Centres et propriété

| Centre | Contenu | Propriétaire |
| --- | --- | --- |
| `app-lifecycle.html` | Vue d’ensemble, sites/environnements, runtime, variables, maintenance et activité | USER, sous-centre de Websites |
| `app-health.html` | Configuration des checks, observations simulées et activité du site sélectionné | USER / Operations / Metrics & Health |
| `websites.html` | Inventaire des sites, création, domaines, fichiers et liens vers les opérations applicatives | USER / Workspace |
| `web-runtime.html` | Inventaire installé et gouvernance des moteurs de plateforme | ROOT, conservé séparément |

Le paramètre `site` sélectionne un identifiant de l’inventaire du compte. Un identifiant explicite inconnu n’ouvre pas un autre site. Le sélecteur permet de changer de site sans changer de portail ni de permissions. Les anciennes entrées sans propriétaire explicite restent reconnues comme des données de ce prototype de compte ; une ligne explicitement affectée à un autre propriétaire est exclue.

Quatre pages deviennent des redirections de compatibilité, avec conservation des paramètres et fragments explicites :

| Ancienne route | Destination |
| --- | --- |
| `environments.html` | `app-lifecycle.html#environments` |
| `app-runtime.html` | `app-lifecycle.html#runtime` |
| `environment-variables.html` | `app-lifecycle.html#variables` |
| `app-maintenance.html` | `app-lifecycle.html#maintenance` |

Les environnements listés viennent des sites existants, avec leur classification Production/Staging/Development. Leur création reste dans le parcours de création ou clonage des sites. Les environnements de livraison et leurs versions restent gérés par Deployments.

## Un inventaire commun, une commande par fonction

`assets/website-model.js` porte les données initiales et l’accès à `wcp360-website-center-v1`. Les trois pages utilisent ce même modèle. Les données existantes sont conservées.

Les réglages runtime du formulaire historique Manage, son bouton de maintenance, l’action groupée de maintenance et son ancien job de redémarrage ont été retirés. Les liens conduisent au site précis dans le centre applicatif. Le cache et les autres opérations de site restent dans leurs parcours existants. Les liens utilisent les styles de boutons des cartes correspondantes, sans changement du shell.

Les sauvegardes du centre Sites préservent les champs runtime/version/maintenance/statut relus dans l’inventaire. Les événements `storage` rafraîchissent les résumés ouverts. Le centre applicatif relit l’inventaire avant application et conserve les sites voisins ; il ne recrée jamais un site supprimé pendant un job.

## Brouillon et application

État métier : `wcp360-account-applications-v1`. Chaque site possède un brouillon, une configuration publiée distincte, des révisions et une génération d’application. Les tableaux et objets imbriqués du brouillon et de la configuration publiée ne partagent pas de référence.

- Modifier le runtime ou les variables ne crée pas de job et ne modifie pas l’état appliqué.
- Review & apply présente le site, l’environnement, la révision et les changements. Une raison est requise ; la production exige la saisie exacte du hostname.
- Un seul job de mutation peut être actif par site. Les checks peuvent être demandés séparément, mais leur résultat est invalidé si leur contexte change.
- Le job capture une copie de la configuration. Seul un résultat terminé avec validation simulée « Pass » l’applique. Une annulation, un échec ou un refus de politique conserve l’état actif.
- Les modifications de brouillon suivantes restent intactes après application du snapshot précédent.
- Un changement de cible, de révision ou de génération invalide une ancienne revue. Les générations détectent aussi un état passé par plusieurs transitions avant de revenir au même statut apparent.
- L’abandon du brouillon restaure la configuration appliquée, exige le hostname et est bloqué pendant une mutation en attente.

## Runtime et variables

Le runtime conserve sa famille. Les versions sélectionnables sont dérivées de l’inventaire ROOT `wcp360-runtime-center-v1` : statut Recommended/Supported et présence sur au moins un nœud. Les versions de patch sont regroupées par ligne de version ; Node utilise le label majeur LTS. Sans état ROOT enregistré, un catalogue de démonstration est disponible. Un inventaire ROOT explicite vide ou illisible bloque les choix correspondants. Aucune version ou disponibilité réelle de logiciel n’est déduite de ces données fictives.

Le formulaire vérifie le budget mémoire entier de 64 à 512 MiB et le fuseau IANA. Ce plafond est une contrainte fixe du compte de démonstration, pas une traduction complète des quotas de production. La politique de version est relue à la soumission et à la fin du job. Le portail USER n’installe aucun package.

Les variables utilisent des noms uniques en majuscules. `APP_ENV` et `WCP_ACCOUNT` sont calculées et gérées par la plateforme ; les clés de contrôle de processus réservées ne sont pas éditables. Les noms sensibles, tels que DB_PASSWORD, APP_KEY ou API_TOKEN, exigent un identifiant `demo://secrets/...`. Le formulaire ne fournit aucun champ de clé privée, token ou mot de passe réel. Les valeurs ordinaires sont des textes de démonstration sur une ligne. Le prototype ne fournit ni Vault, ni chiffrement de secrets, ni fonction Reveal.

La recherche, les exports et les préférences de grille voient les valeurs ordinaires et les identifiants de référence affichés. Les jobs et l’activité n’incluent pas de matériau secret. La modification ou suppression d’une variable reste dans le brouillon jusqu’à publication.

## Maintenance et santé

La maintenance simule une réponse applicative 503 avec message et raison. La production exige le hostname. L’activation attend la fin du job ; la désactivation restaure le statut précédent, notamment Protected pour un staging protégé. Un job annulé ou échoué conserve le statut. Aucun serveur n’est drainé, arrêté ou redémarré, et aucun droit de bypass n’est accordé.

Les checks commencent avec « Not measured ». Le formulaire configure un chemin applicatif relatif, une réponse attendue 200/204, un timeout borné et l’activation. Les URL externes, chemins réseau, traversées et échappements sont refusés par le sous-ensemble de chemins du prototype.

Un check manuel choisit une observation simulée Pass/Fail et ne contacte aucune URL. La fin du job enregistre une observation explicitement simulée. Une annulation ne crée pas d’observation. Une configuration de check, une version appliquée, une identité de site ou une génération de maintenance différente rend le résultat obsolète. Les anciennes observations restent conservées mais ne sont pas présentées comme une mesure actuelle.

Les rapprochements de jobs utilisent l’identifiant exact et le scope USER. Les résultats terminés sont appliqués une seule fois, y compris lors du chargement différé du Job Center.

## Vérification

```powershell
node WCP360_Prototype/tests/audit_wcp360_applications.mjs
node WCP360_Prototype/tests/audit_wcp360_delivery.mjs
node WCP360_Prototype/tests/audit_wcp360_dns_tls.mjs
node WCP360_Prototype/tests/audit_wcp360_protection.mjs
node WCP360_Prototype/tests/audit_wcp360_workspace.mjs
node WCP360_Prototype/tests/audit_wcp360_hosting.mjs
node WCP360_Prototype/tests/audit_wcp360_foundation.mjs
node WCP360_Prototype/tests/audit_wcp360_integration.mjs
node WCP360_Prototype/tests/audit_wcp360_prototype.mjs WCP360_Prototype
```

Résultats : **47 nouveaux tests**, soit **271 tests de comportement réussis** avec les suites précédentes. L’intégration vérifie **232 pages, 51 scripts, 2 832 références locales, 57 redirections et 4 099 assertions**. Foundation est chargé par 168 pages complètes ; les quatre pages consolidées sont désormais de simples redirections. L’audit statique ne relève aucun écart. Les deux centres, quatre redirections, Sites et les trois nouveaux scripts répondent en HTTP 200.

Les tests utilisent les vrais contrôleurs, le modèle de sites et les schémas métier avec des adaptateurs aux frontières du DOM. Le connecteur navigateur était indisponible : le rendu, les dialogues natifs, le focus, les téléchargements et les petits écrans restent à vérifier visuellement.

Les mutations sont des simulations dans localStorage, sans appel système, requête de santé, publication de site ou ordonnanceur réel. Les contrôles de formulaire et de propriété ne constituent pas une authentification serveur. Le rafraîchissement entre onglets ne remplace pas des transactions et verrous backend.
