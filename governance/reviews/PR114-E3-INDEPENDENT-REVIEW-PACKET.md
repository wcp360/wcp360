# WCP360 — PR #114 — dossier de revue indépendante E3

Statut : **PRÊT À SOUMETTRE À UN RELECTEUR — E3 NON EFFECTUÉE**.
Préparé le 2026-10-30 par SOURCE CODE ; classement G1 / collecte E2 du même auteur.
Ce document prépare une revue R1 des tests et de leurs preuves. Il ne valide ni cette PR ni sa dépendance #108.

## Identité et dépendance

| Élément | Valeur figée |
|---|---|
| PR | [#114](https://github.com/yyogas/wcp360/pull/114), ouverte, draft |
| Branche | `test/sc-m01-cli-process-contract` |
| SHA à relire | `462792110330f00b2a1911dd03175e8257379b3a` |
| Parent et base PR | `71deefa2976a7005983d410907dc2050287be05f`, branche de #108 |
| Arbre du candidat | `139e7f60d7459538aec08433c75186e210d405c9` |
| Merge de test | `b00103cd018006a492210c67847e29c3b0eeb8b0` |
| Classe | R1 — extension du harness et de la collecte CI de la fondation inerte |

La PR ajoute des assertions sur le vrai processus CLI ; elle ne modifie aucun fichier Go runtime ni contrat canonique. Les conclusions des propriétaires de #108 restent liées au SHA #108. Aucun avis GitHub de revue ou commentaire propriétaire n'était enregistré sur #114 lors du relevé ; les avis applicables Engineering/DevOps/Security restent à obtenir. QA doit confirmer la portée de ces preuves dans les familles bootstrap/build/unit applicables.

Lire d'abord le [dossier #108](PR108-E3-INDEPENDENT-REVIEW-PACKET.md), la [politique de revue](../REVIEW-MERGE-POLICY.md), le [manifeste](../../manifests/WCP360-BM-0001.md) et le [snapshot commun](evidence/PR108-PR114-E3-SNAPSHOT-2026-10-30.json). Le GO existant reste limité au daemon inerte ; aucune extension de runtime n'est accordée ici.

## Diff exact — cinq fichiers

| Fichier | Blob au candidat #114 |
|---|---|
| `.github/workflows/sc-m01-daemon-foundation.yml` | `c2d66367fbdf63a49a612d6c6779666e839583d1` |
| `SOURCE/daemon/README.md` | `afe13c56c3a62af07372e8c8e6c350aa2709525c` |
| `SOURCE/daemon/tools/check-cli.sh` | `15e2cce2a597506eb8332ccdbdd2495ded284ab8` |
| `SOURCE/daemon/tools/check-negative.sh` | `3d837a94371b7271c7594988c1aea2359822c8b4` |
| `SOURCE/daemon/tools/check.sh` | `4bdaa313ab5d1791674449fd361b8a30cb257579` |

La ligne modifiée dans le workflow ajoute uniquement l'affichage de `cli-process.tsv`. Les assertions sont intégrées dans le gate `daemon-smoke` existant ; l'inventaire des 13 gates n'est pas augmenté.

## Exécution E1 constatée

[Run 41133175259](https://github.com/yyogas/wcp360/actions/runs/41133175259), [job 124329033296](https://github.com/yyogas/wcp360/actions/runs/41133175259/job/124329033296), tentative 1, pull_request : **SUCCESS**.

Le run est associé au head `4627921...`, mais `provenance.txt` et le log indiquent le commit réellement exécuté `b00103c...`. Les parents de ce merge sont le head #108 et le head #114. Son arbre est **exactement identique** à l'arbre du candidat #114 : `139e7f60d7459538aec08433c75186e210d405c9`. Cette égalité est une comparaison de contenu Git ; ce n'est ni une revue E3 ni une preuve sur un futur main.

Environnement : Debian 13.7 linux/amd64, Go 1.27.3, UID/GID 10001, mêmes pins et isolement que #108. Aucun test produit n'a été relancé pour rédiger ce dossier.

| Preuve réellement exécutée | Résultat |
|---|---|
| Invocations valides : sans argument, --help, -h, --version, --check | 5/5 ; code 0 et octets stdout/stderr exacts |
| Rejets : vide, serve, listen, config, token, aide+extra, options combinées, --, mauvaise casse | 9/9 ; code 2, stdout vide et diagnostic fixe sans écho |
| Échec stdout sur les cinq commandes valides | 5/5 ; code 1, stderr vide |
| Échec stderr sur deux formes d'entrée rejetée | 2/2 ; code 1, stdout vide |
| Ensemble des cas du vrai processus | **21/21** |
| Tests unitaires CLI hérités | cinq tests de premier niveau + quatre sous-tests aide/version, PASS |
| Mutations de harness | **7/7** rejetées au gate attendu |
| Inventaire nominal | **13/13 gates PASS** |
| Scan govulncheck v1.9.0 | `No vulnerabilities found.` pour cette exécution |

La mutation ajoutée enlève `os.Exit` dans une copie jetable du wrapper. Les tests de `cli.Run` continuent de passer, mais l'exécutable renvoie à tort 0 pour une entrée invalide : le gate `daemon-smoke` échoue comme attendu. Le relevé du rejet figure dans `negative-gates.tsv`, séparé des résultats du candidat.

`/dev/full` est vérifié comme device qui refuse une écriture avant d'être utilisé comme fixture ENOSPC. Les assertions comparent les octets des fichiers, sans suppression implicite des nouvelles lignes. Les entrées sont synthétiques ; aucun secret réel n'est requis.

Binaire SHA-256 : `9bbb7a499d77d4bd77e12554e915064815fbfa10b2c020e9763f93621f1f5446`, identique à #108 et au rebuild frais de ce run. La couverture unitaire du package CLI reste 100 %, celle du wrapper 0 % ; les cas processus sont une preuve distincte. Les 21 cas ne correspondent pas aux 199 tests d'acceptation produit, à des tests IPC/DB/session ou à un race test.

### Conservation

Artifact `13013388542`, nom `sc-m01-daemon-b00103cd018006a492210c67847e29c3b0eeb8b0-1`, 2 547 396 octets.
Digest annoncé par Actions : `sha256:24f0909d918391a088860ecb1ea485edd407388c8fd855f5a416950b468d4d8e`.
Expiration UTC : **2026-11-29 21:45:25**.

Les logs et métadonnées ont été relus ; le ZIP n'a pas été téléchargé ni rehashé. Le snapshot commun conserve les 21 lignes de cas, sept mutations et 13 gates, ainsi que les identités SHA/run/job et digest. Il ne conserve pas les octets de l'artifact.

## Travail demandé au relecteur indépendant

Checklist à remplir par le relecteur, sans conclusion préattribuée :

- [ ] Vérifier identité/indépendance et la dépendance exacte #108 ; ne pas réutiliser automatiquement ses approbations.
- [ ] Comparer les cinq blobs et confirmer l'absence de changement Go, de contrat canonique et de capacité runtime.
- [ ] Vérifier que les assertions testent bien le binaire compilé, pas une réimplémentation de `cli.Run` ou un stub.
- [ ] Relire les 21 cas, les octets attendus, les flux, le statut du processus et l'absence d'écho ; vérifier que toute assertion ratée empêche PASS.
- [ ] Vérifier le contrôle positif de la fixture /dev/full et les deux voies stdout/stderr, avec sorties et codes réellement observés.
- [ ] Vérifier que la mutation os.Exit est effective, isolée dans une copie jetable, puis rejetée exactement à daemon-smoke ; une simple erreur de build ne doit pas suffire.
- [ ] Vérifier la conservation séparée des échecs intentionnels, la preuve du nombre de cas et le maintien de l'inventaire nominal 13/13.
- [ ] Vérifier les impacts du nouveau sous-script sur montages, permissions, lecture seule, réseau et timeouts du builder ; le scanner n'obtient aucune nouvelle permission.
- [ ] Vérifier la liaison head/merge/arbre/source archive, la rétention et les limites des preuves, puis examiner les éventuelles lacunes restantes.
- [ ] Consigner verdict, constats, réserves et dépendances non examinées ; demander les avis propriétaires applicables à ce SHA.

Reproduction : utiliser le workflow épinglé au candidat dans son environnement canonique isolé. Le gate offline construit d'abord le binaire puis appelle `sh SOURCE/daemon/tools/check-cli.sh` ; `check-negative.sh` exécute ensuite les sept mutations. Le scanner utilise son conteneur/réseau séparé. Ne pas remplacer Debian/Go par un environnement local différent tout en conservant une conclusion de gate canonique.

## Retour et intégration

Utiliser [le modèle de relevé E3](SC-M01-E3-REVIEW-RECORD-TEMPLATE.md), avec un verdict distinct pour #114.

**État actuel : E3 MISSING ; avis spécialisés de #114 PENDING ; MERGE HOLD ; CODE-BASELINE NONE.**

Ordre proposé : terminer la revue de #108 inchangée, traiter ses corrections et son intégration approuvée, puis préparer le retarget de #114 vers le main obtenu. Vérifier à nouveau le diff, l'ascendance et la CI d'intégration. Si un squash/rebase/cherry-pick ou une correction produit un nouveau SHA, consigner ce SHA et obtenir la revalidation de revue exigée ; ne jamais transférer silencieusement un verdict. Merger #114 dans la branche #108 avant cette étape modifierait le candidat #108.
