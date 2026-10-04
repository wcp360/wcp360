# WCP360 — Maintenance ciblée du dépôt avant SC-M01

Date : 2026-10-02. Statut : **CORRECTIF CANDIDAT — REVIEW**.

Références : AUDIT-WCP-0111 et volet initial AUDIT-WCP-0109 ; suivi unique [#41](https://github.com/yyogas/wcp360/issues/41). Base de branche : `d802dfdfad25f91f030c7ff7656310d05f57c311`, fusion documentaire de la PR #40. Le code applicatif, les spécifications canoniques, les versions DOC/DESIGN et les critères de reprise ne changent pas.

## 1. Modification bornée

Le `.gitignore` précédent (`479678cad0d0a624bb0dcc3ca394395b339fe4ea`) masquait des chemins source illustratifs. Le correctif :

- conserve `*.sql` par défaut, avec exceptions limitées à `/database/migrations/**/*.sql` et `/tests/fixtures/**/*.sql` ; aucun fichier SQL, migration ou DDL n'est créé ;
- conserve les dumps et sauvegardes exclus, et ajoute les noms `*.dump.sql`, `*.backup.sql`, `*.sql.gz`, `*.sql.bz2`, `*.sql.xz` ;
- ancre `/runtime/` et `/build/` à la racine pour ne plus masquer `src/.../runtime/` ou `packaging/build/` ;
- conserve la règle `secrets*`, avec deux exceptions de documentation `!/documentation/**/secrets.md` et `!/specifications/**/secrets.md` ;
- conserve les exclusions `.env`, clés, credentials, répertoires privés et overrides locaux.

Les exceptions ne sont PAS des lieux autorisés pour stocker des données réelles, mots de passe ou sauvegardes. Un fichier SQL dans une zone source doit être écrit pour le projet ou contenir des données synthétiques, jamais un dump client. Une page `secrets.md` décrit un mécanisme sans contenir de secret. Aucune arborescence applicative cible n'est imposée par ces sondes.

## 2. Vérification réellement exécutée

Commande : `python3 -I tools/repository/check_gitignore.py`.

Le contrôleur utilise uniquement Python standard et Git. Il copie les règles dans un dépôt temporaire isolé, désactive l'héritage des configurations Git et transmet 52 noms synthétiques à `git check-ignore --no-index --verbose --non-matching -z --stdin`. Les sondes ne sont pas créées sur disque. Les négations Git sont interprétées comme des réinclusions, et non comme des exclusions.

| Version de règles | Total | Assertions satisfaites | Assertions non satisfaites | Sortie |
|---|---:|---:|---:|---:|
| Avant | 52 | 41 | 11 | 1 |
| Correctif | 52 | 52 | 0 | 0 |

Parmi les 11 écarts initiaux : dix sources masquées et une sonde de dump compressé non exclue. Les 52 sondes incluent 14 chemins devant rester suivables et 38 devant être ignorés.

Trois contrôles négatifs ont également été exécutés : retirer la règle `.env` provoque 2 échecs ; ajouter `*.go` provoque 2 échecs ; un fichier d'entrée manquant produit une erreur et le code 2. Ces mutations sont temporaires et ne sont pas intégrées au correctif.

Preuve et empreintes : [JSON de reproduction](evidence/gitignore-regression-2026-10-02.json).

Les noms de fichiers et empreintes permettent de reproduire ce résultat. Ce n'est ni un audit de tout l'historique Git, ni un scanner du contenu secret, ni une vérification des `.gitignore` imbriqués. Les tests applicatifs WCP360 exécutés restent au nombre de zéro.

## 3. Premier contrôle GitHub Actions

Workflow : `.github/workflows/repository-guardrails.yml`.

Le workflow exécute les mêmes 52 sondes sur les PR vers main et les push dans main. Il n'emploie aucun secret de production, ne déploie rien et ne modifie pas le dépôt. Le token est limité à `contents: read`, les credentials du checkout ne sont pas persistés, et `actions/checkout` est figé au commit `3d3c42e5aac5ba805825da76410c181273ba90b1`, résolu depuis le tag officiel `v7.0.1` pendant cette maintenance.

Ubuntu 24.04 est l'environnement de cette vérification de dépôt, PAS une nouvelle plateforme supportée WCP360. La plateforme produit Debian 13 amd64 reste inchangée. Git et Python utilisés sont inscrits dans la sortie de chaque exécution ; cela ne constitue pas une chaîne de build produit reproductible.

L'exécution distante doit être prouvée par l'URL, le SHA et le résultat d'un run. La présence du YAML seule ne constitue pas un PASS. Ce premier workflow ne couvre pas encore les liens documentaires, les schémas de contrats, les scanners de secrets, les builds ou les tests applicatifs. AUDIT-WCP-0109 reste donc partiellement traité.

## 4. Protection et revue : limite réelle

Pendant cette intervention, la lecture de `/repos/yyogas/wcp360/branches/main/protection` retourne `403 Resource not accessible by integration`. La réponse de branche observée avant cette maintenance signalait `protected=false`. Aucune protection, offre d'abonnement, visibilité du dépôt ou règle de bypass n'a été modifiée.

Le workflow ne rend pas de lui-même le check obligatoire et ne remplace pas une revue indépendante. Il faudra vérifier les capacités d'administration disponibles, définir la politique de revue adaptée au mode mono-mainteneur et rendre les checks requis lorsqu'ils existent et s'exécutent correctement. Ne pas rendre le dépôt public pour contourner une restriction.

La préparation et les contrôles locaux sont réalisés par l'assistant sous le compte utilisateur. Aucune seconde personne, aucun sous-agent autonome ou approbateur indépendant ne sont inventés. Le correctif doit être examiné comme changement Engineering/DevOps, et sa fusion ne suffit pas à fermer tous les constats associés.

## 5. État conservé et suite

```text
WCP360-BM-0001 = FINAL
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Les références DB/QA/Engineering sont toujours à obtenir via #6/#7/#9. Aucune de ces publications n'est remplacée par ce document, le script ou ses résultats. Aucun rescan Documentation et aucune réintroduction du Sprint 0 ou de V4.1.

## Sources techniques

- [Git : format et portée des règles](https://git-scm.com/docs/gitignore).
- [Git : négations et sortie de check-ignore](https://git-scm.com/docs/git-check-ignore).
- [GitHub : sécurisation des workflows](https://docs.github.com/en/actions/reference/security/secure-use).
- [GitHub : syntaxe et permissions des workflows](https://docs.github.com/actions/reference/workflow-syntax-for-github-actions).
- [Commit officiel checkout v7.0.1](https://github.com/actions/checkout/commit/3d3c42e5aac5ba805825da76410c181273ba90b1).


## 6. Tracked-path policy enforcement — 2026-10-04

Deep same-author review identified a gap in the earlier 52 synthetic path probes.

The previous evidence proved that selected path patterns behaved as intended, but it did **not** prove that a file already tracked by Git could not violate those ignore rules after a forced add.

Example class:

    git add -f .env
    git add -f production.sql
    git add -f credentials.json

A tracked ignored path may exist even though normal `git add` would have skipped it.

The guardrail now performs two distinct checks:

1. **synthetic policy regression** — the existing 52 expected tracked/ignored path behaviors;
2. **real tracked-path policy** — enumerate Git's actual tracked paths, apply `git check-ignore --no-index` to them, and fail when any tracked path is ignored by the repository rules.

The real-path report does not print the raw violating path. It records only:

    path_index
    SHA-256(path bytes)
    ignore-rule line
    SHA-256(rule bytes)

The index is read again after the check; mutation during the scan fails closed.

A disposable integration self-test forcibly adds a synthetic `.env` and proves:

    clean tracked set → 0 violations
    forced ignored tracked file → detected
    raw path not emitted

This closes a repository-policy gap; it does **not** make the repository-secret scanner exhaustive and does not replace E3 review.
