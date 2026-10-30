# Environnement de test candidat — File Manager autonome

Cette proposition met en œuvre le dossier propriétaire FM113-01..15 sur la PR #113. Elle ne constitue ni F01, ni contrat WCP360, ni avis indépendant E3. Les réserves Security, DevOps et QA restent à lever sur le **nouveau SHA** et ses preuves. Aucun merge, déploiement ou CODE-BASELINE n'est autorisé par ce document.

## Entrées verrouillées

La source machine est `tests/ci/toolchain.json`. Les digests désignent les manifests Linux amd64 récupérés dans les registres officiels le 30 octobre 2026 ; les tags seuls ne sont pas utilisés pour construire.

| Élément | Candidat |
|---|---|
| PHP | 8.2.35, 8.3.36, 8.4.27 ; images officielles CLI Bookworm par digest |
| Python | CPython 3.11.2 ; python3 3.11.2-1+b1, python3.11 et python3.11-minimal 3.11.2-6+deb12u9, versions assertées |
| Composer | 2.11.1, PHAR vérifié par SHA-256 |
| Node | v22.23.4 linux-x64, archive vérifiée par SHA-256 |
| Playwright | 1.64.1 ; package-lock npm et intégrités, scripts npm désactivés |
| Chromium | 156.0.8078.4, révision 1248 ; image Playwright par digest |
| Paquets système | snapshot Debian et Debian Security 20261029T000000Z, signatures APT conservées |
| Extensions exigées | json, fileinfo, session, ctype, iconv, filter, xml, dom, xmlwriter, libxml, mbstring, phar, tokenizer, zip |

DevOps 6535401769 retient PHP 8.3.36 comme lane initiale obligatoire ; 8.2/8.4 sont des couvertures supplémentaires conservées dans le workflow, sans réduction des checks existants. QA peut exiger la matrice complète.

Les versions réelles PHP/Node/Composer et Chromium sont contrôlées avant un PASS. Toutes les extensions chargées et la version Python sont enregistrées. L’inventaire dpkg complet (noms/versions) est conservé dans packages.json, en plus de son empreinte. `composer check-platform-reqs` vérifie la plateforme réelle, y compris avec l'installation `--no-dev` ; le `config.platform.php` historique n'est pas une preuve de compatibilité.

Les 62 fichiers de l'import initial sont décrits dans `tests/ci/import-baseline.json`. La construction refuse toute modification non déclarée de cet import. Seuls README et les trois harnais HTTP/setup/navigateur sont autorisés à évoluer ici. `SOURCE-PROVENANCE.json` reste le relevé historique des octets du ZIP, distinct des octets normalisés de l'import Git. Les sources produit, le Composer lock et les rapports historiques restent inchangés.

## Exécution et isolation

Le workflow **racine** `.github/workflows/file-manager-standalone.yml` exécute les trois lanes sur une pull request pertinente. Le workflow imbriqué historique n'est pas actif à la racine. Une exécution manuelle sera disponible lorsque ce workflow sera présent sur la branche par défaut.

Le conteneur de tests utilise UID/GID 10001, système de fichiers en lecture seule, aucune capability, `no-new-privileges`, profil seccomp explicite, PID/mémoire/CPU bornés, shm privée et données éphémères sous `/tmp`. Aucun port n'est publié et seul `/evidence` est monté en écriture depuis l'hôte. Les serveurs PHP écoutent 127.0.0.1. Les tests utilisent `--network none` et vérifient que seule l'interface loopback existe. Aucun stockage tenant ni installation XAMPP n'est monté.

Chromium est démarré avec `chromiumSandbox: true` ; sa page `chrome://sandbox` doit confirmer les namespaces et Seccomp-BPF. **Aucun `--no-sandbox`, `--privileged`, `SYS_ADMIN`, `seccomp=unconfined` ou repli automatique.** Si l'hôte refuse les namespaces utilisateur, la lane échoue : un autre environnement doit être proposé et revu, sans contourner silencieusement cette exigence.

Le profil `tests/ci/seccomp.json` dérive du profil Docker Playwright 1.64.1, blob `fddc05fb520affb145404e6f6f647ca96af8087d`, sous Apache-2.0 (licence jointe). Il autorise les appels nécessaires aux namespaces. Modification locale : `clone3` renvoie ENOSYS (38) pour permettre le repli libc vers `clone`, au lieu d'EPERM. Cette modification reste soumise à revue Security/DevOps. Source : https://github.com/microsoft/playwright/blob/v1.64.1/utils/docker/seccomp_profile.json . Guide : https://playwright.dev/docs/docker .

