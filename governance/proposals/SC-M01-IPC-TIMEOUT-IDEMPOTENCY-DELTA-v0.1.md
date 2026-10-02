# WCP360 — IPC-B délais, idempotence et issue incertaine — proposition ciblée v0.1

Date : 2026-10-02. **PROPOSAL v0.1 — REVIEW — NON NORMATIF — NON CONSOMMABLE PAR SOURCE CODE.**

PR #43 ; constats existants AUDIT-WCP-0106, AUDIT-WCP-0107 et AUDIT-WCP-0108 ; dépendances 0105/0114 ; suivi #41 ; gate #13 inchangé.

Préparé par le même assistant dans la coordination MASTER après relecture des exports API 1.0, IPC WCP360-IPC-B/1, DevOps v1.0 et du draft QA non autoritaire. Ce document ne remplace ni un avis de `WCP360 — API / IPC / CONTRACTS`, ni Database, ni Tests / QA. Aucune modification canonique n'est effectuée.

## 1. Sources et faits déjà établis

Référence relue : main `9c1a6cb62e3a81e75cbe40bf28671ea717421be4`.

L'IPC fixe déjà :

```text
CONNECT TIMEOUT = 1 second
I/O TIMEOUT = 5 seconds
DEFAULT OPERATION TIMEOUT = 30 seconds
MAX OPERATION TIMEOUT = 900 seconds
request.deadline_ms = required
```

Le registre de 47 opérations fixe en plus un timeout par opération, de 10 à 900 secondes selon le cas.

L'API fixe :

```text
T-READ  = 10 seconds
T-WRITE = 15 seconds
T-JOB   = 10 seconds to durably accept/persist a job
```

et distingue déjà :

```text
timeout != definite failure
retry != reconciliation
rollback != compensation
execution success != verified success
```

API et IPC imposent également :

```text
dispatch possibly occurred + completion cannot be proven
→ UNKNOWN_OUTCOME
→ reconciliation
→ no blind retry
```

**Constat à fermer :** les documents ne définissent pas encore précisément à quelle phase s'appliquent les 5 secondes d'I/O, comment `deadline_ms` se compose avec le timeout par opération, ni quel résultat est attendu lorsqu'un délai expire avant ou après un dispatch potentiellement effectif.

## 2. Principe proposé : un budget, plusieurs phases, aucune nouvelle trame

La proposition **ne crée pas d'ACK, de heartbeat ou de nouveau message IPC**. Elle distingue uniquement les phases d'une requête pour appliquer les valeurs déjà présentes.

Les noms P0–P6 ci-dessous sont documentaires, pas de nouveaux états Job :

| Phase | Début | Fin | Effet externe possible ? |
|---|---|---|---|
| P0 CONNECT | tentative de connexion | socket connecté/authentifiable | non |
| P1 REQUEST_RX | lecture du préfixe de taille | requête JSON complète | non |
| P2 VALIDATE_AUTH | requête complète | schéma, pair, binding, cible et contexte validés | non |
| P3 PRE_DISPATCH_DURABILITY | validation terminée | preuve obligatoire pré-effet durable | non |
| P4 DISPATCH_EXECUTE | primitive explicitement déclenchée | résultat système disponible ou issue incertaine | oui |
| P5 VERIFY_RECONCILE | observation/postcondition ou reprise après incertitude | résultat vérifié/reconcilié | oui, déjà possible |
| P6 RESPONSE_TX | résultat durable disponible | réponse complètement écrite ou connexion perdue | effet déjà possible |

Une implémentation peut organiser son code autrement. Ce modèle ne devient normatif que si les propriétaires le ratifient ; il sert à rendre les cas de timeout non ambigus.

## 3. Décision T01 proposée : portée du I/O TIMEOUT = 5s

Deux lectures sont possibles :

