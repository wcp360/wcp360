# Validation 0.3.0 Pro — 30 octobre 2026

Runtime réellement exécuté : PHP 8.3.6 / Linux, Symfony 7.4, Chromium via Playwright. PHP 8.2 et 8.4 figurent dans le workflow CI fourni, non exécuté sur GitHub ici. Aucune exécution native Windows ; la logique CHMOD est explicitement désactivée côté serveur et UI sur Windows.

| Suite | Résultat |
|---|---|
| PHPUnit | 50 tests / 122 assertions — PASS |
| HTTP réel | 46 contrôles — PASS |
| Setup | 5 contrôles — PASS |
| Navigateur | 28 contrôles — PASS |
| Syntaxe PHP / JS | PASS |
| Inspection visuelle | Login, workspace et Options desktop ; workspace et Options mobiles 390 px |

Les nouveautés testées incluent CHMOD effectif et refus des modes dangereux/non-admin, quota avant écriture, versions et restauration avec conflit, rétention à 20 versions, recherche sans fuite via symlink, verrou bloquant un autre éditeur et une opération parent, audit sans contenu/credentials, contenu d’un ZIP, partage immuable avec mot de passe, révocation, cinq téléchargements maximum, throttling des essais et purge de corbeille réservée à l’administrateur.
Le navigateur a exercé les parcours Options correspondants, téléchargé un snapshot de dossier, utilisé un contexte anonyme pour le partage et tenté un save concurrent sans token. Aucun échec JavaScript ni débordement du viewport mobile constaté dans ce parcours.
Les tests ne certifient pas NTFS, tous les navigateurs, l’accessibilité complète, les pannes système, les rollback CHMOD partiellement empêchés par l’OS, un quota Linux réel ou une exposition multi-tenant. Aucun audit indépendant de sécurité n’a été réalisé.
Preuves machine dans validation/. Les captures n’utilisent que des fixtures temporaires et ne sont pas incluses dans le ZIP.
