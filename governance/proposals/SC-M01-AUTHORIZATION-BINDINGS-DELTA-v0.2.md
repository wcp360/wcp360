# WCP360 — Liaisons d'autorisation : revue ciblée et proposition v0.2

Date : 2026-10-02. **PROPOSAL v0.2 — REVIEW — NON NORMATIF — NON CONSOMMABLE PAR SOURCE CODE.**

PR #43 ; constat existant AUDIT-WCP-0104, dépendances 0103/0105/0106/0108/0114 ; suivi #41 et gate #13. Ce document est la proposition à examiner dans cette PR à la place de la [v0.1](SC-M01-AUTHORIZATION-BINDINGS-DELTA-v0.1.md), conservée inchangée pour traçabilité. Il ne remplace aucun export sous specifications/.

Préparé par le même assistant dans la coordination MASTER. Ce n'est ni un avis reçu d'une autre discussion, ni une revue indépendante. Décision attendue d'API / IPC / CONTRACTS et SECURITY / HARDENING ; Database intervient sur ses seules structures et QA sur les assertions. Aucune approbation propriétaire n'est déclarée.

## 1. Références effectivement relues

Main observé : `9c1a6cb62e3a81e75cbe40bf28671ea717421be4`. Proposition précédente : commit `3e2ba6c10d0f5e1a81fb2c8275a301469252c320`.

| Source | Version inchangée | Blob Git | Sections de preuve |
|---|---|---|---|
| [API][API] | 1.0 | `4893c5191b4928ecfa5a70f4dc14b39a8939fcea` | §8, API-WCP-0305/0802/0803/0809 ; §9, permission/action |
| [IPC][IPC] | WCP360-IPC-B/1 | `fec9c74e8413db822662e47c76c40486e282334f` | §3, enveloppe ; §4, refus ; §5, incertitude ; §6, IPC-WCP-1023/1024/1026/1029/1030 |
| [Security][SEC] | v1.0 | `6d74f2f363524511424f839ca7262143c2af95ab` | §7, contexte de confiance ; §9, matrice et responsabilités ; §10, cibles bornées |

DOC v4.0.21.23, DESIGN V2.5.3.36.4, BM-0001 FINAL et CODE-BASELINE NONE restent inchangés. Cette revue ne rescane pas le ZIP Documentation et ne prétend pas avoir contrôlé toutes les opérations du produit.

## 2. Résultat de la revue : deux catégories, pas un remplacement global

**Constat vérifié :** reload, restart et application du pare-feu ont déjà la même valeur d'action dans les registres API et IPC. Leur ligne Security emploie un nom en `.manage` dans la colonne ambiguë « Required action_id / approved parent binding ».

**Constat vérifié :** la gestion d'un ensemble de droits DB emploie `databases.grants.set` dans l'API, mais `database_grant.apply` et `database_grant.revoke` dans la colonne action_id de l'IPC. Ces valeurs ne sont pas identiques et ne doivent pas être uniformisées sans décision.

**Conséquence proposée :** la matrice doit distinguer permission d'origine, action d'origine et action de la ligne IPC. Le fait que trois lignes coïncident ne permet pas d'imposer une égalité universelle. La v0.1 signalait déjà la question de l'enveloppe ; cette v0.2 fournit une recommandation et les alternatives sans présenter cette recommandation comme approuvée.

## 3. Matrice à ratifier : cinq liaisons explicites

Les colonnes « action d'origine » et « action IPC » désignent des rôles logiques ; elles ne créent pas de nouveaux champs dans le protocole. Les valeurs API/IPC ci-dessous sont présentes dans les sources. Leur liaison comme workflow autorisé reste une proposition, surtout pour les deux étapes DB.

| Ligne | Permission d'origine | Action d'origine API | Action de la ligne IPC existante | Primitive IPC | État de la proposition |
|---|---|---|---|---|---|
| B01 | `servers.services.manage` | `servers.service.reload` | `servers.service.reload` | `service.reload` | Alignement lexical API/IPC vérifié ; explicitation du parent Security à ratifier |
| B02 | `servers.services.manage` | `servers.service.restart` | `servers.service.restart` | `service.restart` | Alignement lexical API/IPC vérifié ; explicitation du parent Security à ratifier |
| B03 | `network.firewall.manage` | `network.firewall.apply` | `network.firewall.apply` | `firewall.policy.apply` | Alignement lexical API/IPC vérifié ; explicitation du parent Security à ratifier |
| B04 | `databases.grants.manage` | `databases.grants.set` | `database_grant.apply` | `database_grant.apply` | Parent set → étape apply conditionnel à la décision API/Security et à la résolution DB |
| B05 | `databases.grants.manage` | `databases.grants.set` | `database_grant.revoke` | `database_grant.revoke` | Parent set → étape revoke conditionnel ; aucune révocation autonome implicite |