## Réseau, caches et secrets

La préparation d'image passe par un proxy CONNECT TLS à liste fermée, dont les domaines exacts sont dans `tests/ci/network_policy.json`. Une seule classe statique, mcr-single-label, est autorisée par DevOps6535573110 et Security6535584979 pour l'acquisition : exactement un label DNS ASCII valide suivi de .data.mcr.microsoft.com. Les autres domaines restent exacts. Aucun port autre que 443, résolution vers IP non publique ou extension automatique sur redirection n'est accepté. Les destinations Composer dist proviennent des hosts du lock (api.github.com, redirection codeload.github.com). Les registres et CDN déclarés servent uniquement les images épinglées. Les CONNECT réels sont comptés par nom de destination validé. Les tests couvrent suffixe trompeur, label manquant/supplémentaire/invalide, port80, phase audit et résolution privée/mixte ; les probes vérifient TCP direct IPv4/IPv6 et DNS. Une destination nouvelle nécessite un delta revu ; l'échec ne provoque pas de repli réseau.

Sur la VM jetable GitHub uniquement, `network_host.py` démarre le proxy sous UID 19001 distinct, configure Docker daemon et build avec ce proxy, puis interdit les connexions sortantes directes du daemon root et tout forwarding IPv4/IPv6 de conteneur. Le runner GitHub garde sa connexion de contrôle. Les accès au proxy sont limités au loopback et à l'interface Docker locale. Les tests restent `--network none`, sans proxy. Avant l'audit, l'accès au port acquisition est fermé aux conteneurs ; seul le proxy audit (repo.packagist.org et packagist.org) reste accessible. L'audit prouve l'accès HTTPS autorisé, le refus d'un domaine non listé et le blocage TCP/DNS direct ; les tests prouvent aussi le blocage TCP/DNS externe. Les règles sont retirées dans une étape `always()` avant l'export, et la VM est jetable. Les compteurs ne contiennent ni URL, ni headers, ni réponse brute.

Cette politique est une implémentation candidate de FM113-13, soumise à relecture exacte DevOps/Security ; un succès de construction ne remplace pas cette revue. Ne jamais utiliser cet installateur réseau sur un hôte partagé ou de production.

Pas de cache Actions restauré, pas de cache Docker de construction réutilisé (`--no-cache`), pas de cache Composer/npm transmis aux tests. Le cache de téléchargement d'une construction peut être utilisé pour sa seconde installation no-dev, puis supprimé. Un audit Composer connecté séparé interroge les avis de vulnérabilité ; une erreur réseau ou une réponse incomplète échoue. Les tests produit restent hors réseau. Les avis évoluent et sont datés, contrairement au lock.

Le workflow ne reçoit aucun secret métier, token d'installation ou identifiant tenant. Checkout possède seulement `contents: read` et ne conserve pas ses credentials. Les traces de construction contiennent les téléchargements et diagnostics des outils ; aucune configuration utilisateur réelle n'est fournie. Les sorties brutes des tests sont capturées dans deux fichiers temporaires privés distincts (stdout/stderr) et ne sont ni imprimées ni publiées. Les erreurs exportées utilisent des codes fixes. Pas de captures d'écran, JUnit brut, serveur.log, configuration, contenu fichier ou lien de partage dans les artefacts.

## Preuves attendues, sans PASS anticipé

Chaque lane doit produire :

- Identité : SHA candidat, SHA checkout et parents (merge synthétique PR), image ID construit, versions Docker/noyau, verrouillage et empreinte seccomp.
- Résultats : contrôles du harnais (vingt-six tests Python et cinq tests Node de diagnostic de confidentialité/nettoyage et parsing), lint, validation Composer, PHPUnit (au moins 50 tests/122 assertions, aucun skip), setup (5), HTTP (46), HTTP/setup sans dépendances dev (46/5), navigateur (28), sandbox et version navigateur.
- Audit : date, état, nombre de packages affectés/abandonnés ; un audit n'est pas une revue du code.

