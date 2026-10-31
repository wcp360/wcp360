# WCP360 — Frontière de contrôle backend/worker → wcpd — proposition ciblée v0.1

Date : 2026-10-02. **PROPOSAL v0.1 — REVIEW — NON NORMATIF — NON CONSOMMABLE PAR SOURCE CODE.**

PR #43 ; constat existant `AUDIT-WCP-0105`, dépendances 0103/0104/0106/0108/0114 ; suivi #41 ; gate #13.

Cette proposition ne crée pas un « IPC-A » canonique et ne renomme pas IPC-B. Elle documente uniquement la frontière de contrôle déjà décrite entre les runtimes WCP-USER/WCP-ADMIN, le worker et `wcpd`, afin que les propriétaires puissent choisir les éléments physiques et protocolaires sans les laisser à l'implémentation.

Préparé par le même assistant dans la coordination MASTER. Décisions attendues d'API / IPC / CONTRACTS, SECURITY / HARDENING et DEVOPS / BUILD / RELEASE ; Database/QA/SRE interviennent uniquement sur les dépendances qui leur appartiennent. Aucune approbation indépendante ou propriétaire n'est prétendue.

## 1. Sources relues et statut

Main observé : `9c1a6cb62e3a81e75cbe40bf28671ea717421be4`.

### Canonique DevOps / Security

DevOps §5.1 fixe :

```text
WCP-USER / WCP-ADMIN backend runtime
→ control IPC
→ wcpd

control group = wcp360-control
```

Les runtimes `wcp360-user` et `wcp360-admin` sont membres de `wcp360-control`. Le worker `wcp360-worker` n'a **aucun groupe supplémentaire par défaut**. DevOps impose que les workers nécessitant un effet privilégié passent par le « normal control path » et interdit leur accès direct à IPC-B.

Security confirme :

```text
wcp360-user  → supplementary wcp360-control
wcp360-admin → supplementary wcp360-control
wcpd         → supplementary wcp360-priv
worker       → none by default
```

et interdit toute invocation privilégiée directe depuis Web/UI.

### Design System actif candidat

Le document Design `03-runtime-socket-locks.md` publie :

```text
/run/wcp360/wcpd.sock
```

avec exigences de socket : group restreint, `0660` ou plus strict, peer credentials, protocol version, taille maximale, timeout, correlation ID.

Un autre document historique/Design emploie encore des groupes génériques `wcp360` autour de ce socket. Le DevOps final v1.0 emploie explicitement `wcp360-control`.

**Conclusion de cette revue :** le chemin `/run/wcp360/wcpd.sock` dispose d'une preuve Design, mais son owner/group/mode et le protocole de contrôle ne sont pas suffisamment publiés dans le même niveau d'autorité que IPC-B. L'implémentation ne doit pas fusionner silencieusement les anciens groupes avec le modèle Unix final.

## 2. Décision C01 — identité physique du socket de contrôle

Proposition de convergence, **PENDING OWNER DECISION** :

```text
candidate path = /run/wcp360/wcpd.sock
candidate access group = wcp360-control
candidate mode = 0660 or stricter
server = wcpd
```

Les quatre éléments ne deviennent pas canoniques par cette proposition.

Points à trancher par DevOps/API/Security :

- owner exact du socket ;
- owner/group/mode du répertoire parent `/run/wcp360` ou d'un sous-répertoire dédié ;
- création par systemd/socket activation ou par le daemon ;
- politique au redémarrage et suppression d'un socket périmé ;
- confinement supplémentaire éventuel.

**Ne pas** réutiliser `wcp360-priv` : ce groupe appartient à la frontière `wcpd → wcp-privd` et ne doit pas devenir un groupe d'accès des backends.

## 3. Décision C02 — transport access != authorization

La possession du groupe `wcp360-control` ne doit autoriser que l'ouverture de la frontière de contrôle.

Proposition :

1. le serveur vérifie le peer Unix lorsque le transport le permet ;
2. le peer doit appartenir à un ensemble explicite d'identités de service autorisées pour **ce transport** ;
3. l'identité Unix n'est pas une permission applicative ;
4. la requête doit porter/référencer le contexte applicatif nécessaire à l'autorisation ;
5. `wcpd` réévalue les contraintes mutables requises avant enqueue/dispatch effectif, conformément à Security ;
6. un peer valide avec un contexte absent, expiré, incompatible ou hors scope échoue fermé.

