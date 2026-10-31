# Guide de démonstration WCP360

Ce dossier contient le prototype interactif local. Il sert à vérifier les parcours et les contrats d'interface avant leur implémentation dans Symfony et les agents Go. Les comptes, domaines, tâches, services et fichiers sont fictifs.

## Ouvrir et conserver une session

Depuis la racine du dépôt, exécuter l'une des commandes suivantes puis ouvrir `http://127.0.0.1:8790/` :

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\WCP360_Prototype\serve-prototype.ps1
```

```sh
node WCP360_Prototype/serve-prototype.mjs
```

Le serveur Node fonctionne sous Linux, macOS et Windows. Aucun serveur ne demande de privilège root ; tous deux écoutent uniquement sur `127.0.0.1`. Un autre port peut être choisi : `-Port 8792` en PowerShell ou `node WCP360_Prototype/serve-prototype.mjs 8792`.

Conserver le même hôte et le même port pour retrouver les données. Un autre navigateur ou profil fournit une démonstration indépendante. Les autres pages ouvertes sur cette origine peuvent partager l'état ; ne pas utiliser de données réelles. L'ouverture directe en `file://` ne garantit pas la persistance entre pages. Les polices et icônes Google nécessitent un accès réseau.

## Parcours à vérifier dans l'ordre

| Parcours | Étapes | Résultat attendu dans le prototype |
| :--- | :--- | :--- |
| Préparation Linux | [Assistant](setup-wizard.html) → mode Quick → identité → précontrôles | Le profil Debian 13 / amd64 prêt autorise la revue ; les profils disque, DNS, ports et horloge bloquent la suite. Les résultats sont des fixtures. |
| Configuration détaillée | Assistant → mode Expert → base, stockage, réseau, sécurité, ressources, runtimes, services | Une entrée invalide bloque son formulaire. PostgreSQL 17 est la cible du control plane. Les moteurs de bases hébergées se gèrent séparément. |
| Publication de la configuration | Revue → précontrôles réussis → Apply | Une révision locale et une opération simulée sont créées. Un échec de stockage ne doit pas afficher de succès. |
| Hébergement | [Sites](websites.html) → [domaines](domains.html) → [certificats](ssl.html) → [tâches](jobs.html) | Les formulaires, validations et cycles de vie réutilisent les moteurs communs. |
| Données du compte | [Fichiers](file-manager.html) → [bases](databases.html) → [email](email.html) | Les actions prises en charge modifient les collections locales. Aucun fichier Linux, message SMTP ou serveur SQL n'est manipulé. |
| Reprise | [Sauvegardes ROOT](admin-backups.html) → [sauvegardes USER](backups.html) → [tests de restauration](restore-tests.html) | La politique, les tâches et les états se parcourent dans les centres existants ; aucune archive de production n'est générée. |
| Modularité | [Logiciels et extensions](software-manager.html) → onglet Plugins → désactiver App Installer → [installer USER](app-installer.html) → réactiver | L'extension désactivée affiche un état indisponible et son entrée disparaît ; les fonctions Core restent accessibles. |
| Exploitation | [Services](service-manager.html) → [incidents](incidents.html) → [audit](audit-explorer.html) | Vérifier les états, liens et actions simulées ; aucune commande système n'est exécutée. |
| Délégation | [Revendeur](reseller.html) → client, offre et limites | Les limites de l'offre et du périmètre revendeur restent appliquées dans la simulation. |

## Précontrôles et références de secrets

`assets/setup-model.js` centralise la validation et cinq profils déterministes. Il contrôle les noms, adresses, bornes numériques, chemins de données, endpoints sans identifiants, références de secrets et dépendances de services. MFA ROOT, pare-feu et absence de connexion SSH directe root constituent la baseline de démonstration.

Les champs de secrets n'acceptent que des références synthétiques telles que `secret://demo/control-database`. Ils n'enregistrent pas de mot de passe ou de clé privée. Les anciens brouillons de l'assistant sont réécrits pour retirer leurs identifiants littéraux. Cette règle est propre à l'assistant : le prototype entier ne constitue pas un coffre de secrets.

Un résultat de précontrôle n'est jamais réutilisé après rechargement ou modification. Il faut le relancer. Le serveur, le disque, le DNS, les ports et l'horloge réels ne sont pas inspectés. Les seuils affichés illustrent le parcours ; ils ne définissent pas le dimensionnement de production.

## Navigation et recherche

L'accueil propose USER, RESELLER et ROOT, des catégories et six parcours. Le registre partagé `assets/reference-ui.js` possède les routes canoniques ; les anciennes URL restent des redirections de compatibilité.

La recherche accepte plusieurs mots et des synonymes français, avec ou sans accents : `sécurité audit`, `sauvegardes`, `fichiers`, `certificats`. Tous les mots doivent correspondre. La requête figure dans l'URL pour retrouver une recherche. `/` place le focus dans le champ et `Échap` efface les filtres lorsque ce champ est actif. `Ctrl+K` ouvre la palette commune sur les surfaces qui la proposent.

## Limites et passage vers le produit

| Prototype | Implémentation de production attendue |
| :--- | :--- |
| État localStorage et données d'exemple | Symfony, Doctrine et PostgreSQL, transactions et migrations vérifiées |
| Sélecteur de portail et contrôles JavaScript | Authentification et autorisations côté serveur, isolation ADMIN/USER et séparation des tenants |
| Tâches et précontrôles simulés | Workers, opérations idempotentes, observation des résultats réels et procédures de reprise |
| Écrans Linux et services | `wcpd` non privilégié et `wcp-privd` minimal, IPC borné et opérations autorisées |
| Références synthétiques | Gestion réelle des secrets hors navigateur, accès minimaux et rotation |
| Activation visuelle des plugins | Processus détachés, SDK, permissions, dépendances, drain, santé et arrêt vérifiés |

La matrice historique [20-integration-coverage-audit.md](20-integration-coverage-audit.md) décrit les surfaces interactives, partielles et statiques. Les mentions de fermeture de conception ne certifient pas une fonctionnalité de production. Le renommage du dossier n'approuve aucune nouvelle tranche de runtime système ni aucune release.

Les commandes de vérification sont dans [tests/README.md](tests/README.md).