Les compteurs JUnit proviennent de tous les éléments testcase (y compris les cas directement placés au-dessus de suites imbriquées). Ils sont enregistrés avant les assertions, même si PHPUnit sort en erreur. Les identifiants de cas sont SHA-256 tronqués de classname::name ; aucun message, chemin ou sortie de cas n’est exporté. Le cache PHPUnit est placé sous /tmp/phpunit-cache.

Les sorties sont limitées à des JSON structurés, conservés 30 jours. Seuls ces JSON expurgés reçoivent le mode 0644 pour être lus par le runner ; le umask privé 077 reste appliqué aux données et diagnostics temporaires. COMPOSER_ROOT_VERSION fixe la version du paquet de test à 0.3.0-alpha sans modifier composer.json. Le parseur audit applique le schéma de Composer 2.11.1 (Auditor.php), refuse les réponses incomplètes, avis ignorés, dépôts injoignables et codes de sortie non nuls. Un processus de suite et ses descendants sont arrêtés en fin d'exécution ou au timeout ; les conteneurs sont supprimés après les étapes, et l'hôte est jetable. Aucun PASS de l'archive ne remplit ces exigences.

## Limites et décisions encore ouvertes

Cette recette verrouille ses entrées et trace son image construite ; elle ne promet pas une reconstruction binaire identique. DevOps 6535401769 accepte pour E1 initial le noyau/Docker non figés et l'absence d'image finale publiée, à condition de conserver l'identité du runner (ImageOS/ImageVersion), le noyau, Docker, l'inventaire dpkg complet et l'image ID liés au SHA/run. Une certification stable exige une image OCI retenue par digest ou une décision distincte. Security6535584979 accepte le profil seccomp dérivé pour ce design E1, sous réserve d'attestation effective du navigateur sur le nouveau SHA ; aucune exception --no-sandbox de l'ancien SHA n'est transférée.

Les diagnostics bruts ne sont pas exportés : un échec demande une reproduction isolée ou un diagnostic structuré supplémentaire, jamais la réactivation globale des logs de configuration. La taille du stockage temporaire et les durées sont bornées ; aucun test de charge gigaoctet n'est promis.

LocalBackend reste limité à un propriétaire et une racine privée exclusive. Il n'est pas un adaptateur WCP multitenant. Aucun serveur public, CHOWN privilégié, daemon WCP, IPC, choix de production PHP ou validation F01 ne découle d'une lane verte. L'approbation E2 actualisée des propriétaires et E3 indépendant restent nécessaires selon les gates applicables.

## Reproduction sur exécuteur jetable

Le workflow racine constitue la procédure complète : checkout, réseau restreint, construction, tests, audit, restauration réseau et export. `host.py` exige désormais le reçu `/tmp/fm-egress/gateway` créé par la configuration réseau, sans mode permissif local. Une reproduction hors GitHub doit réimplémenter/faire revoir les mêmes limites et déclarer son identité ; ne pas simuler GITHUB_ACTIONS sur une machine de travail ou de production pour contourner le contrôle d'exécuteur jetable.

## Provenance du delta régional MCR

Le manifeste amd64 `d65108d1a8095d8b0c4a5e32bf1807a40a5ce90ff0c78473e1116b1c243235e8` référence le blob config `d8e19ba36e1441fcd3f8a6d3d84c1eeb713ed5f1f46476918b9c3c1c6e765ead`. Les redirections proviennent de GET HTTPS sur le registre primaire, sans accepter automatiquement la destination : westus observé lors de la préparation locale ; centralus observé job124362403834 ; eastus observé job124362404062 (run41143290937), refusés initialement et conservés comme preuve ; westeurope confirmé par vérification primaire complémentaire du coordinateur sur le même blob. Ces refus et vérifications sont historiques. La politique courante applique la classe régionale statique validée par les deux propriétaires, pas une liste apprise de ces redirections. La redirection Docker observée est production.cloudfront.docker.com. La grammaire est exactement un label ASCII de 1..63 caractères, sans tiret initial/final, avant .data.mcr.microsoft.com ; aucun sous-label supplémentaire ni suffixe approchant.

Les diagnostics bruts de construction sont désormais également privés, car les erreurs du registre peuvent contenir une URL SAS temporaire. Seuls des codes fixes et l'état de construction sont exportés dans build.json. Les rapports navigateur d'échec sont limités à une étape/code allowlistés et un compteur ; aucune exception Chromium brute.