Les identités browser/user final ne sont jamais déduites de l'UID Unix du runtime web.

## 4. Décision C03 — séparation WCP-USER / WCP-ADMIN

Les deux runtimes ont accès au groupe de transport. La séparation de surface doit donc être conservée dans le protocole et le contexte, pas supposée grâce au groupe Unix.

Réponse propriétaire attendue :

```text
peer unix identity →
allowed origin surface(s) →
accepted authentication/context class →
permission/action/resource checks
```

Un appel provenant du processus `wcp360-user` ne doit pas pouvoir présenter un contexte WCP-ADMIN et inversement. Un contexte BOTH/POLICY doit conserver sa surface d'origine et ses contraintes selon Security.

Le socket partagé éventuel ne doit pas effacer la séparation logique. Une décision de sockets séparés serait un changement architectural à documenter explicitement ; elle n'est pas imposée ici.

## 5. Décision C04 — voie du worker

Le point reste réellement ouvert :

```text
worker = wcp360-worker
supplementary control group = none by default
direct IPC-B = forbidden
privileged effects → normal control path
```

Deux catégories de solution sont possibles :

| Option | Description | Effet sur les contrats |
|---|---|---|
| W1 | le worker devient client direct du control IPC | nécessite un changement explicite de son accès Unix ou un transport distinct ; augmente le nombre de peers ; **ne peut pas être supposé** |
| W2 | le worker ne reçoit pas le socket ; il produit/consomme un état durable et un composant autorisé réalise ensuite le passage par wcpd | exige de définir producteur/consumer, claim, autorisation d'exécution et corrélation ; dépend de Database/jobs |

Cette revue **ne recommande pas automatiquement W1** : ajouter `wcp360-worker` à `wcp360-control` contredirait le « none by default » sans décision propriétaire.

W2 est cohérente avec la séparation actuelle mais reste incomplète tant que le chemin jobs/daemon exact n'est pas défini. API/DevOps/DB doivent choisir ou documenter une troisième solution bornée.

## 6. Décision C05 — contexte de confiance entrant dans wcpd

Security définit déjà le contenu minimal du contexte privilégié :

```text
authorization_context_id
origin_surface
origin_actor_type
origin_actor_reference
origin_tenant_id?
origin_permission_id
origin_action_id
resource_type
resource_id
job_id
correlation_id
ipc_operation_id
authorization_decision = ALLOW
authorization_decided_at
```

Ce contexte n'est pas un bearer credential et n'est pas accepté directement depuis le navigateur.

Le control boundary doit préciser :

- qui crée la décision initiale ;
- quel composant attribue l'identifiant/référence ;
- comment `wcpd` obtient le contexte authentique ;
- quelles valeurs de la requête doivent correspondre au contexte ;
- quelles propriétés sont réévaluées ;
- expiration/révocation ;
- comportement lorsque le stockage/résolveur est indisponible ;
- protection contre réutilisation hors cible/workflow.

Aucune valeur déclarée par le client de transport ne devient fiable simplement parce que l'UID Unix est autorisé.

## 7. Décision C06 — protocole de contrôle

Le Design exige version, taille maximale, timeout et correlation ID, mais le format complet n'est pas publié comme celui d'IPC-B.

Trois règles de compatibilité sont proposées :

1. **ne pas copier IPC-B implicitement** : le control IPC a des producteurs/consommateurs et une frontière de confiance différents ;
2. **réutiliser les types canoniques** là où ils ont le même sens : action_id, resource identifiers, correlation_id, job/error semantics, sans fusion de namespaces ;
3. toute structure de requête/réponse consommée par SC-M01 doit être publiée avant son implémentation.

API/IPC doit décider si le transport est :

- request/response direct ;
- soumission durable de job + lecture de statut ;
- combinaison bornée de ces deux modèles selon les opérations.

Le choix doit rester compatible avec T-JOB = 10s pour l'acceptation durable des actions asynchrones et avec la proposition timeout/idempotence de cette PR.

## 8. Décision C07 — responsabilité de wcpd

Proposition de frontière à ratifier :

```text
client de contrôle
→ authentification transport
→ parsing/version/schema
→ résolution du contexte fiable
→ autorisation/revalidation nécessaire
→ persistance/claim selon workflow
→ sélection d'une opération connue
→ IPC-B si effet privilégié
→ observation/résultat/reconciliation
```

