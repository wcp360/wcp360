# WCP360 — Vérification de publication du travail

**Date :** 2 octobre 2026 — Europe/Paris.  
**Statut :** VÉRIFICATION DE PUBLICATION PARTIELLE — pas de certification « tout le projet est publié ».  
**Dépôt :** `yyogas/wcp360`, privé.  
**Main inspecté :** `d802dfdfad25f91f030c7ff7656310d05f57c311`.  
**Arbre inspecté :** `4f3d4588d79e7b58fb5f147002f01c0b5760b670`.

## 1. Conclusion vérifiable

**Les 147 fichiers du miroir sont présents et conformes à leur manifeste Git, et les quatre fichiers du rapport AUDIT-WCP-0100 sont dans main. Les travaux récents des PR #42 et #43 sont bien publiés sur GitHub, mais ne sont pas fusionnés ni approuvés par leurs propriétaires.**

Un visuel HTML précédemment non publié est ajouté avec sa provenance. Un catalogue de sept images natives WCP360 retrouvées est également ajouté, mais les images elles-mêmes ne sont pas importées par cette publication. Les trois exports canoniques manquants et plusieurs sources d'archives restent explicitement non disponibles ou non vérifiés. Il serait donc inexact d'affirmer « 100 % de tout le travail est publié ».

Ce document contrôle la publication, les chemins et les empreintes. Il ne remplace pas l'audit architectural, les décisions des propriétaires ou le gate de reprise. Les résultats ci-dessous sont figés aux commits indiqués ; les nouvelles pièces de publication ne sont pas comptées rétroactivement dans le snapshot initial.

## 2. Périmètre et méthode

Le contrôle a combiné : la relecture de main et des PR actives ; un inventaire des objets Git réels ; la comparaison au manifeste des 147 fichiers ; la liste paginée complète du dossier Bibliothèque `/WCP360 — PROJECT` ; des recherches ciblées de sources WCP360 hors de ce dossier ; et des tentatives de récupération des fichiers concernés.

Le dossier Bibliothèque contient **149 fichiers : 147 Markdown, un HTML et un ZIP**, hors dossiers. Ce périmètre n'est pas l'ensemble de toutes les conversations ni une exploration exhaustive de toute la Bibliothèque personnelle. Les documents des autres projets ne sont pas importés. Le corpus documentaire de plus de mille fichiers n'est pas rescanné sémantiquement.

