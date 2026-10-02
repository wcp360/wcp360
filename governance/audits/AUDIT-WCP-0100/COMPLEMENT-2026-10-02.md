# WCP360 — Analyse approfondie : complément de publication du 2 octobre 2026

**Dossier :** `AUDIT-WCP-0100`  
**Date de publication :** 2026-10-02 — Europe/Paris  
**Statut documentaire :** REVIEW — observations et recommandations soumises aux propriétaires  
**Statut des constats :** OPEN ; aucune clôture ou approbation propriétaire produite par cette publication  
**Dépôt :** `yyogas/wcp360` — privé  
**Main examiné :** `aab5033f09a9b9d459d5c723344d7629306fd5ae`  
**Arbre main :** `ba9b7608fea3bdf26f675d821dadfc8889b8b3eb`  
**Rapport existant conservé :** `README.md`, présent au commit `43ff6eb5d52f86a931bc42c25458422282070f8e`  
**Publication commune :** [PR #40][PR40]  
**Suivi des constats :** [issue #41][ISSUE41]  
**Gate d'implémentation existant :** [issue #13][ISSUE13]

Ce document conserve l'ensemble des conclusions de l'analyse communiquée à l'utilisateur, les rattache aux 16 constats déjà publiés dans le rapport principal et ajoute deux points distincts : `AUDIT-WCP-0117` et `AUDIT-WCP-0118`. Il ne remplace ni les exports des propriétaires ni les décisions du MASTER. La PR existante est complétée plutôt que dupliquée.

## 1. Références et limites

```text
WCP360-BM-0001 = FINAL — statut officiel conservé
DOC-BASELINE = v4.0.21.23
DESIGN-BASELINE = V2.5.3.36.4
MODULE REGISTRY VERSION = v1.0
PERMISSION REGISTRY VERSION = v1.0
SECURITY BASELINE VERSION = v1.0
DB MODEL VERSION = v1.0
API CONTRACT VERSION = 1.0
IPC CONTRACT VERSION = WCP360-IPC-B/1
TEST BASELINE VERSION = v1.0
DEVOPS BASELINE VERSION = v1.0
OS SUPPORT MATRIX VERSION = v1.0
OBSERVABILITY BASELINE VERSION = v1.0
PERFORMANCE BASELINE VERSION = v1.0
ENGINEERING RULESET = ENG-WCP-RULESET-0001 v0.2
CODE-BASELINE = NONE
```

Ces versions sont celles déclarées dans [PROJECT-STATUS][STATUS] et [BM-0001][BM]. Leur présence dans ce tableau ne prouve pas que tous leurs corps de fichiers sont disponibles ou que leurs exigences sont déjà implémentées.

Le périmètre est documentaire et architectural : gouvernance, arbre Git, contrats API/IPC, permissions, Security, DevOps, préparation QA et métadonnées GitHub pertinentes. Le corpus ZIP de plus de mille Markdown n'est pas rescanné. Le miroir des 147 fichiers n'est pas recertifié octet par octet à l'occasion de cette publication. Aucune application WCP360, migration, unité systemd ou configuration de production n'est créée ou exécutée.

Aucun pentest, benchmark, restauration de production, scan complet de secrets ou test applicatif n'est effectué dans cette publication. La reproduction locale du `.gitignore`, déjà consignée dans le rapport principal et son JSON, est conservée comme preuve de ce contrôle antérieur ; elle n'est pas réexécutée ici et ne devient pas un PASS de la suite applicative.

Les sévérités désignent des impacts potentiels de conception ou de gouvernance, pas un score CVSS ni une compromission démontrée. Une absence dans les exports examinés ne prouve pas l'absence universelle d'un document dans toutes les conversations ou tous les systèmes.

## 2. État réel et conclusion

**Le projet a avancé dans sa définition et sa centralisation ; la première implémentation logicielle n'est pas présente dans le répertoire source examiné.** Les invariants sont structurés, mais certaines liaisons entre contrats restent insuffisamment explicites pour être implémentées sans interprétation.

| Élément | Observation | Limite de la conclusion |
|---|---|---|
| Artefacts canoniques SC-M01 | 5 familles sur 8 présentes dans `specifications/` | Présence et statut déclaré ne valent pas cohérence technique démontrée. |
| Exports manquants | Database, QA final, Engineering | Leur contenu exact ne doit pas être reconstitué depuis un résumé. |
| Code | `src/` contient un README | Pas de logiciel exécuté évalué par ce rapport. |
| Documentation | 147 Markdown matérialisés/mirrored selon le registre | Ce n'est pas l'intégralité certifiée du corpus DOC v4.0.21.23. |
| Corpus DOC complet | Publication physique suivie séparément par #16 | Ne pas substituer une archive plus ancienne ou un export Design. |
| GitHub Actions | Réponse de la collection runs : `total_count=0`, `workflow_runs=[]` | Pas de preuve Actions retrouvée ; ne pas en déduire l'absence de toute CI externe ou historique supprimé. |
| Main | Réponse de branche : `protected=false`, protection désactivée | Les règles écrites ne démontrent pas un enforcement technique ; politiques non accessibles non certifiées. |
| Publication d'audit | PR #40 ouverte ; issue #41 déjà créée | Ne pas recréer un second audit ou un second tracker. |

Sources : [état projet][STATUS], [arbre specifications][SPEC-TREE], [arbre src][SRC-TREE], [branche main][BRANCH], [Actions runs][RUNS], [PR #40][PR40], [issue #41][ISSUE41]. Les deux dernières observations ont été relues pendant la publication du 2 octobre.

**Actualisation de la réponse conversationnelle :** la mention antérieure « aucune PR ouverte » ne doit pas être réutilisée comme état courant. La relecture GitHub a trouvé la PR #40 et son tracker #41. Le commit de main reste celui examiné. Ce complément respecte ce travail existant au lieu de l'écraser.

```text
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

Cet état officiel n'est pas modifié. Les clarifications ci-dessous doivent être instruites dans les vérifications croisées existantes, selon leur impact sur les composants effectivement concernés. Le ratio 5/8 n'est pas un pourcentage d'avancement du produit.

## 3. Fondations à conserver

La séparation USER/ADMIN, les identités Unix distinctes et la chaîne `backend → wcpd non-root → IPC-B borné → wcp-privd root minimal → primitive explicite` sont établies dans les exports. Les exécuteurs arbitraires, proxies sudo/systemctl génériques, signaux libres et chemins privilégiés non bornés sont interdits. [Security §§1–4][SEC] ; [IPC §§1–4,7][IPC].

La sécurité distingue permission, action, job, événement, audit et opération IPC. L'autorisation combine identité, surface, ressource, tenant, ownership/delegation, entitlement et contexte. Une appartenance à une organisation n'accorde pas à elle seule l'accès à un tenant. Une approbation préalable ne remplace pas l'autorisation au moment de l'effet. [Security §§1,6–7][SEC].

Les contrats reconnaissent les situations d'incertitude : intention durable avant effet, résultat observé, audit final, UNKNOWN_OUTCOME et réconciliation avant répétition dangereuse. PostgreSQL du control plane demeure distinct des bases hébergées et des données applicatives des tenants. [API §§6–7][API] ; [IPC §5][IPC] ; [DevOps §§4,13][OPS].

Le diagnostic est donc une demande de précision et de preuves, pas une demande de recommencer l'architecture.

## 4. Trois publications propriétaires restent indispensables

Ce volet correspond à `AUDIT-WCP-0102`, avec traçabilité QA complémentaire sous `AUDIT-WCP-0114`.

### 4.1 Database — issue #6

Fichier attendu :

```text
specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md
```

Le statut du projet rapporte une approbation propriétaire et une disponibilité logique. Le corps physique exact reste absent de `specifications/`. Cela empêche de vérifier les PK/FK, contraintes tenant-aware, nullabilité, suppressions, transactions, révisions, unicités d'idempotence, jobs/outbox et dépendances de durabilité de l'audit. [PROJECT-STATUS §3][STATUS] ; [arbre specifications][SPEC-TREE] ; [pack DB][DB-PACK].

**Demande :** importer l'export propriétaire exact avec provenance, version et confirmation. Si le corps final ne peut pas être retrouvé, l'indiquer et obtenir une republication ciblée ; ne pas faire passer une reconstruction pour le fichier approuvé. Aucune migration ou DDL n'est demandé.

### 4.2 Tests / QA — issue #7

`TEST BASELINE VERSION = v1.0` est déclarée, mais l'artefact final SC-M01 est indiqué `NOT MATERIALIZED / NOT CONFIRMED`. Le draft préparatoire ne constitue pas une autorité QA. [PROJECT-STATUS §3][STATUS] ; [draft QA][QA-DRAFT].

**Demande :** obtenir le livrable final exact, ses scénarios, préconditions, assertions, effets interdits et preuves attendues. Les références API/IPC, N01–N26 et les familles Web sont des entrées utiles ; leur nombre ne prouve pas une couverture exécutée. Une copie de document ne doit pas produire automatiquement un PASS.

### 4.3 Engineering — issue #9

Fichier attendu :

```text
specifications/engineering/WCP360_ENGINEERING_Code_Review_ENG-WCP-RULESET-0001-v0.2_CANONICAL_IMPLEMENTATION_EXPORT.md
```

Son identité logique et son approbation sont déclarées ; son corps exact reste à importer. [PROJECT-STATUS §3][STATUS].

**Demande :** publier les règles finales de structure, dépendances, erreurs, configuration, testabilité, sécurité, maintenabilité et revue avant de juger le futur code sur des critères qu'il n'aurait pas pu consulter.

Les trois input packs existants restent des supports non autoritaires. Les issues #6/#7/#9 restent les trackers de ces publications ; aucun doublon n'est créé.

## 5. Clarifications techniques déjà rattachées au registre

### 5.1 Autorisation : action d'origine, parent et primitive — AUDIT-WCP-0104

| Fonction | Permission API | Action API | Action ou parent affiché dans Security |
|---|---|---|---|
| Runtime applicatif | `applications.runtime.manage` | `applications.runtime.update` | `applications.runtime.manage` pour certaines opérations Unix |
| Pare-feu | `network.firewall.manage` | `network.firewall.apply` | `network.firewall.manage` |
| Service reload/restart | `servers.services.manage` | `servers.service.reload` / `servers.service.restart` | `servers.services.manage` |
| Grants DB hébergée | `databases.grants.manage` | `databases.grants.set` | `databases.grants.manage` |

Sources : [API §8][API], [Security §9][SEC], [IPC §6][IPC].

Le problème n'est pas l'égalité lexicale possible de deux identifiants typés. Il est l'absence d'une transformation suffisamment explicite entre l'action d'origine, le workflow parent et la sous-opération technique dans les exemples indiqués. Des expressions comme `same approved parent action` ou `approved app/domain/admin binding` ne donnent pas toujours la liste fermée de parents admissibles.

Deux risques opposés en découlent : refus d'un workflow légitime par comparaison stricte, ou ajout d'alias permissifs pour le faire fonctionner. Aucun de ces comportements n'est observé dans du code.

**Attendu :** tuples explicites de permission, action d'origine, parent, sous-opération, cible et contraintes ; tests ALLOW exact et DENY sur une combinaison proche mais non autorisée. Une sous-opération privilégiée d'un workflow tenant ne reçoit pas automatiquement une exigence ADMIN étrangère au workflow. Les propriétaires API/IPC et Security doivent confirmer les mappings.

### 5.2 API : frontières de transport et schémas — AUDIT-WCP-0103

Le registre donne des listes de champs, profils et noms de réponses, mais pas un contrat HTTP autonome pour toutes les entrées. Méthode, route, position des paramètres, types, enums, bornes, champs conditionnels, DTO complets et statuts d'erreur doivent être définis ou reliés à une source normative précise pour les endpoints activés. [API §§4–5,8,12][API].

`JobAccepted` ou une liste `{dns_record_id,expected_revision,...}` ne doivent pas obliger l'implémentation à inventer un format. Cela peut rester en Markdown : aucune réintroduction de V4.1 « Executable Contracts », aucun OpenAPI imposé et aucun nouveau framework ne sont autorisés par ce rapport.

### 5.3 Backend/worker vers wcpd — AUDIT-WCP-0105

IPC-B définit un socket, des droits et un framing. La frontière de contrôle décrite dans DevOps reste plus générale. Il faut identifier le transport, l'authentification, la séparation USER/ADMIN, le cycle du contexte de confiance et la voie d'accès du worker. [IPC §§2–3][IPC] ; [DevOps §§3,5,7][OPS].

Le worker n'a pas de groupe supplémentaire par défaut et ne peut pas accéder directement au broker root. Ce point ne doit pas être résolu en lui ajoutant arbitrairement un groupe privilégié. Le propriétaire doit publier le chemin autorisé ou citer son contrat exact.

### 5.4 Timeouts et phases — AUDIT-WCP-0107

Les valeurs I/O 5 s, opération par défaut 30 s et maximum 900 s ne sont pas nécessairement contradictoires. Il manque une définition suffisamment précise de leur application aux phases : connexion, trame, acceptation durable, début d'effet, exécution et résultat. `deadline_ms` doit avoir une interprétation commune. [IPC §§2–3,5–6][IPC].

**Attendu :** comportement défini avant et après dispatch, perte de connexion, lecture partielle, opération dépassant 5 s, deadline expirée et annulation tardive. Ne pas inventer un ACK, heartbeat ou ticket asynchrone, et ne pas augmenter simplement les délais pour masquer l'ambiguïté.

### 5.5 Idempotence et récupération — AUDIT-WCP-0106 / 0108

L'API mentionne l'en-tête `Idempotency-Key` et des champs `idempotency_key`. Il faut confirmer s'ils représentent la même entrée logique, où elle est transmise et comment une divergence éventuelle est rejetée. Les notions de request, job, opération et nonce IPC doivent être reliées à une clé persistante et à sa portée. [API §4 et registre][API] ; [IPC §§3,6][IPC].

Scénario de référence : intention persistée, effet système exécuté, puis crash avant résultat durable. La reprise doit retrouver l'opération, constater l'incertitude et réconcilier l'état observé. La clé de déduplication, l'unicité, la rétention, la concurrence, le détenteur du travail et la revalidation d'autorisation doivent être vérifiables dans les contrats propriétaires.

Aucune transaction ACID fictive entre PostgreSQL et l'OS ne doit être supposée. Aucun TTL, mécanisme de verrou ou schéma de tables n'est choisi par cet audit. L'import du modèle DB est nécessaire pour vérifier cette continuité.

## 6. AUDIT-WCP-0117 — Isolation des cookies et topologie USER/ADMIN

**ID :** AUDIT-WCP-0117  
**Scope :** déploiement des surfaces Web, sessions et tests d'isolation  
**Baseline :** Security v1.0 et DevOps v1.0 au commit main examiné  
**Classification :** risque conditionnel / critère de déploiement à confirmer  
**Severity :** élevée si des surfaces de confiance distinctes partagent un hôte sans confinement adapté  
**Owner :** Security + DevOps ; QA pour la vérification ; Design pour les implications UI uniquement  
**Status :** OPEN — décision propriétaire attendue

### Finding et evidence

Security impose des origines, noms de cookies, namespaces de sessions et identités Unix distincts. Les cookies prévus sont host-only, `Secure`, `HttpOnly`, `Path=/`, `SameSite=Strict`, sans attribut Domain. [Security §13][SEC].

Une distinction de ports produit des origines différentes, mais les cookies HTTP ne sont pas isolés par port. Ce comportement est explicitement décrit dans la RFC 6265, notamment §8.5. [RFC 6265][RFC6265].

**Ce qui n'est pas affirmé :** aucun déploiement réel WCP360 utilisant le même hôte et des ports différents n'a été observé ; aucune fuite n'a été reproduite. Le risque est conditionnel à la topologie et au routage choisis.

### Impact

L'exigence « origines distinctes » pourrait être considérée satisfaite par deux ports sur un même hostname, tout en laissant un service recevoir des cookies destinés à l'autre surface. Un nom de cookie différent ne supprime pas à lui seul leur transmission à un même hôte admissible.

### Recommendation et required change

Publier la topologie admissible, le routage des requêtes et les critères de non-exposition des cookies entre surfaces. Des hostnames distincts cohérents avec des cookies host-only constituent une option à confirmer ; ils ne remplacent pas les contrôles d'autorisation côté serveur.

Faire confirmer par Security/DevOps que la séparation ne repose pas uniquement sur les ports. Ne pas imposer silencieusement des domaines, ports, certificats ou mécanismes de proxy à la baseline existante.

### Required tests et preuve de clôture

QA doit prévoir, pour la topologie finalement approuvée, un test navigateur qui observe quels cookies atteignent chaque origine et vérifie l'absence du cookie ADMIN côté USER et inversement. Tester également le refus serveur d'une session ou d'un contexte de la mauvaise surface et le comportement de logout/révocation déjà exigé par Security.

Clôture : décision de topologie, fichier/commit propriétaire, préconditions de test, observation des en-têtes sans journalisation de secrets et résultat effectivement exécuté sur le déploiement candidat. Avant exécution, ces scénarios restent PLANNED/READY/BLOCKED selon QA.

## 7. AUDIT-WCP-0118 — Priorités de permissions et intersection des contraintes

**ID :** AUDIT-WCP-0118  
**Scope :** moteur d'autorisation, overrides, rôles, entitlements et délégations  
**Baseline :** Permission Registry v1.0 et Security v1.0 au commit main examiné  
**Classification :** ambiguïté de composition à vérifier, pas contradiction démontrée  
**Severity :** élevée si une résolution anticipée d'ALLOW contourne une contrainte obligatoire  
**Owner :** Security ; API/IPC et Database pour les représentations ; QA pour les cas limites  
**Status :** OPEN — clarification propriétaire attendue

### Finding et evidence

Le registre des permissions décrit :

```text
explicit DENY
> account override
> role grants
> plan / feature envelope
> default policy
```

La baseline Security décrit parallèlement une intersection incluant permission, entitlement, ressource, tenant, ownership/delegation, contexte, force d'authentification et approbation requise. [Permission Registry, Authority][PERM] ; [Security §1][SEC].

Ces deux présentations peuvent être compatibles : une priorité de résolution de droits n'est pas nécessairement la règle de décision finale. Ce qui doit être explicite est la distinction entre modification autorisée d'une enveloppe effective et arrêt anticipé du moteur au premier ALLOW.

### Impact

Une implémentation par « premier résultat positif » pourrait ignorer l'entitlement ou une autre contrainte obligatoire. Une interprétation inverse trop restrictive pourrait annuler des overrides légitimes. Aucun de ces comportements n'a été observé dans une implémentation.

### Recommendation et required change

Security doit confirmer l'algorithme de composition ou pointer vers sa définition approuvée : portée et autorité des overrides, calcul de l'enveloppe effective, application des DENY, contraintes non contournables, plafond des délégations et revalidation après révocation. Ne pas créer un pouvoir d'override illimité depuis le seul nom du champ.

### Required tests et preuve de clôture

Documenter les résultats attendus pour : rôle autorisé avec entitlement absent ; override légitime modifiant l'enveloppe ; DENY applicable en présence d'un rôle positif ; délégation dépassant le plafond effectif ; modification/révocation entre enqueue et exécution ; changement de contexte sans nouveau droit.

Les résultats détaillés doivent être ratifiés par Security selon les contrats existants, et non inventés pour faire passer les tests. Clôture : table de décision propriétaire, liens de contrats et preuves QA sur le SHA candidat quand il existe.

## 8. Gouvernance, publication et preuves de validation

Ce volet consolide `AUDIT-WCP-0101`, `0109`, `0110`, `0112`, `0114` et `0115` sans remplacer leurs fiches.

**Décision, disponibilité et exécution restent distinctes.** BM-0001 peut être FINAL comme décision et SC-M01 rester PAUSED pour manque de preuves matérielles. Ce complément ne rétrograde pas le manifeste en archive historique et ne réécrit pas ses décisions. Les écarts courants doivent être reliés aux fichiers qui permettent de les examiner. [BM-0001][BM] ; [checklist de reprise][CHECKLIST].

**Le gate 8/8 existe déjà.** Il inclut des vérifications croisées et un ordre de reprise. Atteindre 8/8 par import ne prouve pas à lui seul que toutes les ambiguïtés d'un composant sont résolues. Inversement, le présent audit ne peut pas ajouter silencieusement un neuvième prérequis. L'import du corpus DOC reste suivi séparément par #16 ; toute modification du périmètre du gate relève de MASTER.

**CODEOWNERS et revue.** Le fichier courant affecte tous les domaines à `@yyogas`. Cette organisation ne prouve pas une revue indépendante. GitHub distingue l'existence de CODEOWNERS de l'exigence d'approbation configurée pour les fusions. [CODEOWNERS][OWNERS] ; [documentation GitHub][GH-CODEOWNERS]. Les observations de revue sur quelques PR ne permettent pas d'affirmer qu'aucun avis historique n'existe.

**Protection et CI.** La réponse main et l'absence de runs retrouvés justifient la demande de preuves d'enforcement. L'accès aux rulesets a été limité lors de l'analyse antérieure ; une collection workflows a également été refusée par le connecteur. Ces limites ne sont pas présentées comme une vérification exhaustive de toutes les politiques du compte. Ne pas rendre le dépôt public pour contourner une restriction, ne pas modifier l'abonnement et ne pas imposer un check inexistant dans cette publication.

**Preuve minimale attendue.** Pour une validation : contenu et version examinés, chemin, SHA ou empreinte, identité/rôle du relecteur, date, verdict et réserves. Pour une exécution : commit candidat, environnement, procédure, résultat et artefact de preuve. Un identifiant TEST n'est ni une procédure complète ni une exécution réussie.

La règle d'autorité existante impose déjà de signaler les conflits sans choisir silencieusement un fichier au nom plus récent. L'autorité métier des propriétaires doit être préservée quand deux exports canoniques divergent. [AUTHORITY-PRECEDENCE][AUTHORITY].

## 9. Handoffs ciblés et critères de réponse

Ces handoffs sont publiés comme demandes d'examen dans le dossier d'audit. Leur présence ne signifie pas que les autres discussions les ont lus, acceptés ou exécutés. Les rôles ci-dessous sont des propriétaires fonctionnels, pas des comptes GitHub fictifs ni des reviewers automatiquement désignés.

| Destinataire | Constats | Demande bornée | Livrable / preuve | Suivi |
|---|---|---|---|---|
| DATABASE / DATA MODEL | 0102, dépendances 0106/0108 | Publier le corps exact du modèle déjà approuvé ; relier les obligations de persistance existantes | Export complet, provenance, version, chemin, confirmation ; écarts éventuels séparés de l'import | #6 ; dépendances dans #41 |
| TESTS / QA | 0102, 0114, cas 0117/0118 | Publier le final SC-M01 ; détailler les assertions du périmètre réellement activé | Artefact propriétaire, préconditions, résultats attendus, effets interdits, statut honnête | #7 ; cas ciblés dans #41 |
| ENGINEERING / CODE REVIEW | 0102, 0110/0111 | Publier le ruleset exact ; définir preuve de revue séparée et corriger les exclusions Git dans une modification distincte | Export v0.2, revue au SHA, reproduction positive/négative pour les règles Git | #9 ; 0110/0111 dans #41 |
| API / IPC / CONTRACTS + SECURITY | 0103–0107, 0118 | Résoudre mappings précis, frontière de contrôle, contexte, source d'idempotence et phases de délai | Décision ou lien normatif exact ; delta déclaré si nécessaire ; tests ALLOW/DENY | #41 |
| SECURITY + DEVOPS + QA | 0117 | Confirmer la topologie de sessions USER/ADMIN et le test de non-exposition des cookies | Critère de déploiement, décision propriétaire et preuve de test au jalon approprié | #41 |
| DEVOPS + ENGINEERING | 0109, 0113 | Définir contrôles du dépôt et entrées de build applicables à SC-M01 | Configuration vérifiable, limites d'accès exposées, toolchain/locks et droits par consommateur | #41 |
| MASTER + AUDIT / ARCHITECTURE REVIEW | 0101, 0115/0116 | Arbitrer les seules divergences signalées et leur impact ; conserver le gate existant | Décisions liées aux commits, composants bloqués explicitement identifiés, ordre de reprise | #13 + #41 |
| Documentation projet détaillée | 0112 uniquement | Publication/provenance ciblée du corpus exact ; normalisation d'un delta après décision si indispensable | Archive exacte, inventaire/empreinte, chemins conservés ; pas de réanalyse générale | #16 |

Format de réponse attendu pour chaque point :

```text
FINDING ID =
OWNER =
REFERENCE EXAMINED = chemin + version + commit
FACT / INTERPRETATION / DECISION =
EXISTING NORMATIVE EVIDENCE =
REQUIRED DELTA = NONE ou description explicite
AFFECTED COMPONENT / MILESTONE =
REQUIRED TESTS / EXPECTED EVIDENCE =
REMAINING DEPENDENCIES =
STATUS = OPEN / ACCEPTED / FIX IN PROGRESS / VERIFIED / CLOSED
```

Une preuve existante suffisante peut résoudre un point sans redéfinition. Une correction sémantique réelle doit être déclarée comme changement ; elle ne doit pas être présentée artificiellement comme `CONTENT CHANGE = NONE`. Une recommandation d'audit n'est pas automatiquement une décision normative.

## 10. Ordre de traitement recommandé

**A — Publication et gouvernance, sans code applicatif.** Conserver ce rapport dans la PR #40, centraliser le suivi dans #41 et réutiliser #6/#7/#9/#16. Obtenir les trois corps propriétaires ; ne pas créer de nouveaux packs de synthèse à la place des exports. Les clarifications indépendantes et les contrôles documentaires peuvent avancer en parallèle, sans prétendre avoir lancé des sous-agents.

**B — Fermer les contrats réellement nécessaires à SC-M01.** Traiter les tuples d'autorisation, le premier segment IPC, les schémas des endpoints activés, la déduplication persistante et les phases de timeout. Compléter les critères de topologie de sessions et de composition des droits. Les nouveaux constats sont à instruire par les owners ; ils ne changent pas unilatéralement les gates.

**C — Vérifier le jeu de consommation.** Appliquer la checklist existante aux huit artefacts et aux dépendances du périmètre choisi. Figer chemins, commits/empreintes et approbations. Distinguer une signature Git d'une approbation du contenu et une présence de fichier d'une preuve de cohérence.

**D — Reprendre uniquement après autorisation MASTER.** Produire alors une première fondation étroite, des tests réellement exécutés et une revue indépendante au dernier SHA. Une CODE-BASELINE ne résulte ni de cette publication ni du seul import des huit artefacts. SC-M02 demeure hors périmètre.

Ne pas rendre chaque benchmark futur, tous les modules DNS/mail/backup/HA ou toute la chaîne de production prérequis de chaque tâche de fondation. Les détails d'installation, montée en charge et release restent attachés aux jalons qui les consomment. Inversement, ne pas laisser un mécanisme de sécurité indispensable à une primitive activée être décidé implicitement dans le code.

## 11. Couverture intégrale de l'analyse conversationnelle

| Sujet communiqué à l'utilisateur | Emplacement publié |
|---|---|
| État réel, 5/8, absence de code dans src, absence de preuve Actions et main non protégée | Ce complément §2 ; rapport principal 0101/0109/0116 |
| 147 fichiers != corpus DOC complet != logiciel développé | Ce complément §§2,8 ; rapport principal 0112 |
| Fondations architecturales à conserver | Ce complément §3 ; rapport principal §2 |
| Trois exports manquants et différence entre déclaration et corps exact | Ce complément §4 ; rapport principal 0102/0114 |
| Divergences permission/action et parents de primitives | Ce complément §5.1 ; rapport principal 0104 |
| Contrats API, transport backend/worker, timeouts, idempotence et reprise | Ce complément §§5.2–5.5 ; rapport principal 0103/0105–0108 |
| Isolation des cookies au déploiement | Nouveau constat 0117, §6 |
| Priorités de permissions et intersection des contraintes | Nouveau constat 0118, §7 |
| Limites des catalogues de tests, des revues et des règles GitHub | Ce complément §8 ; rapport principal 0109/0110/0114 |
| Autorité, gate, preuves rattachées aux commits et absence de nouveau gate implicite | Ce complément §§8–10 ; rapport principal 0101/0115/0116 |
| Demandes aux responsables et ordre de réalisation | Ce complément §§9–10 ; rapport principal §6 |
| Constats complémentaires déjà présents : gitignore, build, layout proposé, scénarios adverses | Rapport principal 0111/0113 et §§4–5, conservés sans modification |

Le dossier compte donc 18 constats d'audit : 0101 à 0116 dans le rapport principal, puis 0117 et 0118 ici. Ils restent OPEN pour examen. Leurs recommandations ne sont pas approuvées par le simple fait de les versionner.

## 12. Sources et observations de publication

Les sources de fichiers sont figées au commit de main examiné. Les collections GitHub sont temporelles : la présente table conserve uniquement les observations utiles, pas des copies brutes contenant des URL de téléchargement temporaires ou des métadonnées inutiles.

| Observation | Résultat retenu au contrôle de publication du 2026-10-02 |
|---|---|
| GET branches/main | SHA `aab5033f09a9b9d459d5c723344d7629306fd5ae` ; `protected=false` |
| GET pulls?state=open | PR #40 présente, branche `audit/audit-wcp-0100-development-readiness` ; tête avant complément `43ff6eb5d52f86a931bc42c25458422282070f8e` |
| GET issues?state=open, ordre création décroissante | Issue #41 présente, dédiée à AUDIT-WCP-0100 ; ne remplace pas #13 |
| GET actions/runs?per_page=10 | `total_count=0` et `workflow_runs=[]` |
| Lecture du dossier d'audit avant écriture | README et `gitignore-reproduction.json` présents ; leurs contenus sont conservés |
| Lecture PROJECT-STATUS au SHA examiné | 5/8 ; exports DB/QA/Engineering encore attendus ; corpus DOC suivi par #16 |

Références publiques consultées le 2026-10-02 : RFC 6265 pour l'absence d'isolation des cookies par port, documentation officielle GitHub pour CODEOWNERS et exigences de revue. Elles ne définissent pas la baseline WCP360 et ne prouvent aucune topologie de production du projet.

[STATUS]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/PROJECT-STATUS.md
[BM]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/manifests/WCP360-BM-0001.md
[API]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
[IPC]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
[SEC]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
[PERM]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
[OPS]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/specifications/devops/WCP360_SC-M01_DEVOPS_BASELINE_v1.0.md
[DB-PACK]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/handoffs/SC-M01-DB-CANONICAL-INPUT-PACK.md
[QA-DRAFT]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/proposals/SC-M01-QA-TEST-BASELINE-v1.0-DRAFT.md
[CHECKLIST]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/SC-M01-RESUME-CHECKLIST.md
[AUTHORITY]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/governance/AUTHORITY-PRECEDENCE.md
[OWNERS]: https://github.com/yyogas/wcp360/blob/aab5033f09a9b9d459d5c723344d7629306fd5ae/.github/CODEOWNERS
[SPEC-TREE]: https://api.github.com/repos/yyogas/wcp360/git/trees/02e11c5d2815c0289ec6744ee429ecc433448384?recursive=1
[SRC-TREE]: https://api.github.com/repos/yyogas/wcp360/git/trees/e5022b33a719f6b1a5966775e19d977a19f92154?recursive=1
[BRANCH]: https://api.github.com/repos/yyogas/wcp360/branches/main
[RUNS]: https://api.github.com/repos/yyogas/wcp360/actions/runs?per_page=10
[PR40]: https://github.com/yyogas/wcp360/pull/40
[ISSUE41]: https://github.com/yyogas/wcp360/issues/41
[ISSUE13]: https://github.com/yyogas/wcp360/issues/13
[RFC6265]: https://www.rfc-editor.org/rfc/rfc6265.html#section-8.5
[GH-CODEOWNERS]: https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/about-code-owners