| Option | Sens | Conséquence |
|---|---|---|
| A | délai total d'attente de toute réponse | incompatible en pratique avec les opérations autorisées à 60/120/900s sauf nouveau protocole/streaming |
| B | délai de progression des **transferts de framing** P1/P6 ; le temps d'exécution P4/P5 est gouverné par le budget d'opération | **Recommandée, PENDING OWNER DECISION** |

Sous B, un blocage de lecture du corps de requête ou d'écriture du résultat au-delà du budget I/O est traité comme un échec de transport. En revanche, le serveur n'est pas obligé de produire des octets toutes les cinq secondes pendant une primitive de 900 secondes.

Cela ne signifie pas qu'une connexion peut rester sans borne : le budget d'opération et le deadline effectif restent obligatoires.

## 4. Décision T02 : sémantique de deadline_ms

Le nom `deadline_ms` ne suffit pas à déterminer s'il s'agit :

- d'une échéance absolue ;
- d'une durée relative ;
- d'un budget calculé ailleurs.

**Ne pas choisir silencieusement.**

Proposition à ratifier :

1. `deadline_ms` représente une **échéance monotone ou une valeur transportée permettant de dériver un budget restant sans dépendre d'une horloge murale non fiable** ;
2. le wire contract doit préciser sa représentation exacte avant implémentation ;
3. une valeur expirée avant P4 interdit le dispatch ;
4. une valeur au-delà du maximum autorisé est bornée/rejetée selon la politique propriétaire, jamais utilisée pour contourner le MAX OPERATION TIMEOUT ;
5. aucun client ne peut augmenter le plafond défini pour l'opération.

Alternative acceptable : échéance UTC absolue, uniquement si le contrat ajoute explicitement les exigences d'horloge, dérive tolérée et comparaison. Cette revue ne choisit pas l'encodage.

## 5. Décision T03 : budget d'exécution effectif

Formule proposée, sans modifier la table des 47 opérations :

```text
effective_operation_budget
=
min(
  budget restant issu de deadline_ms,
  timeout de la ligne IPC,
  MAX OPERATION TIMEOUT
)
```

Le DEFAULT OPERATION TIMEOUT = 30s s'applique seulement si une opération/version explicitement autorisée ne possède pas de timeout spécifique. Il ne remplace pas les valeurs 60/120/900s déjà présentes dans le registre.

Le CONNECT TIMEOUT de 1s est séparé et ne prolonge pas le deadline. Le temps consommé avant P4 réduit le budget restant ; une validation lente ne doit pas offrir un nouveau budget complet à la primitive.

**PENDING OWNER DECISION :** préciser si le timeout de ligne est un maximum dur de P4 seul ou du traitement P2→P5. Cette proposition recommande P2→P5 pour éviter qu'une longue prévalidation permette de dépasser le délai total d'une opération.

## 6. Décision T04 : résultat selon la frontière de dispatch

**T04 = OPEN / PARTIALLY DEFINED.**

The post-dispatch uncertain-result rule is already FINAL in IPC-B/1 §5:

    timeout != definite failure
    execution may have occurred but cannot be proven
    → UNKNOWN_OUTCOME
    → reconciliation
    → no blind retry
    execution success != verified success

The open work is therefore not to choose whether UNKNOWN_OUTCOME exists. It is to define the phase boundary and transport/persistence behavior around that existing rule.

Evidence companion:

    governance/proposals/SC-M01-RECOVERED-AUTHORIZATION-CONTEXT-OUTCOME.md

La distinction proposée repose sur **ce qui peut être prouvé**, pas uniquement sur l'horloge.

### Avant dispatch

Si le deadline/budget expire en P0–P3 et que le système peut prouver qu'aucune primitive n'a été déclenchée :

```text
NO EXTERNAL EFFECT
reconciliation_required = false
```

Le code exact peut être `OPERATION_TIMEOUT` ou une erreur de transport/validation selon la phase ; API/IPC doit fixer la priorité. **Ne jamais retourner UNKNOWN_OUTCOME simplement parce qu'un délai a expiré avant dispatch.**

### Après dispatch

