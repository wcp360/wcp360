# Git, builds et déploiements du compte — 30 octobre 2026

Les parcours USER de livraison partagent désormais un contrôleur et un état métier. Le shell, les styles, la sidebar et les thèmes de la baseline V2.5.3.12 sont conservés. Les listes, recherches, filtres, exports, colonnes et vues enregistrées utilisent Foundation ; les formulaires, confirmations et jobs utilisent `operations-core.js`.

## Propriétaires et liens

| Route | Contenu | Propriétaire |
| --- | --- | --- |
| `git-repositories.html` | Métadonnées des dépôts, révision source, profil de build, politique de branche et archivage réversible | USER / `example.com` |
| `deployments.html#environments` | Versions actives de staging et production | USER / `example.com` |
| `deployments.html#builds` | Builds simulés et résultats | USER / `example.com` |
| `deployments.html#releases` | Versions conservées, promotion et retour arrière | USER / `example.com` |
| `deployments.html#activity` | Jobs et résultat de leur application locale | USER / `example.com` |
| `delivery-platform.html` | Gouvernance des fournisseurs Git, runners et politiques de plateforme | ROOT ; workflow existant séparé |

Git et Deployments restent dans la catégorie USER « Operations ». Trois pages redondantes deviennent des routes de compatibilité :

- `builds.html` → `deployments.html#builds` ;
- `releases.html` → `deployments.html#releases` ;
- `release-rollback.html` → `deployments.html?action=rollback#releases`.

Les paramètres et fragments explicites sont conservés. Le retour arrière par URL demande un `id` de version conservée et ouvre la revue ; une URL sans identifiant invite à choisir une ligne. Aucun retour arrière n’est choisi ou exécuté automatiquement. Le paramètre `action` est consommé après ouverture.

Le module Git historique a été retiré de `hosting-parity.js`. Les deux pages chargent `assets/delivery-center.js` et `assets/delivery-schemas.js` ; elles n’ont pas de contrôleurs métier distincts. Le résumé « Access & deployment » de Sites lit les versions actives de ce même inventaire et ignore son ancien historique fictif.

## Sources et builds

- Les dépôts stockent seulement un nom, une URL HTTPS sans credentials/query/fragment, un label de branche, un profil fixe et une préférence d’accès à la production. Les noms sont uniques, y compris parmi les archives.
- Les labels de branche suivent un sous-ensemble volontairement limité et décrit par le formulaire. Il ne s’agit pas d’une validation exhaustive de références Git.
- Une révision de démonstration contient 7 à 40 caractères hexadécimaux. Un job de source doit terminer avec succès avant de remplacer cette révision. Un changement de branche vide la révision et exige une nouvelle simulation de source.
- Un seul job source/build peut être actif par dépôt. Une édition de configuration est bloquée pendant ce job. Une révision de configuration détecte les formulaires devenus obsolètes.
- Un build capture branche, révision et profil. Un résultat « Pass » crée une seule version de métadonnées immuable ; un résultat « Fail », une annulation ou un échec du runtime n’en crée aucune.
- Les labels des versions réussies et des builds en attente sont uniques. Les versions sont conservées sans purge automatique.
- L’archivage exige une confirmation avec le nom du dépôt. Un dépôt qui fournit un environnement actif ne peut pas être archivé ; les métadonnées archivées peuvent être restaurées.

## Promotion et retour arrière

Une activation capture l’identifiant de version et la génération de l’environnement. Elle attend un job terminé et un contrôle de santé simulé « Pass ». Un échec ou une annulation conserve la version active et son historique.

La production exige que la version exacte soit active en staging avec un résultat sain et que sa branche corresponde à la branche actuellement autorisée du dépôt. La revue présente la version, sa révision, la destination et la version actuellement active. Elle demande une note et la saisie exacte de `example.com`. Les contrôles sont répétés à la soumission et à l’application du résultat ; un changement de staging après la revue bloque la promotion.

Le retour arrière réutilise cette même transition et exige une version déjà utilisée avec succès en production. Il conserve les autres versions et ajoute une activation à l’historique. Il reste disponible pour une version historique même si sa branche n’est plus la branche courante : il s’agit d’un retour à un état précédemment sain, avec revue explicite. Les migrations de bases et les dépendances applicatives ne sont pas modélisées.

Un seul job d’activation peut viser un environnement à la fois. Les jobs sont rapprochés par identifiant exact et scope USER, et leur résultat est appliqué une seule fois, y compris au rechargement du runtime.

## Persistance et extension

Clé métier : `wcp360-account-delivery-v1`. Les préférences de grille restent isolées sous USER / `example.com`. Les données ROOT ne sont pas modifiées.

Les centres relisent les changements enregistrés avant leurs transitions et réagissent aux événements `storage`. Une revue ouverte dans un onglet détecte ainsi une configuration changée dans un autre. Ce mécanisme de démonstration localStorage n’est pas un verrou transactionnel entre processus ou utilisateurs.

L’extension `developer-pipelines` reste optionnelle. Sa désactivation par ROOT masque les entrées, bloque les nouvelles actions et empêche l’application d’un job encore en attente. Les données restent conservées. La disponibilité des routes est désormais évaluée après résolution canonique, y compris pour les anciennes URL et les paramètres de requête.

## Vérification et limites

```powershell
node WCP360_Prototype/tests/audit_wcp360_delivery.mjs
node WCP360_Prototype/tests/audit_wcp360_dns_tls.mjs
node WCP360_Prototype/tests/audit_wcp360_protection.mjs
node WCP360_Prototype/tests/audit_wcp360_workspace.mjs
node WCP360_Prototype/tests/audit_wcp360_hosting.mjs
node WCP360_Prototype/tests/audit_wcp360_foundation.mjs
node WCP360_Prototype/tests/audit_wcp360_integration.mjs
node WCP360_Prototype/tests/audit_wcp360_prototype.mjs WCP360_Prototype
```

Résultats : **44 nouveaux tests**, soit **224 tests de comportement réussis** avec les suites précédentes. L’intégration vérifie **232 pages HTML, 48 scripts, 2 934 références locales, 53 redirections et 4 193 assertions**. L’audit statique ne signale aucun écart. Foundation est chargé par 172 pages complètes ; les trois anciennes pages de livraison sont désormais de simples redirections. Les deux centres, trois redirections et deux nouveaux scripts répondent en HTTP 200.

Les tests exécutent les vrais contrôleurs avec des adaptateurs aux frontières du DOM. La vérification visuelle n’a pas été réalisée : le connecteur par onglet était indisponible et Chrome était utilisé en parallèle. Les petits écrans, le focus des dialogues et les téléchargements restent à vérifier dans un navigateur.

Les workflows ne contactent aucun dépôt, ne clonent aucun fichier, ne lancent aucune commande et ne déploient aucun site réel. Les profils, révisions, versions et contrôles de santé sont des données de démonstration. Aucune clé SSH, token, webhook entrant ou intégration CI réelle n’est créé.