Traçabilité : B01 API-WCP-0802 / IPC-WCP-1029 ; B02 API-WCP-0803 / IPC-WCP-1030 ; B03 API-WCP-0809 / IPC-WCP-1026 ; B04 API-WCP-0305 / IPC-WCP-1023 ; B05 API-WCP-0305 / IPC-WCP-1024. Les références de tests existantes portent les mêmes numéros dans leurs familles TEST-API et TEST-IPC.

Pour ces cinq lignes, la correction proposée dans Security porte sur le **parent d'origine autorisé**, pas sur une copie indistincte des valeurs de l'IPC. Les permissions, primitives et surfaces existantes sont conservées. Aucun nouvel identifiant de permission n'est proposé ; aucun alias `.manage` → action n'est ajouté automatiquement.

## 4. Décision D01 proposée : conserver les deux niveaux sans changer les champs

Security §7 contient déjà `origin_action_id`, `origin_permission_id`, `ipc_operation_id` et les identifiants de cible dans le contexte de confiance. IPC §3 contient déjà `action_id`, `ipc_operation_id` et `authorization_context_ref` dans l'enveloppe. Le problème est leur interprétation et leur liaison, pas l'absence démontrée d'un nouveau champ.

| Alternative | Effet | Évaluation de cette revue |
|---|---|---|
| A — action_id de l'enveloppe toujours égal à l'action API d'origine | Nécessite de réconcilier les lignes IPC techniques, notamment apply/revoke, avec cette nouvelle interprétation ; les sous-étapes restent à borner | Pas retenue comme recommandation : risque de changements plus larges que les cinq lignes visées |
| B — action_id de l'enveloppe égal à la colonne action_id de la ligne IPC sélectionnée ; origine conservée dans le contexte de confiance | Préserve les valeurs des cinq lignes IPC existantes ; exige une liaison explicite du parent et de l'étape | **Recommandée, PENDING OWNER DECISION** |

Texte proposé à ratifier pour B :

> Pour une opération IPC-B, le couple action_id / ipc_operation_id doit correspondre à une ligne explicitement autorisée du registre IPC pour la version demandée. Le contexte référencé conserve le couple permission/action d'origine. Une relation explicite parent → étape autorise cette primitive pour la cible et les contraintes concernées. Aucune égalité universelle entre action d'origine et action IPC n'est supposée, et la possession d'une référence ou d'un identifiant ne confère pas de droit.

L'origine ne doit pas être remplacée par l'action technique dans l'audit. Inversement, le contrôle de l'origine ne dispense pas de vérifier la ligne IPC. Aucun moteur d'alias, correspondance par préfixe ou mécanisme de fallback n'est proposé.

Cette recommandation **ne vaut pas définition du résolveur de contexte**. Son émetteur, ses lecteurs autorisés, son authenticité, sa liaison à la requête, sa durée, sa révocation et sa disponibilité restent D02. Elle ne suppose ni accès PostgreSQL direct du broker root, ni secret bearer partagé, ni accès IPC privilégié du navigateur ou du worker. Security conserve la séparation : wcpd contrôle l'autorisation d'origine ; wcp-privd contrôle la primitive et la cible bornées.

## 5. Décisions D02–D04 : questions fermées et preuves attendues

| Décision locale | Question à trancher | Réponse attendue et propriétaire |
|---|---|---|
| D02 — contexte | Comment authorization_context_ref désigne-t-il le contexte authentique, non substituable et encore valable pour cette étape ? | API/Security : référence normative existante ou delta indiquant producteur, résolution, lecteurs, liaison exacte à la cible/étape, expiration/révocation et refus en cas d'indisponibilité. Pas de nouveau mécanisme choisi silencieusement. |
| D03 — cibles/révisions | Que représentent les identifiants et révisions avant/après traduction API → IPC ? | API/DB : tableau de types et de relations, résolution de grant_set_id et assertions DB existantes. Ne pas assimiler une révision attendue à un identifiant de configuration par simple changement de nom. |
| D04 — reprise/audit | Comment corréler parent, étape et résultat sans répéter un effet incertain ni déclarer un succès prématuré ? | API/Security/QA, avec SRE pour les événements : références d'idempotence, preuve pré-effet, résultat/reconciliation et identifiants d'audit exacts. Toute dépendance DB non publiée reste visible. |

