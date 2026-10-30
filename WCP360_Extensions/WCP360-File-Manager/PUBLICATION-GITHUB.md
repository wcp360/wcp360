# Publication GitHub — File Manager 0.3.0 alpha

Import de l’archive fournie par l’utilisateur : WCP360-File-Manager-Pro-XAMPP-v0.3.0.zip.
SHA-256 : eea5cb00e6a527f2056b9e9ab4d6258d7de3bb5738f759f02b42a80f0b107524.

## Statut
Extension autonome expérimentale publiée pour revue. Ce dépôt ne l’active pas dans WCP360. Aucun changement de CODE-BASELINE, de contrats canoniques, du daemon ou de la PR #108. Aucun service privilégié ni endpoint du panneau n’est activé.

## Contenu
Sources, interfaces, tests, documentation et composer.lock sont importés. vendor/ et le SHA256SUMS du ZIP complet sont exclus. Aucun fichier standalone/var/, configuration locale, compte réel ou donnée client ne figure dans cet import. Le workflow imbriqué .github/workflows/tests.yml reste un exemple du paquet ; GitHub ne l’exécute pas à cet emplacement.
Le README reçoit uniquement un avertissement sur les dépendances absentes de Git. Le reste du contenu importé est identique à l’archive. SOURCE-PROVENANCE.json contient les empreintes des fichiers originaux importés.

## Utilisation des sources
Depuis ce dossier : composer install --no-dev --prefer-dist, puis DEMARRER-WINDOWS.bat sous Windows/XAMPP. Pour le développement et les tests, utiliser composer install avec les dépendances de développement. Ne pas copier une installation contenant des secrets ou des données dans Git.

## Vérifications de cet import
Intégrité SHA-256 du ZIP vérifiée ; chemins relatifs sans traversal et absence de liens symboliques dans l’archive vérifiées ; inventaire des fichiers et contrôle ciblé de motifs de clés privées/tokens effectués. Ce contrôle limité n’est pas un audit de sécurité exhaustif.
PHP n’est pas disponible dans l’environnement de publication : aucun test PHP, HTTP ou navigateur n’a été réexécuté pour cet import. docs/validation/ contient les résultats historiques livrés avec le ZIP, pas des résultats de CI GitHub actuels.

## Limites et handoff
LocalBackend reste mono-propriétaire, réservé au stockage privé exclusivement modifié par l’extension. Ne pas l’utiliser sur des homes tenants modifiables par SSH/FTP/scripts. L’intégration WCP360 exige les contrats typés, les autorisations par opération et l’exécuteur isolé. La chaîne privilégiée canonique distingue wcpd non-root et wcp-privd minimal root.
Les propositions d’intégration de docs/INTEGRATION-WCP360.md restent non canoniques. Les mentions anciennes « pas publié sur GitHub » décrivent la livraison ZIP antérieure ; cet import publie les propositions sans les approuver. La section ancienne sur CHMOD non actif concerne l’intégration ; le mode autonome 0.3.0 possède CHMOD sous l’UID courant.
Revue attendue avant intégration : API/IPC, Security, Database, QA, DevOps, Design et MASTER selon docs/INTEGRATION-WCP360.md. Pas d’approbation indépendante revendiquée, pas de fusion automatique, pas de release stable.
