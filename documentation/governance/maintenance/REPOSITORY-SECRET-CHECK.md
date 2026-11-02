# WCP360 — Contrôle ciblé des secrets du dépôt

> Historical integration note (2026-11-02): this dated branch document is retained
> as development-source evidence. Its original decisions, pending states, path
> literals and execution claims describe the recorded snapshot; they do not
> override the current Core implementation or grant owner approval, independent
> E3 review, a CODE-BASELINE, deployment or production qualification. See the
> [tooling integration guide](../maintenance/BRANCH-TOOLING-INTEGRATION-2026-11-02.md)
> for current paths and separate current/historical CI scopes.
Date : 2026-10-02. Statut : **IMPLEMENTATION CANDIDATE — REVIEW**.

Périmètre : maintenance autorisée par CONTRIBUTING.md, en complément de la PR #42 et d'AUDIT-WCP-0109. Ce livrable n'est pas le moteur de sécurité applicatif et ne remplace aucune des publications #6/#7/#9. SC-M01 reste PAUSED, SC-M02 NOT AUTHORIZED, CODE-BASELINE NONE.

## Fonctionnalité développée

`tools/repository/check_repository_secrets.py` inspecte un commit Git exact ou HEAD. Il lit les blobs, même non UTF-8, sans exécuter le code, appliquer de filtre Git, suivre de lien symbolique ou interroger un fournisseur de credentials. Les fichiers non suivis et les modifications locales non committées ne font pas partie de ce snapshot.

Quatre règles locales, non assimilées aux identifiants SEC-WCP canoniques :

| Règle | Détection ciblée |
|---|---|
| REPO-SECRET-SENSITIVE-PATH | Fichiers .env hors .env.example, certains formats de clés/conteneurs et dumps, dossiers .ssh/.aws/backups/dumps |
| REPO-SECRET-PRIVATE-KEY | En-têtes PEM de clés privées, RSA/EC/DSA/OpenSSH et clés chiffrées |
| REPO-SECRET-GITHUB-TOKEN | Chaînes ayant des préfixes GitHub et une longueur indicative compatible avec les formats ciblés |
| REPO-SECRET-GITHUB-APP-JWT | Forme d'un token d'installation avec identifiant d'application et trois segments JWT |

Les signatures ne valident pas l'authenticité d'un token. Un faux positif doit être examiné ; aucun mécanisme d'exclusion ou de suppression automatique n'est ajouté. .env.example n'est exempté que de la règle de chemin, jamais de l'inspection du contenu. Un SQL de migration ne devient pas autorisé à contenir des données de production parce que son chemin n'est pas classé comme dump.

## Rapports sans valeurs sensibles

Les résultats ne contiennent ni extrait, ni valeur détectée, ni ligne source, ni nom de fichier brut. Chaque constat porte l'identifiant de règle, un numéro de ligne et un identifiant de chemin (ordinal dans l'arbre Git et SHA-256 des octets du chemin). Le numéro de commit permet au relecteur de retrouver localement le chemin à partir de l'ordre `git ls-tree -r -z --name-only <commit>` sans afficher le contenu en CI.

Les messages d'erreur utilisent des codes fixes. Les sorties et exceptions des sous-processus ainsi que les arguments invalides ne sont pas réémis. Le rapport optionnel est créé en mode exclusif : pas d'écrasement d'un fichier existant et pas de suivi d'un lien à la destination finale.

## Résultats et limites

- Sortie **0** : aucune règle sélectionnée n'a correspondu, statut NO_KNOWN_PATTERN_DETECTED ; jamais « aucun secret dans tout le projet ».
- Sortie **1** : constats à examiner, le job CI échoue.
- Sortie **2** : lecture incomplète, entrée non supportée, limite atteinte ou erreur ; le job échoue, sans résultat rassurant fictif.

Limites de garde : 5 000 fichiers, 8 MiB par blob, 64 MiB au total, 1 000 constats et budget de lecture Git de 90 secondes. Ces plafonds sont des paramètres de cet outil de maintenance, pas des capacités produit. Les symlinks et sous-modules produisent une erreur explicite ; aucun contenu externe n'est suivi. Les archives sont inspectées seulement comme octets bruts, sans décompression.

Ne couvre pas : historique supprimé, valeurs arbitraires de mots de passe, tous les fournisseurs, secrets encodés/chiffrés, contenu des archives ou la sécurité des fichiers binaires en général. Le contrôleur ne remplace pas un scanner de secrets spécialisé, la revue humaine, une rotation des secrets exposés ou les protections de branche. Un membre autorisé à modifier simultanément le contrôleur et le workflow peut modifier le contrôle : la revue et l'enforcement restent nécessaires.

## Tests et intégration

`tools/repository/test_repository_secrets.py` comporte **37 tests unitaires/d'intégration locale**, utilisant des chaînes synthétiques et des dépôts jetables. Ils ont été exécutés en local avant publication : tous réussis. Les variantes sont exercées avec subtests, sans multiplier artificiellement le compteur de tests.

Couverture : clés publiques/privées, tokens classiques/fine-grained/installation, contenu binaire, noms non UTF-8, absence de valeurs dans les rapports, fichiers .env forcés dans Git malgré gitignore, exactitude du commit, fichiers non committés, limites de volume et de temps, liens et sous-modules, erreurs Git, pollution de l'environnement, JSON et refus d'écrasement du rapport.

Un job CI distinct `repository-secrets` lance ces tests puis vérifie le snapshot de merge réellement extrait par checkout. Permissions contents:read, credentials non persistés, aucun secret de production, aucun déploiement. Le rapport sans contenu est écrit sous RUNNER_TEMP et conservé comme artefact à rétention 30 jours. Une anomalie fait échouer ce job mais ne prétend pas rendre la fusion techniquement impossible en l'absence de protection de branche.

```sh
python3 -I tools/repository/test_repository_secrets.py
python3 -I tools/repository/check_repository_secrets.py --ref HEAD
```

Le résultat GitHub doit être rattaché à un run et au SHA réellement testé ; il n'est pas supposé sur la seule présence du YAML. Les preuves distantes sont ajoutées à la discussion de la PR après exécution.

## Handoff ciblé

Engineering/DevOps : examiner le code, les limites, les messages et le job. Security : examiner la pertinence des signatures et des exclusions de périmètre. QA : vérifier la couverture et les contrôles négatifs. La préparation et les tests de l'assistant ne constituent pas une approbation indépendante. Aucune fusion ou fermeture des constats n'est décidée par ce document.

## Références

- Règle existante du projet : [CONTRIBUTING.md](../../../CONTRIBUTING.md).
- Git, lecture d'objets : https://git-scm.com/docs/git-cat-file.
- Python, sous-processus et délais : https://docs.python.org/3/library/subprocess.html.
- Formats/préfixes des tokens GitHub, dont l'évolution des tokens d'installation : https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/about-authentication-to-github.

Pas de nouveau contrat API/IPC/DB, de migration, de daemon, de reprise du Sprint 0 ni de nouvelle baseline dans cette modification.