Le script [check_publication.py dans la PR #42][CHECKER] inspecte les arbres Git, lit chaque blob, recalcule l'identité Git et le SHA-256, vérifie tailles, doublons, chemins attendus, absences et fichiers non listés. Il n'exécute pas le code produit et n'envoie aucun contenu source hors de GitHub. Les inventaires contiennent chemins, tailles et empreintes, pas les corps de fichiers ni des credentials.

## 3. Inventaire constaté sur main

| Ensemble au commit inspecté | Résultat |
|---|---:|
| Fichiers dans l'arbre Git | 261 |
| Taille totale des blobs | 771 691 octets |
| Markdown | 258 |
| JSON | 1 |
| Fichiers sans extension | 2 |
| Sources du miroir attendues | 147 |
| Sources du miroir avec même blob Git et même taille que le manifeste | **147** |
| Sources du miroir manquantes / modifiées / non listées | **0 / 0 / 0** |
| Octets sources du miroir selon manifeste | 348 310 |
| Fichiers du rapport AUDIT-WCP-0100 | **4 / 4** |
| Snapshot historique du 27 septembre | 42 Markdown sources + un README |

La liste nominative des 147 chemins, leurs blobs et tailles est dans [le manifeste existant][MIRROR-MANIFEST]. Les inventaires complets avec SHA-256 sont conservés comme artefacts du [run de vérification][RUN1]. Les résultats essentiels, références et empreintes de ces inventaires sont versionnés dans [publication-check-2026-10-02.json](evidence/publication-check-2026-10-02.json).

### Limite importante : manifeste Git versus octets des originaux

La comparaison 147/147 certifie les fichiers Git par rapport au manifeste publié ; elle ne certifie pas une identité brute avec chaque original de Bibliothèque.

Une comparaison réellement exécutée sur `01-daemon-operation-allowlist.md` donne : source matérialisée 4 152 octets, blob `342155edf268ab7bd07b63efcc1fe6ce93f67951` ; fichier Git 4 151 octets, blob `0112736b015f54690f452b56a052ff5417107172`. Retirer exactement le dernier saut de ligne de la source produit le blob Git. L'écart de cet échantillon est donc un saut de ligne final, pas une règle fonctionnelle manquante. Ce résultat n'est pas extrapolé comme certification de tous les originaux. Aucun fichier canonique n'est réécrit pour normaliser cet écart.

## 4. Design System : où sont les fichiers ?

Le miroir contient **114 Markdown** sous :

`documentation/library-mirror/_GITHUB-STAGING-DESIGN-FULL-V2.5.3.36.4/`

Le dossier dédié `documentation/design-system/V2.5.3.36.4/` ne contient que **27 de ces fichiers sources**, avec blobs identiques, plus son README. Les **87 autres fichiers sont bien sur GitHub dans le miroir**, mais pas dans ce dossier dédié.

Il ne faut donc ni les déclarer perdus, ni considérer le dossier dédié comme la totalité du contenu documentaire disponible. Le miroir complet inclut historique, staging, audits et candidats : les copier automatiquement comme autorité active introduirait une promotion injustifiée. Le point d'entrée complet est [le dossier du miroir][DESIGN-FULL] ; la règle [AUTHORITY-PRECEDENCE][AUTHORITY] continue de déterminer leur usage.

Les 114 fichiers Markdown ne prouvent pas la publication intégrale du ZIP Design System, des pages HTML, des styles CSS, du JavaScript ou des ressources binaires de l'archive d'origine. Ces éléments ne sont pas déclarés complets par ce contrôle.

## 5. Tout le travail récent : localisation et statut

| Livrable | Localisation | État au contrôle |
|---|---|---|
| Rapport principal, complément, index des 18 constats, reproduction initiale gitignore | [AUDIT-WCP-0100][AUDIT] — PR #40 | Dans main ; recommandations toujours soumises aux propriétaires |
| Correctif gitignore et contrôleur des 52 sondes | [PR #42][PR42] | Publiés en branche, non fusionnés |
| Workflow et nouvelles vérifications de publication | [PR #42][PR42], SHA `3d8d2ac1e9ce71df02cfd2bf2a7d40a6e1f68d82` | Publiés ; exécution réussie sur ce SHA |
| Tests synthétiques du vérificateur de publication | [test_publication_checker.py][CHECKER-TESTS] | Six cas présents, exécutés en CI ; pas de tests applicatifs |
| Preuve avant/après gitignore et documentation de maintenance | `governance/maintenance/` dans #42 | Publiées en branche ; limites de validation conservées |
| Delta de cinq liaisons API/Security, douze scénarios QA proposés | [PR #43][PR43], SHA `3e2ba6c10d0f5e1a81fb2c8275a301469252c320` | Proposition en revue ; non canonique, non fusionnée, scénarios non exécutés |
| Demandes de publication Database, QA, Engineering | Issues #6, #7, #9 | Publiées ; les demandes ne valent pas livraison des exports |
| Suivi des constats et de la reprise | Issues #41 et #13 | Sur GitHub ; gate inchangé |
| Visuel d'organisation initial | [HTML](../../visuals/WCP360_project_flow_visual.html) et [provenance](../../visuals/README.md) | Ajouté par la présente publication |
| Bilan de publication et catalogue des sources manquantes | Ce dossier | Ajoutés par la présente publication |

Les branches ne sont pas perdues et leurs fichiers ne sont pas « locaux seulement ». Inversement, la publication en branche ne signifie pas que main les contient déjà. La présente opération ne contourne pas les revues attendues pour #42/#43 et ne crée pas de copies concurrentes de leurs sources.

## 6. Vérifications effectivement exécutées

**Premier run de publication :** [37048894960][RUN1], head `e8423ba09e2fbb001a33a49b3b240093b8351e55`, état `completed / success`. L'artefact `11244913541` a été téléchargé puis contrôlé : SHA-256 ZIP `cf180b80d804bee6ef72dcfd48d6467e6d176d76a8070abd971852832f983ea3`. Les trois inventaires base/candidat/merge de test ont été ouverts et analysés.

**Run avec tests du vérificateur :** [37049862542][RUN2], head `3d8d2ac1e9ce71df02cfd2bf2a7d40a6e1f68d82`. Le [job 110980260348][JOB2] et ses étapes de sondes gitignore, tests synthétiques, inventaires et conservation des preuves ont terminé en `success`, sans étape de vérification ignorée.

Les six tests synthétiques vérifient : inventaire correct, blob modifié, fichier manquant, fichier non listé, référence Git refusée et nombre de lignes du manifeste invalide. Ils utilisent des données et un dépôt temporaires ; aucune application WCP360 n'est exécutée.

Le workflow est en lecture seule, checkout sans credentials persistés, dépendances Actions figées à leurs commits. Les artefacts Actions ont une conservation de 30 jours ; l'artefact du premier run expire le **1er novembre 2026 à 18:39:28 UTC**. Il ne faut pas présenter ces téléchargements temporaires comme un archivage permanent. Le résumé chiffré et les empreintes sont committés ici ; le script est versionné dans #42 et peut régénérer l'inventaire à partir des commits conservés.

Reproduction, à partir d'un checkout contenant le contrôleur :

```sh
python3 -I tools/repository/test_publication_checker.py
python3 -I tools/repository/check_publication.py \
  --ref d802dfdfad25f91f030c7ff7656310d05f57c311 \
  --output publication-evidence/main-d802.json
```

Le succès de ce script ne signifie jamais « tous les fichiers historiques et toutes les sources inaccessibles sont publiés ». Le JSON indique explicitement `overall_project_publication = NOT_CERTIFIED_COMPLETE`.

## 7. Sources encore manquantes ou non vérifiables

| Élément | État exact et suivi |
|---|---|
| Modèle Database final v1.0 | Corps exact non importé ; issue #6 |
| Baseline QA finale SC-M01 v1.0 | Corps final non importé/confirmé ; issue #7 |
| Engineering Ruleset final v0.2 | Corps exact non importé ; issue #9 |
| Archive DOC v4.0.21.23 complète | Octets exacts/corpus intégral non obtenus ; issue #16 ; aucune ancienne version substituée |
| Archive Design complète et ressources non Markdown | Non certifiées par le miroir des seuls Markdown |
| `WCP360_CONSOLIDATED_MD_2026-09-27.zip` | Métadonnées trouvées ; matérialisation brute refusée et pas de texte lisible ; les 42 Markdown déjà publiés sont distincts de la publication du ZIP original |
| `WCP360 — Package R4 — Audit - Durability - Cardinality — Database Return.md` | Métadonnées historiques trouvées hors du dossier projet ; corps non récupéré, non importé, pas une substitution du DB final |
| Sept PNG de propositions de marque WCP360 | Originaux récupérés et empreintes calculées ; **images non importées dans GitHub** ; catalogue ci-dessous |

Les refus de récupération ne prouvent pas une absence universelle des documents. Ils prouvent seulement que ces corps n'ont pas pu être obtenus par les accès utilisés. Les fiches ou métadonnées ne sont pas publiées comme si elles étaient les documents manquants.

### Sept images : transparence sur la publication

Le [catalogue binaire](evidence/pending-binary-assets-2026-10-02.json) liste les noms, les 6 689 940 octets au total, les SHA-256 et les identités Git calculées des sept fichiers PNG natifs. Ces fichiers sont des propositions historiques, pas une identité de marque approuvée.

Une archive des originaux et de leur manifeste a été préparée pour remise dans la conversation : `WCP360-unpublished-source-assets-2026-10-02.zip`, 6 596 441 octets, SHA-256 `3580a3f79aa194b77abb3c9b1ed026cf99f226ac7b179a6da78d96d2dbda53d0`. **Cette archive n'est pas téléversée dans GitHub par la présente opération.** Ni son existence locale, ni la publication du catalogue ne valent publication des images. Leur transfert reste une tâche ouverte du suivi #41 ; aucune URL privée temporaire n'est committée.

## 8. Mesures et décisions conservées

```text
WCP360-BM-0001 = FINAL
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4
CANONICAL ARTIFACTS PRESENT = 5 / 8
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Aucun Sprint 0, V4.1 Executable Contracts, code applicatif, migration ou DDL n'est réintroduit. Aucun avis propriétaire ou reviewer indépendant n'est simulé. Aucune protection de branche, offre GitHub ou visibilité du dépôt n'est modifiée. Le contrôle de publication n'est ni un scan exhaustif de secrets, ni une certification de sécurité, ni une acceptation des 18 constats.

## 9. Critère de complétude pour la suite

La publication pourra être dite complète seulement sur un périmètre explicitement inventorié, avec chaque fichier attendu rattaché à un chemin Git ou à une archive GitHub conservée, une empreinte vérifiée et son statut documentaire. Les imports ne doivent pas transformer un candidat en référence approuvée. Les futurs fichiers issus de cette vérification doivent rejoindre le même suivi, sans ouvrir une seconde autorité de gouvernance.

Les manques documentaires existants restent suivis par #6/#7/#9/#16. #41 suit les écarts de publication et d'audit ; #13 reste le gate d'implémentation. La fusion de ce rapport ne clôture aucun de ces tickets.

[MIRROR-MANIFEST]: https://github.com/yyogas/wcp360/blob/d802dfdfad25f91f030c7ff7656310d05f57c311/documentation/library-mirror/MANIFEST.md
[DESIGN-FULL]: https://github.com/yyogas/wcp360/tree/d802dfdfad25f91f030c7ff7656310d05f57c311/documentation/library-mirror/_GITHUB-STAGING-DESIGN-FULL-V2.5.3.36.4
[AUTHORITY]: https://github.com/yyogas/wcp360/blob/d802dfdfad25f91f030c7ff7656310d05f57c311/governance/AUTHORITY-PRECEDENCE.md
[AUDIT]: https://github.com/yyogas/wcp360/tree/d802dfdfad25f91f030c7ff7656310d05f57c311/governance/audits/AUDIT-WCP-0100
[CHECKER]: https://github.com/yyogas/wcp360/blob/3d8d2ac1e9ce71df02cfd2bf2a7d40a6e1f68d82/tools/repository/check_publication.py
[CHECKER-TESTS]: https://github.com/yyogas/wcp360/blob/3d8d2ac1e9ce71df02cfd2bf2a7d40a6e1f68d82/tools/repository/test_publication_checker.py
[PR42]: https://github.com/yyogas/wcp360/pull/42
[PR43]: https://github.com/yyogas/wcp360/pull/43
[RUN1]: https://github.com/yyogas/wcp360/actions/runs/37048894960
[RUN2]: https://github.com/yyogas/wcp360/actions/runs/37049862542
[JOB2]: https://github.com/yyogas/wcp360/actions/runs/37049862542/job/110980260348
