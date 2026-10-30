> **CI Linux candidate :** voir [docs/CI-ENVIRONMENT.md](docs/CI-ENVIRONMENT.md). Les résultats du ZIP restent historiques ; seuls les nouveaux runs liés au SHA testé constituent des preuves actuelles. Aucun choix de cette CI ne valide F01 ou une intégration multitenant.

> **Import GitHub des sources 0.3.0 alpha.** Ce dossier ne contient pas `vendor/`. Depuis ce dossier, exécuter `composer install --no-dev --prefer-dist` avant le lancement XAMPP. Les instructions sans Composer ci-dessous concernent uniquement le ZIP original avec dépendances. Voir `PUBLICATION-GITHUB.md`.

# WCP360 File Manager — 0.3.0 alpha Pro

Gestion quotidienne et outils avancés dans le bouton Options. Application autonome et paquet Symfony ; PHP 8.2+ pour le test local, PHP 8.4 cible WCP360. Dépendances de fonctionnement incluses. Intégration daemon WCP360 non active.

## Démarrage Windows / XAMPP

1. Extraire entièrement le ZIP dans un **nouveau dossier hors htdocs**.
2. Ouvrir `DEMARRER-WINDOWS.bat`.
3. Au premier lancement, choisir l’identifiant et conserver le mot de passe généré.
4. Ouvrir http://127.0.0.1:8088/ et garder la fenêtre du serveur ouverte.

Apache, MySQL et Composer ne sont pas nécessaires pour ce test. Le lanceur utilise PHP de XAMPP et vérifie la configuration. Lire `LIRE-MOI-WINDOWS.txt`. Pour conserver une installation 0.1.1 ou 0.2.0, lire `MISE-A-JOUR.txt` avant de lancer le nouveau paquet. Aucun compte prédéfini n’est distribué.

## Fonctions disponibles

| Domaine | Socle conservé en 0.3.0 |
|---|---|
| Navigation | Arborescence extensible, fil d’Ariane, dossiers paginés, vues liste et grille |
| Actualisation | Bouton manuel et interrogation automatique toutes les 15 secondes, désactivable ; pas de notifications temps réel WebSocket |
| Fichiers cachés | Afficher/masquer les noms commençant par un point, masqués par défaut |
| Sélection | Cases individuelles, sélection de la page, Maj+clic, Ctrl+A dans l’espace fichiers |
| Presse-papiers | Copier, couper, coller entre dossiers ; presse-papiers interne à l’onglet, aucune lecture du presse-papiers système |
| Opérations | Créer, renommer, déplacer, dupliquer un fichier ou un dossier ; glisser-déposer interne vers un dossier, le fil d’Ariane ou l’arborescence |
| Actions groupées | Copier, déplacer, dupliquer, mettre à la corbeille et restaurer ; bilan détaillé par élément |
| Corbeille | Fichiers et dossiers non vides, restauration du contenu au chemin d’origine, sans écrasement |
| Propriétés | Nom, chemin relatif, taille de fichier, date, permissions, UID/GID sur Linux ; propriétaires non applicables sur Windows |
| Édition | Texte UTF-8 jusqu’à 1 Mio, Ctrl+S, détection de conflit, avertissement avant fermeture ; pas encore Monaco |
| Transferts | Import d’un fichier et téléchargement direct, jusqu’à 20 Mio |
| Interface | Français, menu contextuel et bouton ⋯ pour le tactile, affichage mobile |

Les sélections portent sur la page visible (100 éléments) ; changer de dossier ou de filtre les efface. Le presse-papiers reste disponible pendant la navigation, jusqu’au rechargement de la page. Un collage au même emplacement refuse le conflit ; utiliser **Dupliquer** pour créer `nom - copie.ext`. Les noms existants ne sont jamais remplacés automatiquement.
Les recherches portent seulement sur le dossier courant. La sélection multiple ne produit pas encore d’archive de téléchargement.

## Bornes des opérations

