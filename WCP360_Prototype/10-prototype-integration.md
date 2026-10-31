# Intégration du prototype — 30 octobre 2026

Le point d’entrée est [index.html](index.html). Les portails USER, RESELLER et ROOT proposent un catalogue filtrable alimenté par le même registre que la sidebar et la recherche de commandes. Le shell et les composants de la baseline visuelle restent la référence.

## Navigation et consolidation

- 99 entrées organisées : 22 USER, 10 RESELLER, 61 ROOT et 6 outils de conception.
- Chaque page hors connexion/MFA est rattachée à un centre. Les détails ouvrent sa catégorie et gardent une seule entrée active.
- Les outils ROOT auparavant absents de la sidebar sont accessibles dans leur catégorie : toolbox DNS, providers, continuité, confidentialité, credentials, delivery et virtualisation.
- Les extensions désactivées disparaissent du catalogue et de la recherche de commandes.
- 30 anciennes routes deviennent des pages de compatibilité : leurs interfaces en doublon sont retirées et les liens existants continuent de fonctionner.
- Les anciens formulaires de création de site, base, utilisateur de base, boîte mail et alias ouvrent directement le formulaire persistant du centre concerné. L’action est consommée dans l’URL après ouverture.

## Tâches et notifications

Le stockage `wcp360-runtime-v2` alimente le drawer Job Center, [jobs.html](jobs.html), [job-detail.html](job-detail.html) et [notifications-center.html](notifications-center.html). Les tâches gardent leur identifiant, progression, statut et corrélation lors de la navigation. Une tâche en attente reste arrêtée jusqu’à « Start now ». Une tâche annulée conserve ce statut.

Les tâches et notifications affichées sont limitées au portail courant. Les liens d’audit renvoient vers l’audit ROOT, l’audit délégué RESELLER ou l’audit de sécurité USER. Cette séparation sert la démonstration ; elle ne constitue pas un mécanisme d’autorisation serveur.

## Démarrage

Depuis la racine du workspace :

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File ".\WCP360_Prototype\serve-prototype.ps1"
```

Ouvrir **http://127.0.0.1:8790/**. Le serveur écoute uniquement en local et sert ce dossier. `Ctrl+C` l’arrête. Conserver le même hôte et le même port pour retrouver les données du navigateur.

## Vérification

```powershell
node WCP360_Prototype/tests/audit_wcp360_integration.mjs
node WCP360_Prototype/tests/audit_wcp360_prototype.mjs WCP360_Prototype
```

La passe d’intégration vérifie les 232 documents HTML, les références locales, les identifiants, les 36 scripts JavaScript, la propriété des routes, la visibilité des extensions et les redirections. Un adaptateur DOM minimal vérifie le moteur d’état : persistance, progression, démarrage manuel, annulation, notifications, échappement et séparation des portails.

Le serveur PowerShell a été vérifié par HTTP : accueil et scripts en 200, HEAD sans corps, fichiers absents et sortie du dossier en 404, méthodes d’écriture en 405.

La vérification visuelle n’a pas pu être réalisée : Computer Use a interrompu l’accès à Chrome car l’URL de la fenêtre ne pouvait pas être déterminée avec certitude. Les rendus responsive, le focus dans un vrai navigateur et les nouveaux raccourcis ouvrant les formulaires restent à contrôler visuellement.

## Limites

Les opérations restent fictives ; aucun serveur d’hébergement réel n’est administré. Les données vivent dans le navigateur et les centres spécialisés conservent leurs propres contrats de démonstration. Le Job Center simule le suivi ; il n’exécute pas de commande système. L’ouverture directe des fichiers HTML reste possible, mais ne garantit pas un stockage commun entre les pages.


## Socle partagé — suite de l’intégration

La passe suivante introduit [WCP360 UI Foundation](11-shared-ui-library.md). Sites et Bases de données utilisent désormais sa grille et son Form Builder ; la galerie des composants devient interactive. Le shell et le runtime délèguent les thèmes et toasts à ce socle. Les autres centres restent sur leurs composants historiques.
