# Migration Messagerie, Comptes et Récupération

Cette passe poursuit l’intégration HTML avec WCP360 UI Foundation 1.1. Elle ajoute quatre grilles communes aux grilles Sites, Bases et Galerie déjà migrées.

| Centre | Liste commune | Formulaires communs | Données conservées |
|---|---|---|---|
| [Messagerie](email.html#mailboxes) | Boîtes mail | Création et édition | `wcp360-email-center-v2`, préférences Webmail et sessions existantes |
| [Routage mail](email.html#routing) | Alias, transferts et identités d’envoi | Création et édition | Même contrat mail, paramètres catch-all et réponses automatiques |
| [Comptes](hosting-accounts.html#accounts) | Comptes et ressources effectives | Provisionnement, capacité/placement, suspension et résiliation | `wcp360-hosting-capacity-v1`, plans, serveurs et extensions de capacité |
| [Récupération](hosting-accounts.html#recovery) | Archives finales | Confirmation de restauration et purge | `wcp360-account-recovery-v1` |

Les grilles partagent recherche, tri multicritère, filtres combinés, pagination, export, colonnes et vues enregistrées. Les préférences USER et ROOT restent séparées par application, périmètre, propriétaire et module. Les anciens contrôleurs de recherche et les renderers de listes correspondants ont été retirés.

## Règles métier testables

[assets/hosting-schemas.js](assets/hosting-schemas.js) fournit les schémas métier `WCPHostingSchemas`. Ils sont chargés après `foundation.js` dans les deux centres concernés. Ils ne sont pas une dépendance obligatoire de la bibliothèque générique.

- Les adresses de boîtes mail et d’alias partagent un même espace de noms, insensible à la casse. Les noms mal formés, doublons, dépassements du nombre de boîtes et quotas inférieurs à l’utilisation courante sont refusés.
- Le mot de passe temporaire est contrôlé à la saisie, puis effacé du formulaire après création ; il n’est pas enregistré dans l’état du navigateur.
- Les clients Webmail proposés proviennent du catalogue activé. L’absence de client ne bloque pas la création d’une boîte mail. Une liste de boîtes vide possède un état explicite.
- Renommer une boîte met à jour ses préférences, les destinations de ses alias, ses réponses automatiques et les références de session de démonstration.
- Un alias doit viser une boîte existante. Les nouvelles destinations de transfert doivent être des adresses valides ; les destinations externes restent en attente de vérification. Les anciens formats opaques restent conservés tant que leur destination et leur type ne changent pas.
- Le provisionnement d’un compte conserve identité, contact, propriétaire/revendeur, plan, blueprint, liste de fonctions, runtime, langue, DNS, AutoSSL, mail et accueil dans une seule écriture.
- Une topologie avec réplica impose un second serveur distinct. Une topologie cluster exige une cible cluster. Les extensions de ressources ne peuvent pas être négatives.
- La suspension et la réactivation respectent le statut du compte. La résiliation exige la saisie exacte du domaine et préserve le choix d’une archive finale.

Les opérations restent simulées et visibles dans le Job Center. Aucun serveur ni service mail réel n’est modifié.

## Récupération et navigation

- Une résiliation sans sauvegarde finale ne fabrique plus d’archive de récupération.
- Une archive en attente de restauration ne peut plus être restaurée, purgée ou remise à zéro par une vérification.
- Une archive à examiner doit être vérifiée avant de proposer sa restauration. Un legal hold empêche sa purge.
- La purge utilise le dialogue commun avec saisie exacte `PURGE <domaine>`.
- Les grilles sont détruites lors du changement entre inventaire et récupération ; les états actifs des onglets restent cohérents.
- Les ancres `email.html#mailboxes`, `email.html#routing` et `hosting-accounts.html#recovery` ouvrent les vues correspondantes.
- `mailboxes.html` et `mail-aliases.html` deviennent deux nouvelles routes de compatibilité vers le centre mail. Le prototype possède ainsi 32 routes de compatibilité.

La restauration reste un job simulé en attente ; elle ne reconstitue pas les fichiers ou services d’un hébergement réel.

## Changements du socle

Foundation 1.1 exécute les validateurs personnalisés même sur un champ facultatif vide ou une case décochée. Les contraintes obligatoires et de format restent prioritaires. Cela permet les règles conditionnelles de destination, vérification et serveur secondaire.

Lorsque `onSubmit` est fourni, le contrôleur commun consomme la soumission avant les anciens gestionnaires, pour éviter une double écriture ou un double job. Sans `onSubmit`, il valide puis laisse le gestionnaire métier existant agir. Le focus retrouve la ligne ou la recherche après un remplacement du DOM ; les dialogues résolvent leur promesse même si leur callback de fermeture échoue.

Les drawers mail et comptes fermés, ainsi que leurs backdrops, sont inertes. Leurs contrôles ne restent donc pas dans le parcours clavier.

## Vérification

Depuis la racine du workspace, avec Node disponible :

```powershell
node WCP360_Prototype/tests/audit_wcp360_hosting.mjs
node WCP360_Prototype/tests/audit_wcp360_foundation.mjs
node WCP360_Prototype/tests/audit_wcp360_integration.mjs
node WCP360_Prototype/tests/audit_wcp360_prototype.mjs WCP360_Prototype
```

La suite métier exécute les vrais contrôleurs avec des adaptateurs aux frontières UI. Elle couvre notamment les soumissions, la persistance, les dépendances d’un renommage, les statuts de récupération et l’unicité des jobs. Le socle et les contrôles statiques restent vérifiés séparément.

Les deux centres, les nouvelles ressources et les redirections ont répondu en HTTP 200. L’affichage de la messagerie a été observé dans Chrome à une largeur de fenêtre d’environ 1 067 px. Les interactions automatiques ont ensuite été arrêtées après détection répétée d’une saisie utilisateur dans la fenêtre. Les interactions natives, le rendu mobile, le mode sombre et les parcours visuels complets des comptes/récupération restent à vérifier dans le navigateur.
