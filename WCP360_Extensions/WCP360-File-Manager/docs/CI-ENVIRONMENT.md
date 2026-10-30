# Environnement de test candidat — File Manager autonome

Cette proposition met en œuvre le dossier propriétaire FM113-01..15 sur la PR #113. Elle ne constitue ni F01, ni contrat WCP360, ni avis indépendant E3. Les réserves Security, DevOps et QA restent à lever sur le **nouveau SHA** et ses preuves. Aucun merge, déploiement ou CODE-BASELINE n'est autorisé par ce document.

## Entrées verrouillées

La source machine est `tests/ci/toolchain.json`. Les digests désignent les manifests Linux amd64 récupérés dans les registres officiels le 30 octobre 2026 ; les tags seuls ne sont pas utilisés pour construire.

| Élément | Candidat |
|---|---|
| PHP | 8.2.35, 8.3.36, 8.4.27 ; images officielles CLI Bookworm par digest |
| Composer | 2.11.1, PHAR vérifié par SHA-256 |
| Node | v22.23.4 linux-x64, archive vérifiée par SHA-256 |
| Playwright | 1.64.1 ; package-lock npm et intégrités, scripts npm désactivés |
| Chromium | 156.0.8078.4, révision 1248 ; image Playwright par digest |
| Paquets système | snapshot Debian et Debian Security 20261029T000000Z, signatures APT conservées |
| Extensions exigées | json, fileinfo, session, ctype, iconv, filter, xml, dom, xmlwriter, libxml, mbstring, phar, tokenizer, zip |

Les versions réelles PHP/Node/Composer et Chromium sont contrôlées avant un PASS. Toutes les extensions chargées et la version Python sont enregistrées. `composer check-platform-reqs` vérifie la plateforme réelle, y compris avec l'installation `--no-dev` ; le `config.platform.php` historique n'est pas une preuve de compatibilité.

Les 62 fichiers de l'import initial sont décrits dans `tests/ci/import-baseline.json`. La construction refuse toute modification non déclarée de cet import. Seuls README et les trois harnais HTTP/setup/navigateur sont autorisés à évoluer ici. `SOURCE-PROVENANCE.json` reste le relevé historique des octets du ZIP, distinct des octets normalisés de l'import Git. Les sources produit, le Composer lock et les rapports historiques restent inchangés.

## Exécution et isolation

Le workflow **racine** `.github/workflows/file-manager-standalone.yml` exécute les trois lanes sur une pull request pertinente. Le workflow imbriqué historique n'est pas actif à la racine. Une exécution manuelle sera disponible lorsque ce workflow sera présent sur la branche par défaut.

Le conteneur de tests utilise UID/GID 10001, système de fichiers en lecture seule, aucune capability, `no-new-privileges`, profil seccomp explicite, PID/mémoire/CPU bornés, shm privée et données éphémères sous `/tmp`. Aucun port n'est publié et seul `/evidence` est monté en écriture depuis l'hôte. Les serveurs PHP écoutent 127.0.0.1. Les tests utilisent `--network none` et vérifient que seule l'interface loopback existe. Aucun stockage tenant ni installation XAMPP n'est monté.

Chromium est démarré avec `chromiumSandbox: true` ; sa page `chrome://sandbox` doit confirmer les namespaces et Seccomp-BPF. **Aucun `--no-sandbox`, `--privileged`, `SYS_ADMIN`, `seccomp=unconfined` ou repli automatique.** Si l'hôte refuse les namespaces utilisateur, la lane échoue : un autre environnement doit être proposé et revu, sans contourner silencieusement cette exigence.

Le profil `tests/ci/seccomp.json` dérive du profil Docker Playwright 1.64.1, blob `fddc05fb520affb145404e6f6f647ca96af8087d`, sous Apache-2.0 (licence jointe). Il autorise les appels nécessaires aux namespaces. Modification locale : `clone3` renvoie ENOSYS (38) pour permettre le repli libc vers `clone`, au lieu d'EPERM. Cette modification reste soumise à revue Security/DevOps. Source : https://github.com/microsoft/playwright/blob/v1.64.1/utils/docker/seccomp_profile.json . Guide : https://playwright.dev/docs/docker .

## Réseau, caches et secrets

La préparation d'image est connectée : registres Docker/Microsoft, snapshot.debian.org, nodejs.org, getcomposer.org, registry.npmjs.org, Packagist/GitHub et leurs endpoints de distribution. TLS et empreintes disponibles sont vérifiés ; Composer utilise le lock sans scripts/plugins. Il n'y a **pas de filtre réseau par domaine** dans ce candidat. Cette limite demande une disposition DevOps/Security explicite si une allowlist réseau est exigée.