The following branch is already canonical and must not be weakened:

    effet possible mais résultat non prouvable
    → UNKNOWN_OUTCOME
    → reconciliation_required = true
    → no blind retry

The remaining classification details around definite absence / durable verified result / response-loss recovery still require owner clarification.

Si P4 a commencé :

- effet définitivement absent et prouvable → erreur déterministe, pas UNKNOWN_OUTCOME ;
- effet et résultat définitivement vérifiés → résultat correspondant, même si la réponse au client est ensuite perdue ;
- effet possible mais résultat non prouvable → `UNKNOWN_OUTCOME`, `reconciliation_required = true`, aucune répétition aveugle.

Une perte en P6 après résultat durable ne transforme pas le résultat stocké en échec. Le client doit retrouver le même résultat logique via l'idempotence/job/reconciliation appropriée, sans redéclencher la primitive.

## 7. Décision T05 : idempotence ≠ identité de transport

Les sources utilisent plusieurs identifiants :

```text
Idempotency-Key côté API
request_id
job_id?
action_id
ipc_operation_id
operation-ID protected / key-required / revision-protected...
```

Proposition :

- `request_id` identifie une tentative IPC et ne doit pas être utilisé seul comme clé métier durable de déduplication ;
- `correlation_id` relie les preuves mais n'accorde aucune autorité et ne constitue pas à lui seul l'idempotence ;
- `job_id` peut relier une exécution durable si le workflow en possède un, sans présumer que tous les appels IPC ont un job ;
- les qualificatifs `key-required`, `operation-ID protected`, `plan-ID protected`, `revision-protected`, `desired-state protected` doivent être reliés à une **clé de déduplication et une portée persistantes définies par le propriétaire**, pas interprétés comme synonymes.

Database reste propriétaire des contraintes physiques : unicité, persistance, rétention, transaction et reprise. Ce document ne crée ni table ni index.

## 8. Décision T06 : état durable minimum attendu avant effet

Les sources imposent déjà « durable pre-dispatch evidence ». Pour rendre cette règle testable sans inventer le schéma, le propriétaire doit confirmer les propriétés minimales suivantes :

- identité logique de l'intention / opération ;
- cible et révision/plan/set validés ;
- contexte de corrélation ;
- état indiquant qu'aucun effet n'est encore confirmé ;
- protection contre une seconde exécution concurrente du même effet logique ;
- capacité de retrouver l'intention après redémarrage ;
- lien avec le mécanisme d'audit/outbox applicable.

Le nom des colonnes/tables et l'ordre transactionnel exact restent **BLOCKED ON #6** lorsqu'ils dépendent du contrat physique Database.

## 9. Décision T07 : redémarrage et ownership d'une opération incertaine

Scénario critique :

```text
durable intent
→ dispatch
→ external effect occurs
→ process/socket failure
→ durable final result missing
→ process restarts
```

Le redémarrage ne doit ni marquer SUCCEEDED par hypothèse, ni redéclencher automatiquement l'effet.

Réponse propriétaire attendue :

1. quel composant revendique l'opération pour réconciliation ;
2. quelle preuve empêche deux réconciliateurs d'agir simultanément ;
3. comment une revendication périmée est reprise ;
4. quelles observations déterminent « effet absent », « effet présent », « encore inconnu » ;
5. à quel moment l'opération redevient exécutable, si elle peut l'être ;
6. comment audit et job convergent vers un état final.

Cette section ne choisit pas advisory locks, leases DB, `SKIP LOCKED` ou un autre mécanisme : cela appartient au contrat DB/architecture.

## 10. Dix scénarios QA supplémentaires proposés

Identifiants locaux `TQ01–TQ10`, sans réservation dans le registre TEST-WCP. **Tous PLANNED, zéro exécuté.** QA doit les rattacher aux TEST-IPC existants et à N14/N15/N16/N17/N18/N25.