### D03 : différences concrètes à résoudre

Pour **reload**, l'API fournit `expected_config_revision?`, l'IPC `config_revision_id?`. Il faut définir le sens de chacun et le comportement lorsque le champ est absent. Ne pas inventer une révision ou rendre la précondition sans effet au moment de la traduction.

Pour **restart**, les deux listes de paramètres comprennent server_id/service_id. La cible doit rester le même service enregistré du même serveur ; le profil d'autorisation privilégiée de l'API ne disparaît pas à l'enqueue. Cela ne tranche pas la durée de validité du contexte à l'exécution.

Pour **firewall apply**, l'API fournit `expected_current_revision`, l'IPC `expected_active_revision`. La relation peut être une traduction documentée, mais sa portée et son type doivent être confirmés. L'autorisation d'une politique ne permet pas à la sous-étape d'en substituer une autre. Validate/rollback ne reçoivent aucun parent implicite dans ce delta.

Pour **grants set**, l'API fournit `privileges[]`, l'IPC `grant_set_id`. DB doit identifier l'objet et les contraintes déjà approuvés ; ce document ne crée ni table ni transaction. Apply/revoke doivent être limités à la même base hébergée et à la même identité, ainsi qu'aux changements approuvés. Ne pas présumer que le grant_set cible final et le sous-ensemble à retirer sont le même objet ou le même ensemble de droits. Leur relation et la reprise après effet partiel doivent être définies.

Les noms d'audit du pare-feu signalés dans v0.1 restent à réconcilier, pas à renommer automatiquement. La corrélation de deux étapes ne justifie ni de les fusionner en un événement unique ni d'émettre deux succès pour un seul effet sans sémantique définie.

## 6. Critères de refus proposés, sans inventer de code HTTP

Avant effet privilégié, une liaison parent/étape inconnue, une cible substituée, un contexte non fiable ou un droit devenu invalide ne doivent pas permettre l'effet. Cela prolonge les refus déjà définis par Security §7/§9 et IPC §4 ; la priorité entre erreurs et les projections HTTP ne sont pas fixées ici.

Un refus avant dispatch n'est pas un UNKNOWN_OUTCOME. Après dispatch possiblement effectif, la perte de réponse n'autorise ni à affirmer l'absence d'effet ni à rejouer aveuglément : IPC §5 impose observation/réconciliation. Le choix du texte d'erreur ne doit pas faire disparaître cette distinction temporelle.

Les sous-opérations tenant déjà autorisées ne reçoivent pas arbitrairement une permission ADMIN supplémentaire. Les actions directes d'administration d'un service ne deviennent pas accessibles à WCP-USER. La relation explicite de parent reste nécessaire dans les deux cas.

## 7. Complément QA : 17 scénarios proposés au total, zéro exécuté

Les cas C01–C12 de v0.1 sont conservés. Leur lecture est complétée par la matrice et les réserves ci-dessus, pas remplacée par une égalité action API = action IPC. En particulier C04 porte sur la substitution de l'action d'origine par le nom de permission ; C07 ne présume pas la structure de grant_set ; C09 refuse l'étape sans parent valide, sans interdire un autre parent qui serait explicitement approuvé ultérieurement.

**C13–C17 sont ajoutés, PLANNED ; pas de nouveaux identifiants canoniques TEST-* attribués.** QA doit confirmer les rattachements et les codes exacts. Les autres contraintes sont supposées satisfaites pour isoler la cause testée.