Cela ne signifie pas que `wcpd` implémente toute logique métier. API/Architecture doit fixer ce qui reste backend versus daemon. En revanche :

- aucun passthrough de commande ;
- aucun `ipc_operation_id` arbitraire déduit d'un string UI ;
- aucun contexte de permission auto-déclaré ;
- aucun fallback direct vers `wcp-privd`.

## 9. Douze scénarios QA proposés

Identifiants locaux `CQ01–CQ12`, sans réservation TEST-* ; **PLANNED, zéro exécuté**.

| Cas | Entrée/adversaire | Résultat attendu à ratifier |
|---|---|---|
| CQ01 | peer Unix non autorisé ouvre le control socket | refus avant traitement métier ; aucun enqueue/dispatch |
| CQ02 | peer `wcp360-user` présente contexte surface ADMIN | refus ; aucun downgrade/fallback |
| CQ03 | peer `wcp360-admin` présente contexte USER substitué pour contourner une règle | refus ou traitement selon contrat explicite, jamais changement silencieux de surface |
| CQ04 | peer autorisé mais permission/action/ticket context inconnu | fail closed ; aucune opération privilégiée |
| CQ05 | ressource de la requête diffère de resource_id du contexte | refus ; aucune substitution de cible |
| CQ06 | contexte révoqué après création, avant effet | revalidation applicable puis refus si droit disparu |
| CQ07 | worker tente directement IPC-B | refus Unix/peer avant effet |
| CQ08 | worker tente directement le control socket alors que W1 n'est pas approuvé | refus transport ; ne pas ajouter le groupe pour faire passer le test |
| CQ09 | même job consommé simultanément par deux workers/composants | un seul claim/exécuteur selon contrat DB ; aucune duplication d'effet |
| CQ10 | opération inconnue au control boundary | aucun fallback en shell, generic exec ou passthrough IPC-B |
| CQ11 | daemon redémarre avec jobs/intents persistants | ne restaure pas aveuglément les locks éphémères ; reprise via état durable/réconciliation |
| CQ12 | correlation_id réutilisé avec autre cible | aucun droit dérivé de la corrélation ; authorization binding indépendant |

CQ09/CQ11 restent bloqués pour leurs assertions physiques tant que #6 n'est pas matérialisé. Les autres peuvent être transformés en tests de contrat dès publication du protocole de contrôle.

## 10. Handoff propriétaire

### API / IPC / CONTRACTS

Répondre :

```text
C01 socket/protocol identity =
C03 surface binding =
C05 trusted-context resolution =
C06 request/response/job protocol =
C07 wcpd responsibility boundary =
```

Publier le schéma minimal consommé par SC-M01, ses erreurs, timeouts, idempotence et compatibilité.

### SECURITY / HARDENING

Confirmer :

- peers autorisés ;
- surface-to-peer invariants ;
- revalidation ;
- contextual binding ;
- worker denial/direct-path rules ;
- fail-closed behavior.

### DEVOPS / BUILD / RELEASE

Confirmer :

- socket path/owner/group/mode/runtime directory ;
- systemd lifecycle ;
- identités/groupes ;
- worker transport access ;
- permissions après reboot/upgrade.

### DATABASE / DATA MODEL

Uniquement si W2 ou un job durable est retenu : pointer vers claim/concurrency/job/context persistence déjà approuvés après #6. Aucun nouveau modèle ici.

### TESTS / QA

Rattacher CQ01–CQ12 aux tests existants, sans faux PASS/FAIL.

## 11. Critères de sortie

Le control boundary est source-ready uniquement lorsque transport + peer auth + protocol + surface binding + context resolution + worker path + errors/timeouts sont définis sur la même version.

```text
TARGETED REVIEW = COMPLETE
OWNER DECISIONS = NOT RECEIVED
CONTROL SOCKET CANONICAL PATH = NOT DECIDED BY THIS PROPOSAL
WORKER PATH = NOT DECIDED
CANONICAL CONTENT CHANGED = NONE
APPLICATION TESTS EXECUTED = 0
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

L'existence de `/run/wcp360/wcpd.sock` dans le Design est une donnée d'entrée, pas une permission pour SOURCE CODE de choisir silencieusement owner/group/protocole. La présente proposition évite précisément ce choix implicite.