## Limite du modèle de confiance

Le filtrage ne constitue pas une sandbox générique de PR hostile : d'autres processus non-root du runner ne sont pas couverts par le rejet root/Docker. Security6535584979 accepte cette limite uniquement pour le harnais E2 de confiance, sur VM jetable sans secrets métier. Les PNG de tests sont privés et éphémères, supprimés en finally ; ils ne sont jamais exportés. Un PASS navigateur doit aussi enregistrer l'UID Linux du processus browser obtenu via CDP/proc, sa version, Namespace et Seccomp-BPF Yes et 28 contrôles.

Le probe navigateur préalable reste privé : seul existent/exécutable, code ldd, nombre de bibliothèques introuvables, booléen ABI indisponible et métadonnées de lancement fermées (type/catégorie, code de sortie, signal connu) sont exportés. Aucun nom de bibliothèque, chemin, message ldd ou trace de lancement ne l'est. Les assertions sandbox et parcours restent identiques.

Une sonde enfant Python appelle unshare(CLONE_NEWUSER) sous le même UID/non-root/seccomp/AppArmor/no-new-privileges. Elle ne modifie ni profil, ni sysctl, ni setuid ; le namespace éventuel disparaît avec l'enfant. Seuls UID initial, retour/errno, création booléenne et indicateurs AppArmor/userns lisibles sont rapportés. Un refus est une preuve de limite d'environnement, jamais une permission de désactiver la sandbox. Les catégories SUID_HELPER_UNUSABLE/SIGABRT/SIGSEGV/APPARMOR_DENIED proviennent de motifs fixes sans export de message.

L'extraction FATAL est fermée par `chromium_diagnostic_sources.json`, treize composants dont les fichiers ont été consultés dans le tag primaire Chromium156.0.8078.4. Seuls booleans FATAL/CHECK/sandbox, identifiants de composants et numéros de ligne bornés sont exportés. Aucun chemin du message, expression CHECK, valeur, URL, querystring ou ligne brute. Une source inconnue produit unclassified_fatal et reste privée ; tout ajout à cette classification est un delta revu.

Le site ZYGOTE_HOST:251 observe l'arrêt enfant avant le hello, sans identifier sa cause. Les codes de sortie zygote sont donc extraits comme entiers bornés. `base/check.h` du tag primaire confirme que RAW_CHECK émet la condition stringifiée sans emplacement source : six conditions connues de credentials/namespace_sandbox/namespace_utils sont comparées à une table statique et remplacées par des IDs RAW_*. Ni condition ni chemin de cette condition ne sont exportés ; les autres CHECK restent privés. Les CHECK avec emplacement n'exportent que les composants/numéros autorisés.

## SEC-113-CHROOT-01 — activation conditionnelle de design

Security6535962406 autorise explicitement, après RAW_CHROOT_FDINFO du run41146281751, le seul remplacement de includes.caps=[CAP_SYS_CHROOT] par includes={} dans l'unique règle names=[chroot], action SCMP_ACT_ALLOW inchangée. Aucun autre syscall/capacité/argument/profil/montage n'est modifié. L'empreinte précédente reste016e91c692ec343e923510d80bc366259ba2a66b50cea310e5fe5958c38de44a ; l'empreinte candidate est enregistrée par identity.json et le commentaire exact-SHA. Cette disposition est une autorisation de design E1, pas un PASS ni une baseline produit.

Le probe chroot s'exécute avant les suites produit dans deux enfants UID/GID10001 initiaux, CapEff0/nnp1/seccomp2/AppArmor enforcing. Cible unique : nouveau répertoire privé vide dans le tmpfs du conteneur. Hors nouveau namespace, chroot doit échouer ; un succès arrête la suite (OUTER_CHROOT_SUCCEEDED, accord invalidé). Dans un user namespace mappant0 vers10001, l'identité et la capacité relative au namespace sont vérifiées, et chroot doit réussir. Sinon arrêt et retour Security sans escalade. Seuls booléens/codes/errno et identités numériques sont publiés ; aucune cible ni sortie brute. Le contrôle Chromium réel, son UID/version, Namespace/Seccomp Yes et28 assertions restent ensuite obligatoires. Le run antérieur reste FAIL et aucune vérification Security finale n'est déduite de cette activation.
