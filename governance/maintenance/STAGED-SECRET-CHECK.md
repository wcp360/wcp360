# WCP360 — Détection ciblée avant commit et correction du périmètre

Date : 2026-10-02. Statut : **CORRECTIF / EXTENSION CANDIDATE — REVIEW**. PR #42 ; suivi #41, volet AUDIT-WCP-0109. Base du lot : `6df0afc6f12931d039e75ba13b7494d54b0523c9`.

Ce lot étend le contrôleur existant, sans ajouter de moteur de sécurité applicatif. Aucun contrat propriétaire, schéma DB, code sous src/, migration, service ou baseline n'est modifié. Il ne remplace pas les exports #6/#7/#9. SC-M01 reste PAUSED, SC-M02 NOT AUTHORIZED, CODE-BASELINE NONE.

## 1. Défaut reproduit et corrigé

Diagnostic local `PR42-SECRET-SUBDIR-01` : le scanner appelait `git ls-tree` depuis le répertoire fourni, sans `--full-tree`. Dans un dépôt synthétique de deux fichiers, un secret factice à la racine était détecté depuis la racine (2 fichiers, 1 constat), mais pas depuis son sous-dossier (1 fichier, 0 constat), pour le même commit. Ce résultat avant correction a été réellement exécuté ; aucune fuite réelle n'est alléguée.

La lecture d'un commit utilise désormais `--full-tree`. Le mode index utilise `--full-name` et le pathspec ancré `:/`. Deux tests dédiés vérifient que les fichiers hors du sous-dossier ne sont plus omis dans chacun de ces modes. Aucune nouvelle signature de secret n'est ajoutée : il s'agit d'une correction du périmètre inspecté.

## 2. Fonctionnalité avant commit

`check_repository_secrets.py --staged` inspecte le contenu exact enregistré dans l'index Git, pas les fichiers actuellement affichés sur disque. Le contrôle peut ainsi signaler un secret encore indexé même si la copie de travail a été nettoyée sans nouvel ajout à Git. À l'inverse, une modification non indexée n'est pas présentée comme faisant partie de l'index.

Le mode fonctionne aussi avant le premier commit. Les suppressions indexées ne sont pas analysées comme des fichiers encore présents ; cela ne nettoie ni ne certifie l'historique antérieur. Les mêmes règles de contenu et de chemins sensibles, plafonds, codes de sortie et sorties sans extraits restent applicables.

Les métadonnées d'index sont lues à nouveau après le scan. Une différence produit `INDEX_CHANGED_DURING_SCAN`, pas un résultat partiel rassurant. Cette double lecture **n'est pas un verrou** : une modification après le contrôle, ou un changement puis retour à l'identique entre les lectures, n'est pas exclu. Aucun protocole transactionnel système n'est prétendu.

## 3. Hook opt-in, testé dans des dépôts jetables

Le fichier exécutable `.githooks/pre-commit` appelle le mode index. Il ne s'installe pas automatiquement, ne remplace pas un hook existant et ne modifie pas la configuration Git du poste utilisateur.

Git peut préparer un index distinct pour un commit partiel. Le hook transmet alors explicitement `GIT_INDEX_FILE` à `--index-file`. Le contrôleur refuse d'ignorer silencieusement un index alternatif hérité. Un chemin explicite absent, non régulier ou pointant directement vers un lien symbolique est refusé. L'environnement Git général reste filtré ; les sources ne sont pas exécutées et aucun accès réseau n'est demandé par le scanner.

Les tests exécutent de vrais `git commit` locaux : commit propre accepté ; secret rejeté sans création de commit ; commit partiel excluant un autre fichier indexé ; secret dans le fichier sélectionné d'un commit partiel ; secret d'une modification suivie avec `commit --all` ; échec si le contrôleur manque. Ces fixtures ne contiennent que des chaînes synthétiques.

**Installation actuelle : non effectuée sur le poste utilisateur.** La présence d'un hook versionné ne l'active pas dans chaque clone. Son adoption relève de la revue Engineering/DevOps et doit préserver les hooks préexistants. Un hook local est contournable et ne s'applique pas aux commits créés directement par API/GitHub ; il ne remplace donc pas le contrôle CI, les protections de branche ou un scanner spécialisé.

## 4. Interface et compatibilité de l'outillage

```sh
# État committé, toujours accepté comme auparavant
python3 -I tools/repository/check_repository_secrets.py --ref HEAD
# Prochain contenu préparé dans l'index, sans créer de commit
python3 -I tools/repository/check_repository_secrets.py --staged
# Vérification des scénarios index/hook dans des fixtures isolées
python3 -I tools/repository/test_staged_repository_secrets.py
```

`--ref` et `--staged` sont mutuellement exclusifs. `--index-file` est réservé au mode index. Aucun fichier n'est ajouté à l'index par le contrôleur ; ni staging, ni commit, ni mutation du working tree ne sont effectués par le scan. Les écritures des scénarios de test sont limitées à leurs dépôts temporaires.

Le rapport de maintenance passe explicitement à `WCP360-REPOSITORY-SECRET-CHECK/2` : il ajoute `snapshot_kind`, `index_snapshot_sha256` et `index_stable_at_recheck`. `commit` est nul en mode index, car un index n'est pas un commit. En mode commit, les champs d'index sont nuls. C'est le format d'un outil local de dépôt, **pas** une modification du versioning des API/IPC WCP360. Les rapports historiques `/1` restent des preuves de leurs exécutions d'origine.

## 5. Validation et limites

**35 nouveaux tests** ont été exécutés en local avec succès avant publication : 29 cas index/périmètre et 6 cas hook. Les 37 tests existants ne sont pas modifiés ; leur réexécution avec ce lot est prévue dans le job CI existant, suivie des 35 nouveaux tests puis du scan du commit réellement extrait. Une présence de YAML ne vaut pas résultat : l'URL et le SHA du run sont consignés après relecture de l'exécution.

Les nouveaux cas couvrent notamment le staging partiel, les fichiers ignorés forcés, les conflits d'index, blobs absents, symlinks et sous-modules, noms non UTF-8, octets binaires, index alternatif, limites, relecture concurrente, indicateurs assume-unchanged/skip-worktree et absence de mutation de l'index et de la configuration.

Les protections restent ciblées : signatures limitées, pas d'historique supprimé, de mots de passe arbitraires ou de décompression des archives. Aucun succès ne certifie « aucun secret dans tout le projet ». Aucun hook utilisateur n'a été activé et aucune protection GitHub n'a été modifiée. Tous les tests de ce lot portent sur l'outillage de dépôt, **zéro test applicatif WCP360**.

Le lot nécessite une revue Engineering/DevOps/Security. Les essais du même assistant ne constituent pas une approbation indépendante. La PR n'est pas fusionnée et aucun des blocages du gate #13 n'est fermé par ce document.

## Sources primaires

- [Git ls-tree : full-tree](https://git-scm.com/docs/git-ls-tree).
- [Git ls-files : index, format et chemins](https://git-scm.com/docs/git-ls-files).
- [Git : variable GIT_INDEX_FILE](https://git-scm.com/docs/git).
- [Git : hooks pre-commit et portée](https://git-scm.com/docs/githooks).
