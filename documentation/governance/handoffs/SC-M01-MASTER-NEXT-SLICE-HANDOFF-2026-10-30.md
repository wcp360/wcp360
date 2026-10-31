# WCP360 HANDOFF — MASTER — E3 #108/#114 et prochaine tranche SC-M01

```text
FROM =
WCP360 — SOURCE CODE

TO =
WCP360 — MASTER / Project Control

REFERENCE =
WCP360-BM-0001 FINAL / SC-M01 / PR #108 / PR #114 / issue #13

DATE =
2026-10-30

STATUS =
DOSSIERS E3 PRÉPARÉS — DÉCISION MASTER EN ATTENTE

PUBLICATION CLASS =
G1 coordination + P1 proposition non normative ; E0/E2 auteur

CODE-BASELINE =
NONE

SC-M02 =
NOT AUTHORIZED
```

Ce handoff fournit un dossier vérifiable pour les revues et une proposition de prochaine tranche. Il ne constitue pas une décision MASTER, une revue E3 ou une autorisation de code supplémentaire. Le périmètre actuellement autorisé demeure la fondation inerte du daemon.

## 1. Décisions demandées à MASTER

1. Faire réaliser une revue E3 réellement indépendante de #108, puis de #114 avec conclusions séparées liées à leurs SHA. Les dossiers sont prêts ; aucun relecteur ni verdict n'est inventé.
2. Vérifier les corrections et avis propriétaires applicables, puis choisir explicitement l'ordre d'intégration qui préserve les liaisons SHA/revue/CI.
3. Examiner la proposition **SC-M01 — catalogues typés hors ligne**, décrite ci-dessous. Demander les validations propriétaires ciblées, puis décider d'un GO limité ou d'un maintien en attente.
4. Consigner le traitement administratif/DevOps du défaut d'enforcement du dépôt. Ce handoff ne démontre aucune protection et ne clôture aucun constat d'audit.

L'acceptation de ce document comme coordination n'est l'acceptation d'aucune de ces quatre décisions.

## 2. Baselines et état réel au relevé

Sources sélectionnées dans [le manifeste](../../manifests/WCP360-BM-0001.md), main observé `038617047fb6dc701ad1c2345f4742b890c745e2` :

| Référence | État / version |
|---|---|
| WCP360-BM-0001 | FINAL |
| DOC / DESIGN | v4.0.21.23 / V2.5.3.36.4 |
| Module / Permission / Security / DB | v1.0 |
| API / IPC | 1.0 / WCP360-IPC-B/1 |
| QA / DevOps / OS / Observability / Performance | v1.0 |
| Engineering | ENG-WCP-RULESET-0001 **v0.3**, successor accepté ; locator historique v0.2 conservé |
| Artefacts canoniques acceptés | **8/8** ; publication, pas preuve d'implémentation globale |
| GO SOURCE CODE actuel | MASTER `3fd2f8007baa49c2454cc89c0a5488a57c77911b` : daemon inerte seulement |
| Toolchain de cette tranche | addendum DevOps v1.0, blob `f8608d6258f87b8c1099b9b3a7e32ec95c72dc27` ; F02/F04/F06/F07/F08 |
| CODE-BASELINE / SC-M02 | NONE / NOT AUTHORIZED |

Les déclarations historiques 5/8 ou Security encore attendue ne remplacent pas le manifeste et les avis spécialisés ultérieurs au SHA. Les [empreintes exactes des sources et le relevé API/CI](../reviews/evidence/PR108-PR114-E3-SNAPSHOT-2026-10-30.json) sont conservés avec ce dossier.

## 3. Pièces à transmettre pour E3