| Cas | Injection | Résultat attendu à ratifier |
|---|---|---|
| TQ01 | connexion impossible >1s | aucun dispatch ; erreur transport sûre ; aucune intention d'effet créée sauf politique explicitement définie |
| TQ02 | client envoie un frame partiel puis bloque >5s | connexion close/refus ; zéro primitive |
| TQ03 | request complète, deadline déjà expiré avant P4 | zéro primitive ; résultat déterministe, pas UNKNOWN_OUTCOME |
| TQ04 | deadline demandé supérieur au timeout de la ligne | le plafond de l'opération gagne ; aucun allongement par le client |
| TQ05 | opération 120s reste silencieuse >5s mais respecte son budget effectif | ne doit pas échouer uniquement parce que I/O TIMEOUT=5s sous option B |
| TQ06 | deadline expire avant dispatch durable | zéro effet ; preuve d'absence de dispatch |
| TQ07 | effet exécuté puis ACK/réponse finale perdue | UNKNOWN_OUTCOME ou résultat durable récupérable selon preuve ; aucun blind retry |
| TQ08 | même demande logique réémise avec nouveau request_id après perte de réponse | ne doit pas provoquer un deuxième effet si l'idempotence durable indique une opération existante |
| TQ09 | deux exécuteurs concurrents revendiquent la même opération à reprendre | un seul peut déclencher/réconcilier selon le contrat propriétaire ; aucun double effet |
| TQ10 | réconciliation après restart conclut que l'effet a eu lieu | finalisation durable/audit cohérents ; ne pas redispatcher pour « confirmer » |

Pour TQ05, si le propriétaire choisit l'option A ou un protocole à messages intermédiaires, le résultat doit être amendé explicitement plutôt que conservé artificiellement.

## 11. Réponses exactes demandées aux propriétaires

### API / IPC / CONTRACTS

Répondre T01–T05 :

```text
T01 I/O timeout semantics =
T02 deadline_ms representation =
T03 effective budget scope =
T04 phase/error boundary around canonical UNKNOWN_OUTCOME rule =
T05 identifier/idempotency binding =
```

Pour chaque réponse : décision, section canonique à modifier ou référence existante, compatibilité/versioning, erreur/outcome, test.

### DATABASE / DATA MODEL

Après matérialisation #6, pointer T06/T07 vers les structures physiques déjà approuvées :

```text
dedupe uniqueness =
intent/job persistence =
claim/concurrency rule =
outbox/audit durability =
reconciliation state =
retention/recovery =
```

Ne pas générer DDL ou migration pour répondre.

### TESTS / QA

Rattacher TQ01–TQ10 aux références existantes, fixer les préconditions, résultat exact, effet interdit et preuve. Garder PLANNED/READY/BLOCKED tant que l'implémentation manque ; aucun PASS/FAIL fictif.

### SRE / DEVOPS / SECURITY

- DevOps : confirmer les conséquences de connexion/socket et la supervision sans redéfinir le protocole ;
- SRE : observation/reconciliation et corrélation, sans inventer la persistance DB ;
- Security : fail-closed avant effet et absence de fallback/retry destructif.

## 12. Critères de sortie

Cette proposition peut être promue seulement lorsque :

1. T01–T05 sont décidés par API/IPC ;
2. les dépendances T06/T07 nécessaires au premier composant ont une référence Database vérifiable ;
3. QA a rendu les scénarios observables ;
4. les changements sémantiques, s'il y en a, sont versionnés comme tels ;
5. les consommateurs sont vérifiés contre la même version.

```text
TARGETED REVIEW = COMPLETE
OWNER DECISIONS = NOT RECEIVED
CANONICAL CONTENT CHANGED = NONE
DDL / MIGRATIONS = NONE
APPLICATION TESTS EXECUTED = 0
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

La présente proposition ne prétend pas fermer les exports #6/#7/#9 ni définir toute la machine de jobs. Elle ferme uniquement le travail de préparation nécessaire pour qu'un propriétaire puisse trancher les ambiguïtés de délais/idempotence sans relancer l'audit général.
