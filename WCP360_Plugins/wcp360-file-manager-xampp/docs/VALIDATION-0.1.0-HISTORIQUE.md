# Validation — 30 octobre 2026

Cible : PHP 8.4 / Symfony 7.4. Runtime effectivement disponible : PHP 8.3.6 CLI sous Linux. Les dépendances Symfony sont contraintes à la branche 7.4 ; Composer a été exécuté avec `--ignore-platform-req=php` uniquement dans l’environnement de test PHP 8.3. **Ne pas utiliser ce contournement pour déployer.** La validation sous PHP 8.4, dont le setup exige la version, reste à réaliser ; workflow CI fourni, non exécuté sur GitHub.

| Contrôle | Résultat observé |
|---|---|
| PHPUnit stockage / chemins / identité / conflits / corbeille | PASS — 24 tests, 44 assertions |
| Intégration réelle HTTP avec kernel Symfony, serveur PHP et cookies | PASS — 20 contrôles |
| Syntaxe des sources PHP | PASS |
| Syntaxe JavaScript (`node --check`) | PASS |
| Validation Composer stricte | PASS |
| Audit des dépendances Composer au moment du contrôle | Aucune alerte ni dépendance abandonnée retournée |
| Navigateur / responsive / interactions visuelles | NON VALIDÉ — téléchargement Chromium indisponible (archive reçue inutilisable) |
| Installation et suite sur PHP 8.4 | À EXÉCUTER |
| Tests multi-tenant / daemon IPC / races hostiles | BLOQUÉS — adaptateur non implémenté, hors backend local |
| Audit indépendant de sécurité | NON RÉALISÉ |

Les tests HTTP couvrent login, accès anonyme, identité, CSRF, création, lecture, sauvegarde, conflit de révision, traversal, JSON invalide, méthodes HTTP, téléchargement, headers, upload multipart, corbeille, restauration, asset JavaScript, logout et révocation de session.

Les preuves de la dernière exécution sont dans `validation/`. Les échecs intermédiaires d’environnement (installation PHP, dépendances temporairement incohérentes et fixture de hash de mot de passe recalculée par requête) ont été corrigés avant les résultats PASS. Aucun résultat de navigateur ou de PHP 8.4 n’est déduit des tests HTTP.

L’audit Composer ne remplace pas une revue de sécurité du code et ne garantit pas l’absence de vulnérabilité. Cette livraison est une alpha à tester, pas une release WCP360 approuvée.