| Pièce | Portée |
|---|---|
| [Dossier E3 #108](../reviews/PR108-E3-INDEPENDENT-REVIEW-PACKET.md) | SHA `71deefa2976a7005983d410907dc2050287be05f`, 13 fichiers, deux runs, avis E2 Engineering/DevOps/Security, risques résiduels |
| [Dossier E3 #114](../reviews/PR114-E3-INDEPENDENT-REVIEW-PACKET.md) | SHA `462792110330f00b2a1911dd03175e8257379b3a`, cinq fichiers, 21 cas processus et sept mutations |
| [Modèle de relevé](../reviews/SC-M01-E3-REVIEW-RECORD-TEMPLATE.md) | identité/indépendance, périmètre, constats, verdict et SHA ; non rempli |
| [Snapshot machine-readable](../reviews/evidence/PR108-PR114-E3-SNAPSHOT-2026-10-30.json) | blobs, CI head vs commit exécuté, arbres, lignes de résultats, artifacts/digests/expiration et références sources |

### État des revues et preuves

| Sujet | #108 | #114 |
|---|---|---|
| Head actuel | `71deefa2976a7005983d410907dc2050287be05f` | `462792110330f00b2a1911dd03175e8257379b3a` |
| E1 existante | runs 40613003105 et 40613008116 SUCCESS | run 41133175259 SUCCESS |
| Gates / fixtures | 13/13 ; 6/6 rejets attendus | 13/13 ; 7/7 rejets ; 21/21 cas processus |
| Avis spécialisés | Engineering, DevOps, Security approuvent la tranche au SHA ; tous E2 même compte | aucun avis enregistré lors du relevé ; à obtenir selon impact |
| E3 | **MISSING** | **MISSING** |
| Merge | **HOLD** | **HOLD**, dépend de #108 |

Les résultats ne prouvent ni les 199 tests produit, ni les sessions, ni les 47 opérations privilégiées. Aucun test produit n'a été relancé pendant la préparation des documents. Les artifacts bruts expirent le 27 novembre pour #108 et le 29 novembre pour #114 ; faits et digests sont conservés, pas les octets des ZIP.

## 4. Ordre d'intégration proposé — aucune action de merge autorisée ici

1. Garder les heads #108 et #114 inchangés pendant leur revue. Un même relecteur réellement indépendant peut examiner les deux, mais doit rendre deux conclusions explicites.
2. Obtenir E3 #108, traiter les constats et consigner tout nouveau SHA. Les avis E2 ne remplacent pas cette étape.
3. Obtenir les avis Engineering/DevOps/Security applicables à #114 et la confirmation QA de la portée des preuves ; les avis #108 ne se transfèrent pas. Obtenir E3 #114.
4. Préparer l'intégration #108 vers le main réellement retenu ; rafraîchir CI d'intégration et revalider l'applicabilité de la revue après tout changement de base/SHA selon la politique. Choisir et enregistrer la méthode d'intégration, sans modifier silencieusement le candidat relu.
5. Après intégration approuvée de #108, préparer #114 vers le main obtenu. Vérifier diff, ascendance, nouveaux SHA éventuels et E1 actuelle ; obtenir la revalidation explicite nécessaire avant son intégration.
6. Faire vérifier l'état consolidé par les propriétaires concernés, puis demander la décision MASTER sur la suite.

**Ne pas merger #114 dans la branche #108 en premier** : le head #108 deviendrait différent du SHA des avis et de la revue attendue. Un squash/rebase/cherry-pick peut également changer les SHA ou l'ascendance ; il faut en conserver la correspondance et la revue appropriée. Le merge de test #114 a le même arbre que son head, mais cela ne prouve rien sur un futur main.

Depuis le parent `768a411b02c68e8845372c28cabe7c157f88766f` du run d'intégration #108, main observé ne diffère que par le document de preuves. Cette observation permet une décision de revalidation documentée ; elle n'est pas elle-même une revalidation ni un nouveau run.

### Enforcement du dépôt

Au relevé : `main.protected = false`. La lecture `GET /repos/yyogas/wcp360/rulesets` renvoie 403 avec le message de disponibilité « Upgrade to GitHub Pro or make this repository public to enable this feature. »

Ce constat appelle une vérification de disponibilité/plan/droits et une décision explicite Admin + DevOps, suivant le [plan d'enforcement existant](../GITHUB-MAIN-ENFORCEMENT-PLAN.md). Il n'autorise aucun changement de visibilité ni contournement. Une procédure manuelle ne doit pas être présentée comme une protection serveur démontrée. Ne pas exiger des checks inexistants sur main ; établir les vrais contextes et leurs preuves négatives au moment prévu par ce plan.

Le [dossier #42](../reviews/PR42-E3-INDEPENDENT-REVIEW-PACKET.md), AUDIT-WCP-0109/0110 et #41 restent des pistes distinctes. Ce handoff ne les clôture pas et n'ajoute pas un nouveau gate universel non décidé par les propriétaires.

## 5. Prochaine tranche proposée : catalogues typés hors ligne uniquement

**Statut : PROPOSITION NON APPROUVÉE — PAS DE CODE GO.**
Il s'agit d'une extension limitée de SC-M01, pas de SC-M02 et pas de promotion complète de la PR #110.

### Objectif et utilité

Matérialiser les inventaires canoniques dans des types distincts et des valeurs immuables, vérifiés ligne par ligne contre les sources approuvées. Cela prépare les futurs composants sans établir de transport, de décision d'autorisation ou de primitive.

Résultat proposé :
- un package pur sous `SOURCE/daemon/internal/contracts/`, avec inventaires des **86 contrats API**, **82 permissions actives**, **47 opérations IPC** et **23 codes d'erreur**, à recontrôler dans les sources lors du GO ;
- séparation stricte des namespaces : contract_id, permission_id, action_id, ipc_operation_id, error_code et autres identifiants présents ne deviennent pas interchangeables ;
- champs existants recopiés fidèlement, avec provenance chemin/blob/version et vérification des lignes ; aucune nouvelle sémantique n'est déduite d'un nom égal ou d'un total ;
- tests de conformité, doublons, identifiants inconnus, dérive de source et immutabilité des valeurs retournées ;
- adaptation explicitement revue du gate d'import/du périmètre de tests pour couvrir tous les packages de production de la tranche, en conservant dépendances stdlib et build isolé.

**Le CLI reste inchangé dans cette proposition.** Aucun `--contracts` n'est activé par défaut. Un lookup réussi indique seulement l'appartenance à un inventaire : il ne permet aucune action.

### Sources proposées pour les données dérivées

| Source canonique | Blob observé |
|---|---|
| `specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | `4893c5191b4928ecfa5a70f4dc14b39a8939fcea` |
| `specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md` | `81f3d425f36470167aba4bfb7ba069268626342f` |
| `specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md` | `fec9c74e8413db822662e47c76c40486e282334f` |

Security, DB, QA, Engineering et DevOps restent les autorités de leurs invariants. Ces trois blobs servent à l'inventaire ; ils ne donnent pas un nouveau droit d'exécution.

### Matériel candidat existant et extraction

[PR #110](https://github.com/yyogas/wcp360/pull/110), head observé `31fd3fe4d9203d9e5dbc2b43843c6c9ec74f2940`, contient déjà un package contracts candidat, mais aussi sessions, navigateur, cohérence, retry et CLI étendu. Elle reste **draft / non intégrée / non autorisée comme ensemble**.

Après GO seulement, sélectionner les concepts/fichiers strictement nécessaires et réexaminer le diff dans une PR étroite fondée sur le commit d'intégration validé. Ne pas cherry-pick toute #110. Le fichier Python de génération candidat n'est pas couvert automatiquement par la toolchain daemon : son outil/version/commande/isolement doivent être approuvés, ou une solution stdlib Go doit être choisie par Engineering/DevOps. Le présent document ne choisit ni n'implémente le générateur.

### Hors périmètre de cette demande

Sessions/cookies/CSRF/Origin, moteur d'autorisation, règles de retry/deadline, API HTTP, sockets/listeners, authentification peer, dispatcher et IPC-B effectif, wcp-privd, opérations privilégiées, DB/DDL/migrations, jobs/outbox/audit durable, UI, installateur et release.

Les 12 bindings parent/provisioning/OR de [#91](https://github.com/yyogas/wcp360/issues/91) restent ouverts. Ils ne bloquent pas nécessairement un inventaire sans exécution, mais interdisent toute inférence de droit ou activation de dispatcher dans cette tranche. Les décisions A2–A7 restent à activer selon les composants, conformément à la matrice existante ; le catalogue ne les résout pas.

## 6. Owner handoffs et preuves requises avant le GO suivant

| Responsable | Décision / preuve demandée | État à la préparation |
|---|---|---|
| Relecteur réellement indépendant | E3 #108 et #114 aux SHA finaux, constats et corrections ; revalidations d'intégration | MISSING |
| Engineering / DevOps / Security pour #114 | avis sur le harness, /dev/full, mutation, preuves et limites, liés au SHA | PENDING |
| QA | vérifier la portée réelle des preuves actuelles et approuver le plan de conformité de la prochaine tranche ; aucun nouveau TEST-ID inventé par SOURCE CODE | PENDING |
| API / IPC / CONTRACTS | confirmer projection fidèle des lignes, namespaces et provenance ; ne pas interpréter les bindings non résolus | PENDING pour nouvelle tranche |
| Security / Hardening | confirmer que types/lookup ne confèrent aucun droit, aucune fusion USER/ADMIN et aucune nouvelle capacité | PENDING pour nouvelle tranche |
| Engineering + DevOps | confirmer/étendre F02/F04/F06/F07/F08 à ce package pur ; trancher générateur éventuel, versions/commandes, périmètre des imports/CI et provenance | PENDING pour nouvelle tranche |
| Admin + DevOps | enregistrer la disponibilité et le traitement de l'enforcement, sans prétendre qu'il est actif | OPEN |
| MASTER | vérifier intégration acceptée, entrées canoniques toujours valides, décisions activées et sélection de la tranche/base exacte | PENDING |

F01 backend/framework, F03 DB, F05 frontend et les décisions de transport/sessions/release ne sont pas consommées par cette proposition limitée ; ne pas les déclarer résolues. Si le périmètre est élargi, réactiver leurs gates avant implémentation.

### Assertions QA proposées — pas des résultats exécutés

1. Chaque ligne/champ attendu et chaque empreinte de source concordent ; compter 86/82/47/23 ne suffit pas.
2. Dérive d'une source, suppression/duplication de ligne ou namespace confondu produit un échec visible.
3. Une valeur retournée ne permet pas de modifier le catalogue ; une référence inconnue ne produit aucun fallback.
4. API/IPC/permission/action/error restent distincts ; le catalogue ne décide pas d'un grant ni d'un parent binding.
5. Aucun package ajouté n'ouvre de réseau, processus, DB ou mutation ; les contrôles d'import couvrent le périmètre approuvé.
6. Génération éventuelle déterministe, outillage approuvé et aucune modification des sources canoniques.
7. Clean build/unité/conformité/scan à l'environnement épinglé, preuves exact-SHA et tentatives conservées ; E3 et avis spécialisés avant merge.

QA doit fixer les mappings et résultats attendus avant exécution. Leur état est **PROPOSED / NOT_EXECUTED**, jamais PASS.

## 7. Décision MASTER à enregistrer — fiche non signée

```text
MASTER DECISION =
PENDING — aucune décision inscrite par SOURCE CODE

REVIEWED IMPLEMENTATION / INTEGRATION SHAS =
À RENSEIGNER PAR MASTER

E3 RECORDS / OWNER SIGNOFFS / CI RUNS =
À RENSEIGNER ET VÉRIFIER

SELECTED NEXT SLICE =
PROPOSÉE : SC-M01 catalogues typés hors ligne uniquement

ACCEPTED NEXT-SLICE OWNER INPUTS / EXACT BLOBS =
À RENSEIGNER APRÈS VALIDATION DES PROPRIÉTAIRES

AUTHORIZED STARTING COMMIT / ALLOWED PATHS =
À RENSEIGNER PAR MASTER

SOURCE CODE START =
NOT AUTHORIZED FOR THE PROPOSED NEXT SLICE AT PREPARATION TIME

OPEN BLOCKERS / ADMINISTRATIVE DISPOSITION =
À RENSEIGNER

CODE-BASELINE =
NONE

SC-M02 =
NOT AUTHORIZED

PRODUCTION RELEASE =
NOT AUTHORIZED
```

Un GO futur doit préciser la base, les chemins, limites, entrées propriétaires et preuves attendues. Tant que cette décision n'est pas effectivement enregistrée par MASTER après les gates applicables, SOURCE CODE peut préparer les dossiers et propositions mais ne commence pas cette extension.
