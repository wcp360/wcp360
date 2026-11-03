# Consignes permanentes WCP360

Périmètre : ce dépôt `WCP360_Standalone`. Les sites Grav, stockages métier et composants XAMPP gardent leurs périmètres et dépendances propres.

## Références et architecture

- Avant chaque tâche, toujours se référer à la [documentation de développement WCP360](http://localhost/WCP360-DocumentationOfDeveloppement/), point d'entrée central pour l'ensemble du projet, puis consulter les rubriques pertinentes. Signaler toute contradiction avec les contrats et politiques applicables.
- Lire [CONTRIBUTING](CONTRIBUTING.md), [SECURITY](SECURITY.md) et le [guide Core](WCP360_Core/WORKING-GUIDE.md), puis les guides concernés. [STATUS](WCP360_Core/STATUS.md) porte les preuves actuelles ; [ROADMAP](WCP360_Core/ROADMAP.md), l'ordre de livraison.
- [Architecture cible](ARCHITECTURE.md) : PHP/Symfony, Twig/UX/Turbo/Stimulus, PostgreSQL ; agents Go `wcpd` sans root et `wcp-privd` limité aux primitives approuvées. Un candidat ne vaut pas qualification de production.
- `WCP360_Core/` possède les fonctions essentielles ; `WCP360_Modules/`, `WCP360_Plugins/` et `WCP360_SDK/` conservent leurs contrats et graphes de dépendances séparés. Le Core reste autonome sans extension ni service fournisseur.
- `WCP360_App_Deploy/` porte les recettes ; `WCP360_Prototype/` les maquettes ; `WCP360_Build/` la construction ; `WCP360_Releases/` les livrables qualifiés. Préserver les sources, archives et données existantes.
- Respecter les portes du [manifeste](documentation/manifests/WCP360-BM-0001.md). Impact transverse : `WCP360 HANDOFF` ; contradiction : `WCP360 CONFLICT`, selon CONTRIBUTING. Aucune nouvelle autorisation runtime ni réécriture de contrat ici.

## Méthode de travail

- Un objectif par tâche, terminé avec ses contrôles ; découper les grandes fonctionnalités selon leurs dépendances.
- Les skills `wcp360-development` et `wcp360-ecosystem`, lorsqu'ils sont disponibles dans l'espace de travail local `.agents/skills/`, peuvent aider au repérage ; ils ne font pas partie de ce dépôt et ne remplacent pas ses guides versionnés.
- Pour une vue d'ensemble ou un impact sur d'autres projets WCP360, vérifier les contrats des producteurs/consommateurs concernés avant de limiter le changement au seul Standalone.
- Examiner `git status` et les différences ciblées ; préserver les modifications d'autrui et la branche existante. Rechercher avec `rg`, lire seulement les fichiers nécessaires.
- MCP selon le besoin : Context7 pour API/version de bibliothèque, documentation OpenAI pour intégration OpenAI, Playwright pour navigateur autorisé. Réutiliser la documentation vérifiée encore applicable.
- Outils selon disponibilité : Git, Zeal/DevDocs, Ollama, Bruno, DBeaver ; PHPStan, golangci-lint, Ruff selon leur configuration. Voir le catalogue ci-dessous ; aucune installation globale ni transmission de secrets implicite.
- Une conversation par fonctionnalité/bug ; reprise concise avec objectif, fichiers, décisions, contrôles et points ouverts. Rendre changements, vérifications exécutées et limites.

## Tests et sécurité à chaque modification

Suivre [TESTING](documentation/development/TESTING.md), la [matrice de sécurité](documentation/development/SECURITY-TEST-MATRIX.md) et le [catalogue d'outils](documentation/development/QUALITY-SECURITY-STACK.md). Dans l'espace de travail XAMPP, `projects/WCP360_Quality/README.md` décrit les recettes locales ; ce dossier qualité est hors de ce dépôt.

Les essais exécutables locaux restent arrêtés selon le guide Core ; utiliser la CI existante. Commandes de référence en environnement autorisé, après les prérequis de TESTING :

| Composant / répertoire de travail | Contrôles de référence |
| --- | --- |
| Core, `WCP360_Core/` (PHP 8.4, dépendances verrouillées, planner construit, base de test) | `composer check` : PHPStan/Symfony, régressions d'analyse, PHPUnit, lint conteneur/YAML/Twig. |
| Navigateur Core, `WCP360_Core/` | `composer test:browser`, séparément avec Chromium/ChromeDriver et fixtures privées. |
| Go, chaque module `WCP360_Core/agents/wcpd/candidate/planner/` et `WCP360_Core/agents/wcpd/candidate/provisioning/` | `go test ./...`, `go vet ./...` ; race/fuzz et effets Linux selon TESTING. |
| Documentation, racine du dépôt | `git diff --check`, liens/ancres, exemples et exigences conservées ; pas de démarrage de services. |

- Identifier contrats/frontières de confiance ; exécuter syntaxe/analyse configurée, tests fonctionnels/non-régression et refus pertinents. Aucun test recopiant l'implémentation.
- Accès/API : anonymes, mauvais rôle/portée, propriétaire/tenant, entrées invalides, CSRF des écritures navigateur et échappement. Autoriser côté serveur ; préserver la séparation USER/ADMIN et refuser par défaut.
- Fichiers/paquets : traversée, liens/collisions, bornes, corruption, identité, clés inconnues/révoquées, expiration et concurrence selon le changement. Une signature valide n'autorise pas l'installation ; aucun exécuteur privilégié générique.
- Dépendances : vrais manifests/locks du graphe concerné, avis et fraîcheur de leur base. Ne pas modifier le lock Core ni partager son `vendor` pour outiller un site ou SDK.
- Fixtures synthétiques, espaces privés jetables ; aucun secret réel ni test destructif/actif/de charge sur une installation cliente. Vérifier les chemins avant nettoyage récursif.
- Rapport daté : périmètre, révision/empreintes, environnement, outils/configuration, commandes, résultats et limites ; justifier `passed`, `failed`, `not_run`, `not_configured`, `not_applicable`.
- Corriger les échecs introduits et relancer les contrôles concernés. Un contrôle requis manquant bloque la qualification correspondante ; aucun contournement par exclusion globale, baisse de niveau ou désactivation. Résultats locaux ≠ audit complet, revue indépendante ou qualification de production.