Pas de cache Actions restauré, pas de cache Docker de construction réutilisé (`--no-cache`), pas de cache Composer/npm transmis aux tests. Le cache de téléchargement d'une construction peut être utilisé pour sa seconde installation no-dev, puis supprimé. Un audit Composer connecté séparé interroge les avis de vulnérabilité ; une erreur réseau ou une réponse incomplète échoue. Les tests produit restent hors réseau. Les avis évoluent et sont datés, contrairement au lock.

Le workflow ne reçoit aucun secret métier, token d'installation ou identifiant tenant. Checkout possède seulement `contents: read` et ne conserve pas ses credentials. Les traces de construction contiennent les téléchargements et diagnostics des outils ; aucune configuration utilisateur réelle n'est fournie. Les sorties brutes des tests sont capturées dans deux fichiers temporaires privés distincts (stdout/stderr) et ne sont ni imprimées ni publiées. Les erreurs exportées utilisent des codes fixes. Pas de captures d'écran, JUnit brut, serveur.log, configuration, contenu fichier ou lien de partage dans les artefacts.

## Preuves attendues, sans PASS anticipé

Chaque lane doit produire :

- Identité : SHA candidat, SHA checkout et parents (merge synthétique PR), image ID construit, versions Docker/noyau, verrouillage et empreinte seccomp.
- Résultats : contrôles du harnais (onze tests de confidentialité/nettoyage et parsing), lint, validation Composer, PHPUnit (au moins 50 tests/122 assertions, aucun skip), setup (5), HTTP (46), HTTP/setup sans dépendances dev (46/5), navigateur (28), sandbox et version navigateur.
- Audit : date, état, nombre de packages affectés/abandonnés ; un audit n'est pas une revue du code.

Les sorties sont limitées à des JSON structurés, conservés 30 jours. Seuls ces JSON expurgés reçoivent le mode 0644 pour être lus par le runner ; le umask privé 077 reste appliqué aux données et diagnostics temporaires. COMPOSER_ROOT_VERSION fixe la version du paquet de test à 0.3.0-alpha sans modifier composer.json. Le parseur audit applique le schéma de Composer 2.11.1 (Auditor.php), refuse les réponses incomplètes, avis ignorés, dépôts injoignables et codes de sortie non nuls. Un processus de suite et ses descendants sont arrêtés en fin d'exécution ou au timeout ; les conteneurs sont supprimés après les étapes, et l'hôte est jetable. Aucun PASS de l'archive ne remplit ces exigences.

## Limites et décisions encore ouvertes

Cette recette verrouille ses entrées et trace son image construite ; elle ne promet pas une reconstruction binaire identique. Le noyau, Docker et l'image de VM `ubuntu-22.04` sont gérés par GitHub et enregistrés, pas immuables. Il faut encore décider si DevOps exige une VM figée et une image finale publiée par digest avec rétention ; ce candidat ne publie aucune image.

Les diagnostics bruts ne sont pas exportés : un échec demande une reproduction isolée ou un diagnostic structuré supplémentaire, jamais la réactivation globale des logs de configuration. La taille du stockage temporaire et les durées sont bornées ; aucun test de charge gigaoctet n'est promis.

LocalBackend reste limité à un propriétaire et une racine privée exclusive. Il n'est pas un adaptateur WCP multitenant. Aucun serveur public, CHOWN privilégié, daemon WCP, IPC, choix de production PHP ou validation F01 ne découle d'une lane verte. L'approbation E2 actualisée des propriétaires et E3 indépendant restent nécessaires selon les gates applicables.

## Reproduction locale Linux avec Docker

Depuis un checkout du SHA à tester, avec Docker Linux amd64 et Python 3 :

```sh
export FM_PHP_LANE=8.4
export FM_CANDIDATE_SHA="$(git rev-parse HEAD)"
export FM_EVIDENCE="$(mktemp -d)"
python3 WCP360_Extensions/WCP360-File-Manager/tests/ci/host.py build
python3 WCP360_Extensions/WCP360-File-Manager/tests/ci/host.py test
python3 WCP360_Extensions/WCP360-File-Manager/tests/ci/host.py audit
```

Ne pas utiliser de répertoire de données réel comme FM_EVIDENCE. Répéter avec 8.2 et 8.3 pour la matrice complète. Les commandes Docker agissent sur un hôte de test jetable ; ne pas les exécuter sur un VPS client partagé.