- Copie récursive et mise à la corbeille d’un dossier : 2 000 éléments, 32 niveaux, 100 Mio cumulés et 20 Mio par fichier.
- Liste : maximum 10 000 entrées par dossier ; 100 par page.
- Corbeille : 1 000 entrées consultables ; purge définitive manuelle par administrateur ; aucune purge automatique de corbeille.
- Copie : prévalidation, staging privé, finalisation par renommage, nettoyage du staging sur échec. Nouvelles permissions privées 0600/0700 ; pas de préservation des ACL, xattrs ou propriétaires.
- Les actions groupées sont **séquentielles et indépendantes** : les réussites restent appliquées lorsqu’un autre élément échoue. Le bilan permet de reprendre les échecs. Aucune transaction globale ni job durable.
- Les opérations sur le stockage restent synchrones et sérialisées par verrou. Les grandes arborescences nécessiteront les jobs de l’étape suivante.

## Sécurité et périmètre

Stockage local privé, mono-propriétaire et exclusivement modifié par ce module. Aucun autre processus hostile ne doit pouvoir modifier cette arborescence. Ne pas utiliser ce backend sur les homes webhosting accessibles par SSH, FTP, scripts de tenants ou cron.
Les contrôles PHP ne constituent pas un chroot et n’éliminent pas les courses TOCTOU face à un acteur externe. Lire `docs/SECURITY.md` et `docs/INTEGRATION-WCP360.md`.
Aucune exécution de contenu utilisateur, commande shell, modification CHOWN ou connexion daemon n’est ajoutée. CHMOD utilise la fonction PHP sous l’UID courant, sans élévation de privilèges. Le BAT lance seulement le setup et le serveur PHP sur loopback. Les services internes privés de WCP360 ne sont pas inclus.

## Validation et développement

Résultats dans `docs/VALIDATION.md`. Tests exécutés sous Linux/PHP 8.3 ; navigateur Chromium desktop et viewport mobile. L’exécution native Windows de cette nouvelle version reste à confirmer, le lanceur existant est conservé.

Pour les développeurs (Composer réinstalle les dépendances de test non incluses dans le ZIP) :

```bash
composer install
composer test
python3 tests/http_smoke.py
python3 tests/setup_smoke.py
# Installer Playwright dans l’environnement de test puis :
node tests/browser.cjs
```

`FM_PLAYWRIGHT_MODULE` permet de fournir le chemin du module Playwright installé ailleurs. Le test navigateur crée ses propres données temporaires sous `/tmp` et ne vise aucune installation existante. Workflow PHP 8.2/8.3/8.4 fourni ; non exécuté ici sur GitHub.

## Outils Pro livrés en 0.3.0

Voir `docs/PRO-TOOLS.md` pour le détail et les contrats.

- Interface compacte : accès direct aux fichiers, login redessiné, outils dans Options.
- CHMOD Linux réservé à l’administrateur, récursif avec filtre fichiers/dossiers et contrôle des modes.
- Espace utilisé, analyse des gros dossiers/fichiers du dossier courant, quota applicatif et alerte à 85 %.
- Journal : acteur, IP, chemin, opération, résultat et request ID.
- Recherche de contenu UTF-8, avec chemins et numéros de ligne, requête en POST pour ne pas mettre le texte recherché dans l’URL.
- Historique avant sauvegarde et restauration : 20 versions par chemin.
- Instantané ZIP de fichier/dossier avec téléchargement, si extension PHP zip disponible.
- Liens sur copie figée avec mot de passe, expiration, révocation et limite de 5 téléchargements.
- Verrous d’édition inter-onglets, jeton mémoire et expiration à 15 minutes.
- Suppression définitive de la sélection dans la corbeille, par menu contextuel, avec confirmation et rôle administrateur.

## Étapes non livrées

Monaco, extraction ZIP, TAR/GZ, archives de plusieurs sélections indépendantes, uploads de dossiers et reprenables, aperçus multimédias, Git/webhooks, symlinks, CHOWN, quotas système Linux, comptes collaborateurs, jobs persistants, sauvegardes hors serveur et terminal/SSH. Ces fonctions ne sont pas simulées par des boutons inactifs.