| Cas | Précondition et entrée adversariale | Résultat attendu à ratifier | Preuve / rattachement proposé |
|---|---|---|---|
| C13 — deux niveaux | Contexte grants.set authentique et lié à apply ; remplacer dans l'enveloppe l'action technique database_grant.apply par databases.grants.set | Refus du couple IPC non conforme sous l'option B ; origine conservée dans l'audit ; aucun effet | N03, TEST-IPC-1023 ; comparer cas témoin légitime et cas substitué |
| C14 — substitution d'étape | Même contexte borné à apply ; demander revoke avec le couple IPC lexicalement correct | Refus de l'étape non autorisée par ce contexte précis ; aucune révocation. Le couple local IPC correct ne suffit pas | N03/N05, TEST-IPC-1024 ; audit corrélé au parent et à l'étape refusée |
| C15 — contexte indisponible | Le couple action/opération est correct mais la référence ne peut pas être résolue ou sa fiabilité vérifiée avant dispatch | Aucun effet ni fallback sur des droits déclarés par l'appelant ; code sûr à fixer selon D02 | N05/N24 selon classification QA ; aucune primitive déclenchée |
| C16 — révision périmée | Une révision attendue est autorisée ; l'état actif change avant effet | Conflit/refus avant effet selon contrat applicable ; pas d'écrasement de la nouvelle révision ; si le dispatch est déjà incertain, réconciliation distincte | N16, TEST-IPC-1026/1029 ; état observé inchangé par la requête périmée |
| C17 — résultat incertain | La primitive peut avoir modifié le système, mais sa réponse finale est perdue | UNKNOWN_OUTCOME/réconciliation ; pas de répétition aveugle ni de succès inventé ; conservation distincte de l'origine et de l'étape | N15/N18/N25, tests IPC des cinq lignes ; preuve pré-effet et preuve de reprise |

Pour tous les cas : commit de l'implémentation future, entrée synthétique, préconditions, décision, observation d'effet/non-effet, preuve d'audit et dépendances persistantes. Une vérification documentaire de cette matrice n'est pas un PASS applicatif. Les tests peuvent être préparés avant disponibilité du modèle physique ; leurs assertions SQL ne doivent pas être inventées.

## 8. Plan de sortie borné

1. **API/Security** décident D01 et B01–B05 sur cette même version. Une réponse favorable à l'option B ne suffit pas à accepter le cycle de contexte ou le workflow grants.
2. **API/Security/DB** donnent les références ou deltas D02/D03/D04 nécessaires au composant activé. Une dépendance commune connue ne justifie pas de relancer tout le corpus ; une dépendance réellement manquante n'est pas masquée par le mot FINAL.
3. **QA** rattache C01–C17, fixe les résultats observables et conserve PLANNED/READY/BLOCKED tant que la preuve d'exécution manque.
4. **Propriétaires** publient les modifications normatives éventuelles avec leur vrai statut/version et un avant/après. Ne pas déclarer CONTENT CHANGE=NONE pour un amendement sémantique.
5. **MASTER** vérifie le gate existant #13 et la portée exacte de reprise. Ni cette proposition, ni sa fusion documentaire, ne clôturent les exports #6/#7/#9 ou n'autorisent SC-M02.

Les lignes B01–B03 sont prêtes à recevoir une décision de correspondance ciblée, pas prêtes à être codées sans les dépendances communes. B04–B05 restent en plus conditionnelles à la définition du parent set et des sous-ensembles de droits. Les opérations Unix/runtime, tous les parents techniques du provisioning et les 42 autres primitives IPC sont hors de cette conclusion.

Réponse attendue par propriétaire : **ligne/décision → ACCEPTER / AMENDER / REFUSER → justification → fichier/section/commit → effet sur version → test → dépendance restante**. Aucun accord global de type FINAL APPROVED=YES ne remplace ces décisions et le contenu des exports.

## 9. État d'exécution et limites de publication

```text
TARGETED DOCUMENT REVIEW = COMPLETED FOR THE FIVE LISTED BINDINGS
PROPOSAL = v0.2 / REVIEW
OWNER DECISIONS D01-D04 = NOT RECEIVED
CANONICAL ARTIFACTS CHANGED = NONE
APPLICATION IMPLEMENTATION / TEST EXECUTION = NONE
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

La recherche ciblée des exports manquants n'a pas livré de corps final exploitable pendant cette continuation. Les déclarations de publication historiques ne sont pas reprises comme fichiers complets. La PR #42 reste un lot d'outillage en attente de revue ; ses tests ne valident pas les contrats de la présente PR. Aucune nouvelle fonctionnalité périphérique n'est nécessaire pour examiner cette proposition.

[API]: https://github.com/yyogas/wcp360/blob/9c1a6cb62e3a81e75cbe40bf28671ea717421be4/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
[IPC]: https://github.com/yyogas/wcp360/blob/9c1a6cb62e3a81e75cbe40bf28671ea717421be4/specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
[SEC]: https://github.com/yyogas/wcp360/blob/9c1a6cb62e3a81e75cbe40bf28671ea717421be4/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
