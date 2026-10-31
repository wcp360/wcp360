# File Manager et Reseller Center — 30 octobre 2026

Cette passe remplace les tableaux, recherches et workflows propres à ces deux centres par `WCPUI.DataGrid`, `WCPUI.Form` et les dialogues communs. Le shell V2.5.3.12 est conservé.

## Fichiers

- Grilles communes pour le dossier courant, les transferts et la corbeille : recherche, filtres, tri, pagination, exports et préférences.
- Formulaires communs pour la création, le renommage, les permissions, la destination de copie/déplacement, l’upload, l’import, les clés publiques et les préférences.
- Copie, déplacement, renommage et corbeille prennent les descendants d’un dossier en compte. Sélectionner à la fois un parent et son enfant ne répète pas l’opération.
- Les collisions et chemins invalides sont refusés avant mutation. Les ressources protégées, leurs dossiers parents et `/backups` restent en lecture seule.
- La corbeille conserve chaque arbre comme un groupe. Une restauration vérifie le dossier d’origine et toutes les collisions avant d’ajouter une ligne. La purge demande `DELETE <nom>` et retire le groupe complet.
- L’upload sauvegarde uniquement les noms et tailles sélectionnés. Il ne lit pas le contenu des fichiers. Les imports distants sont des préflights simulés, sans requête réseau et sans URL privée persistée.
- Les transferts créés ici suivent le statut du Job Center. Les anciennes entrées sans identifiant de job restent conservées.

La clé métier existante `wcp360-file-manager-v2` est conservée. Les préférences de grille sont séparées par périmètre USER et propriétaire `example`.

| Ancienne page | Destination canonique |
| --- | --- |
| `file-trash.html` | `file-manager.html#trash` |
| `file-settings.html` | `file-manager.html#settings` |
| `file-transfers.html` | `file-manager.html#transfers` |
| `file-search.html` | `file-manager.html#files` |
| `file-upload.html` | `file-manager.html?action=upload` |

Les redirections conservent les paramètres et fragments explicites. Le paramètre `action=upload` est consommé après ouverture du formulaire. L’accès avancé aux services de transfert reste un centre distinct.

## Revendeur

- Grilles communes pour les clients, offres, préflights de transfert, brouillons de notification et historique local.
- « Manage » ouvre un formulaire. Suspension et reprise sont des actions distinctes et confirmées ; la reprise d’un compte Trial conserve son état Trial. Une sélection groupée incompatible est refusée entièrement.
- Création de client : domaine unique, e-mail valide, plafond de 25 comptes et offre publiée. Les clients historiques peuvent conserver leur offre Draft.
- Les offres sont réellement enregistrées. Leurs limites sont contrôlées selon le plan ROOT source ; une offre attribuée ne peut pas être réduite ou retirée de publication. Son renommage met à jour les références des clients.
- Le provisionnement et les préflights suivent des jobs du périmètre RESELLER. Le statut des clients passe à Active lorsque leur job de provisionnement se termine ; annulation/échec restent visibles.
- La marque, les demandes de support/capacité, les invitations d’opérateurs et les notices sont enregistrées localement. Les invitations et notices restent des brouillons : aucun message n’est envoyé.
- Les profils ACL et les exemples de consommation globale restent informatifs. Les frontières de production ne sont pas implémentées dans cette simulation.

La clé `wcp360-reseller-center-v1` est conservée et enrichie avec les offres et opérations locales. Les préférences de grille utilisent le périmètre RESELLER et le propriétaire `example-partner`.

## Contrats et vérification

Les règles métier sont dans `assets/workspace-schemas.js`, séparées des composants génériques de Foundation. Les anciens drawers et handlers de soumission/recherche ont été retirés des deux contrôleurs. Chaque grille est détruite avant remplacement de sa vue.

Depuis la racine du projet :

```powershell
node WCP360_Prototype/tests/audit_wcp360_workspace.mjs
node WCP360_Prototype/tests/audit_wcp360_hosting.mjs
node WCP360_Prototype/tests/audit_wcp360_foundation.mjs
node WCP360_Prototype/tests/audit_wcp360_integration.mjs
node WCP360_Prototype/tests/audit_wcp360_prototype.mjs WCP360_Prototype
```

Résultat de cette passe : **38 tests workspace**, **27 tests mail/comptes/récupération** et **29 tests Foundation** réussis ; **232 pages**, **40 scripts JavaScript**, **3 305 références locales** et **37 redirections de compatibilité** contrôlés. L’audit statique ne rapporte aucune anomalie et les 40 scripts passent la compilation syntaxique.

La suite workspace exécute les vrais contrôleurs avec un adaptateur à la frontière du DOM. Elle couvre les arbres de fichiers, collisions, protections, annulations, uploads, restauration, plafonds, références d’offres et jobs. Les autres suites vérifient les régressions du socle et des modules précédents ainsi que les références et routes des 232 pages.

Chrome a permis de contrôler les grilles clients/fichiers à environ 1067 × 820 px, leurs barres de défilement locales, la recherche de `index.php`, la modale de création de client et celle de renommage d’un fichier. Ces modales ont été fermées sans créer de données de démonstration. Les petits écrans mobiles, lecteurs d’écran et téléchargements d’exports restent à vérifier dans un navigateur.

Le prototype ne manipule aucun fichier serveur et ne met en œuvre ni authentification, ni autorisation serveur, ni provisionnement réel. Le contenu de fichiers, l’éditeur complet et les politiques avancées restent des travaux distincts.
