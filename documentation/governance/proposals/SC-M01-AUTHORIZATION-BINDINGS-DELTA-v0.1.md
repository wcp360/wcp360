# WCP360 — Delta ciblé des liaisons d'autorisation SC-M01

Date : 2026-10-02. Version de proposition : v0.1. **Statut : REVIEW — NON NORMATIF, NON CONSOMMABLE PAR SOURCE CODE.**

Constat : `AUDIT-WCP-0104` ; dépendances `0103`, `0105`, `0114`. Suivi : [#41](https://github.com/yyogas/wcp360/issues/41). Gate inchangé : [#13](https://github.com/yyogas/wcp360/issues/13).

Préparé dans la coordination MASTER à la demande de l'utilisateur. Propriétaires devant décider : API / IPC / CONTRACTS et SECURITY / HARDENING ; arbitrage AUDIT / ARCHITECTURE REVIEW en cas de désaccord ; QA définit la preuve. Aucun avis de ces propriétaires n'est prétendu reçu.

Ce fichier apporte un **avant → correction proposée → résultat attendu**, plutôt qu'un nouvel audit général. Il n'est ni un export historique reconstitué, ni le modèle DB, ni la baseline QA, ni le ruleset Engineering manquants. Les sources canoniques restent inchangées. Une fusion de cette proposition ne constituerait pas une approbation du delta.

## 1. Références exactes

Référence examinée : `d802dfdfad25f91f030c7ff7656310d05f57c311` (main après publication de l'audit #40). Les exports concernés sont identiques à ceux du commit audité `aab5033f09a9b9d459d5c723344d7629306fd5ae`.

| Source | Version | Blob Git |
|---|---|---|
| [API][API] | 1.0 | `4893c5191b4928ecfa5a70f4dc14b39a8939fcea` |
| [IPC][IPC] | WCP360-IPC-B/1 | `fec9c74e8413db822662e47c76c40486e282334f` |
| [Security][SEC] | v1.0 | `6d74f2f363524511424f839ca7262143c2af95ab` |
| [Permission Registry][PERM] | v1.0 | `81f3d425f36470167aba4bfb7ba069268626342f` |

DOC v4.0.21.23, DESIGN V2.5.3.36.4, BM-0001 FINAL et CODE-BASELINE NONE restent inchangés.

## 2. Faits à distinguer des décisions

L'API fixe déjà les actions `servers.service.reload`, `servers.service.restart`, `network.firewall.apply` et `databases.grants.set`. La matrice Security indique pour les primitives correspondantes des noms en `.manage` dans sa colonne « Required action_id / approved parent binding ». Le même document exige que wcpd valide le couple permission/action d'origine et que wcp-privd borne la primitive et la cible.

Ces identifiants peuvent représenter des niveaux différents ; ils ne sont pas des synonymes automatiques. La proposition consiste à expliciter le **parent API autorisé** dans Security pour les chemins ci-dessous, sans inventer de permission et sans transformer une sous-opération en endpoint public.

## 3. Corrections proposées : cinq lignes bornées

Toutes les lignes sont **PENDING OWNER DECISION**. La colonne parent proposée désigne l'action API d'origine ; elle ne renomme pas une primitive IPC et ne tranche pas la signification du champ action_id de l'enveloppe IPC.

| Primitive IPC | Permission conservée | Security : parent/action actuel | Parent API proposé | Source et condition de portée |
|---|---|---|---|---|
| `service.reload` | `servers.services.manage` | `servers.services.manage` | `servers.service.reload` | API-WCP-0802 → IPC-WCP-1029 ; même server_id/service_id enregistré ; contexte AUTH-PRIV et révision de configuration selon contrat |
| `service.restart` | `servers.services.manage` | `servers.services.manage` | `servers.service.restart` | API-WCP-0803 → IPC-WCP-1030 ; même cible enregistrée ; reload ne confère pas restart |
| `firewall.policy.apply` | `network.firewall.manage` | `network.firewall.manage` | `network.firewall.apply` | API-WCP-0809 → IPC-WCP-1026 ; liaison explicite du même serveur et des révisions de politique |
| `database_grant.apply` | `databases.grants.manage` | `databases.grants.manage` | `databases.grants.set` | API-WCP-0305 → IPC-WCP-1023 ; uniquement les grants du desired set validé, même tenant/base/identité |
| `database_grant.revoke` | `databases.grants.manage` | `databases.grants.manage` | `databases.grants.set` | API-WCP-0305 → IPC-WCP-1024 ; uniquement la suppression dérivée du desired set validé ; ne crée pas un droit de révocation autonome |

Les compteurs de référence 82 permissions et 47 opérations ne sont pas modifiés par cette proposition. Une correspondance correcte n'est jamais une autorisation suffisante : toutes les contraintes d'authentification, surface, tenant, propriété/délégation, entitlement, état, MFA et approbation applicables restent requises.

### 3.1 Services : delta concret

Proposition de remplacer uniquement la cellule du parent autorisé dans les deux lignes service.reload/service.restart. Conserver permissions, surface, scope, audit, classe de résultat et primitives. Ne pas accepter `servers.services.manage` comme alias d'action seulement pour contourner une incompatibilité.

La traduction de `expected_config_revision` côté API vers `config_revision_id` côté IPC doit être précisée par API/IPC : une révision attendue et l'identifiant d'un document de configuration ne sont pas nécessairement le même type. La correction de nom d'action ne ferme pas cette dépendance.

### 3.2 Pare-feu : delta concret

Proposition limitée à la primitive apply. La correspondance entre `expected_current_revision` API et `expected_active_revision` IPC doit être déclarée et typée. `firewall.policy.validate` et `firewall.policy.rollback` ne reçoivent PAS automatiquement ce parent : le propriétaire doit préciser leurs parents techniques autorisés, ainsi que la différence entre rollback compensatoire interne et demande distincte.

L'API cite `firewall.policy.applied` comme événement d'audit approuvé, alors que Security utilise `network.firewall.applied`. API/Security doivent confirmer s'il s'agit de deux étapes distinctes ou d'une divergence. Aucun renommage ni alias d'événement n'est appliqué ici.

### 3.3 Grants : delta concret

Proposition de lier apply/revoke au set d'origine, sans SQL libre et sans élargissement de scope. La résolution de `privileges[]` API vers un `grant_set_id` immuable doit être définie par API/DB. Chaque sous-étape doit être bornée à la même base hébergée et à la même identité. L'échec entre une révocation et un ajout requiert l'état partiel/réconciliation déjà prévu, pas une transaction fictive entre control plane et moteur hébergé.

Ces deux lignes restent conditionnelles tant que les sous-étapes exactes du workflow set ne sont pas confirmées. Aucune table, colonne, FK, migration ou politique de transaction nouvelle n'est fabriquée ici.

## 4. Points que le delta ne doit pas résoudre arbitrairement

| Point | Pourquoi pas de remplacement automatique | Réponse propriétaire nécessaire |
|---|---|---|
| `linux_user.lock`, `linux_user.unlock`, `linux_user.delete` | API-WCP-0103 expose `applications.runtime.update` avec runtime_profile_id ; cela ne prouve pas que lock/unlock/delete sont des étapes de cette action | Identifier l'action parent et les transitions concernées ; fournir la décision normative existante ou un delta explicite |
| Provisioning filesystem, nginx et PHP | `approved parent` n'énumère pas l'ensemble des workflows | Liste fermée de parents et contraintes par cible ; pas de wildcard ni ajout global de permission ADMIN |
| `authorization_context_ref` et `authorization_context_id` | Le nom de la référence ne prouve pas son origine, son immutabilité ou son scope | Définir émetteur/résolveur, persistance, révocation, expiration, liaison au payload et à la primitive |
| `action_id` de l'enveloppe IPC | L'API, la table IPC et le contexte Security emploient des niveaux qui ne sont pas toujours identiques | Confirmer s'il transporte l'action d'origine ou une action technique ; démontrer la liaison de l'autre niveau au contexte de confiance |
| Services appelés par un workflow tenant | L'opération privilégiée n'implique pas un workflow ADMIN | Préserver les parents techniques autorisés sans accorder à USER l'endpoint ADMIN service.restart |

Il n'est pas nécessaire de créer un nouveau champ wire ou un nouveau moteur de policy pour répondre. Un lien vers un contrat existant suffisamment précis peut suffire. Tant que la sémantique de l'enveloppe n'est pas confirmée, aucune modification de dispatcher n'est autorisée.

## 5. Règles proposées de lecture de la matrice

Les termes suivants sont documentaires, pas de nouveaux champs DB/API : **action d'origine**, **workflow parent**, **étape technique**, **primitive IPC**.

1. Le parent API est lié à la permission d'origine et au contexte authentifié. Le contexte de confiance est produit côté contrôle, jamais accepté comme une déclaration de droits issue du navigateur.
2. Une sous-étape ne peut pas choisir une autre cible, une autre révision ou un autre ensemble de grants que ceux autorisés par son parent.
3. L'autorisation du parent ne rend pas chaque primitive accessible directement. Il faut à la fois le parent explicitement autorisé, la transition permise et toutes les contraintes Security.
4. La table ne doit pas être interprétée avec des correspondances par préfixe, wildcard, similarité de texte ou conversion permission → action.
5. Les décisions de compatibilité doivent être explicites : si la correction change une sémantique de contrat existante, publier le delta et son versioning par les propriétaires. Ne pas marquer artificiellement CONTENT CHANGE=NONE.

Ces formulations restent proposées pour ratification. Elles ne remplacent pas les règles normatives existantes et ne prétendent pas résoudre le cycle d'idempotence ou de durabilité.

## 6. Douze cas QA ciblés proposés

Identifiants locaux à cette proposition : C01–C12, sans réservation dans le registre TEST-WCP. **Tous PLANNED, non exécutés.** QA doit les lier aux TEST-API/TEST-IPC/Nxx existants et confirmer les codes exacts. ALLOW signifie ici éligibilité à une étape autorisée, pas succès système déjà observé.

| Cas | Précondition / action | Résultat attendu à ratifier | Effets interdits / preuve |
|---|---|---|---|
| C01 | Session ADMIN privilégiée valide ; parent reload ; cible et révision enregistrées | Éligible uniquement à service.reload si les autres gates passent | Aucun restart ; contexte/primitive/cible corrélés |
| C02 | Même contexte mais substitution de service.restart | DENY pour ce parent | Aucun effet système ni fallback |
| C03 | Parent restart valide vers service.restart, même cible | Éligible si tous les autres gates passent | Pas de duplication aveugle en cas de réponse perdue |
| C04 | Permission services.manage valide mais action d'origine remplacée par ce même texte | Pas d'alias implicite ; refus sans liaison normative explicite | Pas de reconnaissance de droits à partir de l'égalité de chaînes |
| C05 | Parent firewall.apply valide, bonne politique et révision | Éligible à apply | Pas de politique choisie librement par la sous-opération |
| C06 | Même contexte firewall, serveur ou révision substitué | DENY ou conflit selon le cas précis à fixer | Aucun effet sur la cible substituée |
| C07 | Parent grants.set et set validé pour tenant A/base A/identité A | Seules les sous-étapes dérivées de ce set sont éligibles | Pas de SQL libre ni de privilèges hors du set |
| C08 | Substitution tenant B, autre base ou autre identité avec le même contexte | DENY | Aucun effet sur moteur hébergé ; trace sûre corrélée |
| C09 | Appel direct de revoke sans parent set valide | DENY | La permission seule ne crée pas une opération publique autonome |
| C10 | Délégation révoquée après enqueue avant effet | Réévaluation selon Security, puis refus si le droit a disparu | Pas d'exécution avec un droit périmé |
| C11 | Parent runtime.update proposé comme autorisation de linux_user.delete sans preuve du workflow | Bloqué jusqu'à décision propriétaire ; aucune correspondance inventée | Pas de suppression Unix |
| C12 | Droit parent valide mais MFA expirée, entitlement absent ou approbation manquante lorsqu'exigés | Refus/reprise d'auth approprié ; aucun ALLOW anticipé | Aucun effet ; code/message sûrs à définir dans l'API |

Pour toute exécution future : SHA du candidat, environnement, entrée synthétique, décision, absence/présence d'effet, audit corrélé et assertions DB nécessaires. Tant que l'implémentation ou une dépendance normative manque, conserver PLANNED/BLOCKED ; aucun PASS fictif.

## 7. Handoff de décision borné

| Propriétaire | Réponse demandée |
|---|---|
| API / IPC / CONTRACTS | Accepter/corriger/refuser chacune des cinq lignes ; confirmer les traductions de révisions, grant_set et la sémantique action_id de l'enveloppe ; lier les sources exactes |
| SECURITY / HARDENING | Confirmer le parent autorisé par primitive et les bornes de scope ; ne pas généraliser aux opérations runtime/filesystem non résolues |
| DATABASE / DATA MODEL | Après publication de #6, indiquer les références existantes pour ressources, révisions, set et contexte ; aucun nouveau modèle construit dans ce delta |
| TESTS / QA | Rattacher C01–C12 aux tests canoniques et préciser statuts/codes/effets/audit ; publication de #7 reste distincte |
| MASTER / AUDIT | Trancher les désaccords seulement, conserver #41 et #13 ; ne pas certifier les autres 42 primitives sur la base de ces cinq lignes |

Réponse minimale par ligne : décision, justification, fichier/section et commit, effet sur versions, test attendu, dépendance et statut. Un accord global sans réponse aux questions wire/parents ne clôture pas AUDIT-WCP-0104.

## 8. Critères de sortie

Le delta sera prêt à être promu seulement après avis API et Security sur la même version, vérification des consommateurs, réponse aux dépendances du composant activé et traçabilité QA. Les modifications canoniques seront alors publiées séparément avec leur vrai statut de changement et leurs versions décidées. La simple existence de cette proposition n'autorise pas SOURCE CODE.

```text
PROPOSAL STATUS = REVIEW
OWNER APPROVAL = NOT RECEIVED
CANONICAL FILES CHANGED = NONE
APPLICATION CODE / DDL / MIGRATIONS = NONE
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```

[API]: https://github.com/yyogas/wcp360/blob/d802dfdfad25f91f030c7ff7656310d05f57c311/specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
[IPC]: https://github.com/yyogas/wcp360/blob/d802dfdfad25f91f030c7ff7656310d05f57c311/specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md
[SEC]: https://github.com/yyogas/wcp360/blob/d802dfdfad25f91f030c7ff7656310d05f57c311/specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
[PERM]: https://github.com/yyogas/wcp360/blob/d802dfdfad25f91f030c7ff7656310d05f57c311/specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md
