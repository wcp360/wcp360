# WCP360 — API transport / DTO / HTTP mapping — proposition ciblée v0.1

Date : 2026-10-02. **PROPOSAL v0.1 — REVIEW — NON NORMATIF — NON CONSOMMABLE PAR SOURCE CODE.**

PR #43 ; constat existant `AUDIT-WCP-0103`, dépendances 0104/0105/0106/0107/0114 ; suivi #41 ; gate #13 inchangé.

Préparé à partir de l'export API 1.0 et de la baseline Security v1.0 présents dans main `9c1a6cb62e3a81e75cbe40bf28671ea717421be4`. Ce document n'invente pas un OpenAPI, ne réintroduit pas V4.1 « Executable Contracts », n'attribue pas de nouvelle route canonique et ne modifie aucun fichier sous `specifications/`.

## 1. Faits déjà publiés

L'export API fournit pour 86 contrats :

- `contract_id`, version, producer/surface/authentication ;
- `permission_id`, `action_id`, `resource_type`, tenant scope ;
- liste logique de champs de requête ;
- nom logique de réponse ;
- profil d'erreur ;
- timeout/retry/idempotency ;
- audit, sécurité, compatibilité et référence TEST-API.

Il fixe aussi :

```text
unknown request field = VALIDATION_FAILED
T-READ = 10s
T-WRITE = 15s
T-JOB = 10s to durably accept/persist a job
```

et les profils RR/RK/RN.

Il **ne publie pas** dans le même export :

- méthode HTTP ;
- chemin/route ;
- emplacement exact des paramètres (path/query/body/header) ;
- définition complète des DTO tels que `JobAccepted`, `Mutation<T>`, `*View/Collection`, `SecretCreated` ;
- mapping HTTP des `error_code` ;
- format commun de l'erreur ;
- règles de pagination ;
- union discriminée des contrats avec champ `operation` ;
- dictionnaire complet des codes `AU0..AU5`, `S-T/S-P/S-X/...` utilisés dans le tableau ;
- règle entre `Idempotency-Key` de transport et les occurrences logiques `idempotency_key` listées dans les schémas.

Security fixe pour les requêtes navigateur mutantes :

```text
valid session
+ synchronizer CSRF token
+ exact expected Origin
```

et exige des origines USER/ADMIN distinctes.

## 2. Principe proposé : compléter, pas redéfinir

La table API 1.0 reste le registre sémantique. Une **fiche transport** doit simplement rendre chaque contrat activé implémentable sans invention.

Aucun besoin d'imposer OpenAPI : Markdown versionné suffit si les champs ci-dessous sont déterministes.

Pour chaque `API-WCP-xxxx` activé, publier au minimum :

```text
contract_id
HTTP method
route template
path parameters
query parameters
headers
request content type
body schema / discriminator
success HTTP status
success DTO
error HTTP mapping
pagination when applicable
cache policy
surface/origin
authentication transport
CSRF requirement
idempotency source
version negotiation
```

## 3. Décision H01 — stratégie de version HTTP

Trois options usuelles sont possibles :

| Option | Exemple | Évaluation |
|---|---|---|
| V1 — version dans le chemin | `/api/v1/...` | simple et explicite |
| V2 — version par media type/header | `Accept: application/vnd...v1+json` | plus complexe |
| V3 — version par host | host/version séparés | coût opérationnel supérieur |

**Aucune option n'est canonique aujourd'hui.**

Cette revue recommande **V1 comme choix de simplicité**, uniquement **PENDING OWNER DECISION**. Le numéro `1.0` de l'artefact n'autorise pas à inventer `/api/v1` sans ratification.

La version HTTP doit préserver la règle existante : changement sémantique, renommage/suppression de champ requis ou changement permission/action = breaking.

## 4. Décision H02 — méthode et route : règle de dérivation interdite

Ne pas déduire automatiquement la méthode HTTP du suffixe de l'action.

Exemples :

- `*.read` sera vraisemblablement GET, mais la présence de filtres complexes ne fixe pas à elle seule la route ;
- `*.set`, `*.update`, `*.manage` peuvent relever de PATCH/POST/PUT selon la sémantique ;
- les opérations destructrices ne doivent pas devenir GET ;
- un contrat à champ `operation` peut être un endpoint de commande ou plusieurs routes séparées.

**Décision propriétaire exigée pour chaque endpoint activé.**

La fiche transport doit interdire toute mutation par GET/HEAD/OPTIONS, conformément à Security.

## 5. Décision H03 — emplacement des paramètres

Les listes actuelles `{tenant_id,...}` décrivent des entrées logiques, pas nécessairement un JSON body.

Proposition de règles à ratifier :

- identifiant de ressource servant à l'adressage principal → path si la route le décide ;
- filtres de lecture/pagination → query ;
- mutation structurée → JSON body ;
- données d'auth/session/CSRF/idempotence/version → headers/cookies selon contrat de transport ;
- aucune information d'autorisation ne devient fiable parce qu'elle est dans le path/body ;
- les champs servant à la cible doivent être recoupés avec la ressource autorisée et le contexte.

Une même donnée ne doit pas être acceptée simultanément depuis plusieurs emplacements sans règle de priorité/refus.

## 6. Décision H04 — Idempotency-Key

**H04 = OPEN / PARTIALLY DEFINED.**

L'API canonique dit déjà :

```text
RK: Idempotency-Key required for mutations
same key + same semantic request = same logical result
same key + different request = IDEMPOTENCY_CONFLICT
```

Ces semantics sont donc FINAL. Ce qui reste ouvert est uniquement le **binding de transport**.

De nombreuses lignes listent aussi `idempotency_key` dans leur schéma logique.

Proposition :

### Option I1 — clé de transport uniquement

```text
HTTP header: Idempotency-Key
body field: absent du wire format
```

Le champ listé dans la table serait compris comme une exigence logique, pas comme une propriété JSON.

### Option I2 — clé présente dans header et body

Si conservée pour compatibilité, **les deux valeurs doivent être identiques** ; absence/divergence selon profil RK/RN → validation/idempotency error avant mutation.

**Recommandation : I1 pour une nouvelle implémentation, PENDING OWNER DECISION**, afin d'éviter deux sources de vérité.

Evidence companion:

    governance/proposals/SC-M01-RECOVERED-TRACEABILITY-IDEMPOTENCY.md

L'owner ne doit pas redécider les semantics same-key/same-request et same-key/different-request : seule la représentation HTTP, la règle de duplication éventuelle et le mapping d'erreur restent ouverts.

Database reste propriétaire de la portée, canonicalisation, unicité, rétention et stockage du résultat.

## 7. Décision H05 — DTO communs à définir

Les noms suivants sont utilisés par le registre et doivent avoir une définition versionnée avant code :

### `JobAccepted`

Propriétés minimales à décider :

```text
job_id
state
correlation_id
resource reference?
accepted_at?
links/status reference?
```

Ce document **ne rend aucun champ obligatoire** : il liste le minimum à trancher.

Le statut HTTP doit exprimer que l'intention/job est durablement accepté, pas que l'effet externe est réussi.

### `Mutation<T>`

Doit préciser :

- projection de ressource après mutation ou référence ;
- nouvelle révision ;
- correlation/audit reference selon exposition autorisée ;
- absence de secret.

### `Collection<T>`

Doit préciser :

- collection/items ;
- cursor suivant ;
- limite ;
- ordre stable ;
- comportement cursor invalide/expiré.

### `SecretCreated`

Doit respecter Security :

- valeur sensible montrée une seule fois lorsqu'autorisé ;
- `Cache-Control: no-store` ;
- jamais de GET ultérieur pour un secret classé NEVER_REVEAL ;
- pas de secret dans logs/audit.

## 8. Décision H06 — format d'erreur HTTP

Le registre de `error_code` existe. Le format HTTP exact manque.

Proposition de structure logique à ratifier :

```text
{
  error_code,
  safe_message,
  correlation_id,
  field_errors?   // seulement détails sûrs
}
```

Interdits dans la réponse, conformément à Security :

```text
stack trace
SQL
filesystem path
command line
environment
secret/token/session id
private key
raw upstream credential response
```

### Mapping de statuts

Cette proposition **ne fixe pas tous les codes HTTP**. Le propriétaire doit publier une table déterministe au moins pour :

- validation ;
- authentication required ;
- authorization/policy/entitlement denied ;
- MFA/reauth/approval required ;
- not found ;
- resource/concurrency/idempotency conflict ;
- rate limit ;
- timeout ;
- dependency/service unavailable ;
- unknown outcome ;
- internal error.

Une même erreur logique ne doit pas changer de status arbitrairement selon le module.

## 9. Décision H07 — authentification, CSRF et CORS

Pour une session navigateur :

```text
surface USER → session USER + origin USER + CSRF USER
surface ADMIN → session ADMIN + origin ADMIN + CSRF ADMIN
```

Aucun cookie de l'autre surface ne constitue une autorisation.

Pour un token/scoped API client, le contrat doit préciser :

- header de transport ;
- type/token class accepté ;
- CSRF non applicable seulement si aucune auth cookie n'est utilisée ;
- origine/CORS applicable ;
- scope/resource restrictions.

Ne pas accepter simultanément session + token avec une priorité implicite. Si plusieurs mécanismes sont présents, le propriétaire doit fixer refus ou sélection déterministe.

## 10. Décision H08 — champs `operation` discriminés

Plusieurs contrats `*.manage` prennent :

```text
{operation, ... champs optionnels ...}
```

Une implémentation ne doit pas accepter un sac de champs optionnels sans discriminant strict.

Pour chaque valeur d'`operation`, définir :

- enum autorisée ;
- champs requis ;
- champs interdits ;
- permission/action inchangée ou sous-action explicitement liée ;
- idempotence ;
- réponse ;
- erreurs ;
- audit.

Un champ applicable à une autre opération présent dans le même body doit être rejeté, pas ignoré silencieusement.

## 11. Décision H09 — pagination et tri

Pour les réponses Collection :

- `limit` doit avoir des bornes documentées ;
- cursor opaque recommandé ;
- ordre stable obligatoire ;
- le cursor ne transporte pas d'autorité ;
- changement de tenant/surface/filtres avec un cursor existant doit être refusé ou produire un cursor invalide selon contrat ;
- aucune donnée d'un autre tenant ne doit être récupérée par réutilisation d'un cursor.

Les valeurs numériques exactes peuvent être définies par le propriétaire ou plus tard si elles n'affectent pas SC-M01, mais le mécanisme ne doit pas être inventé dans chaque module.

## 12. Décision H10 — cache et réponses sensibles

Security exige `Cache-Control: no-store` pour HTML authentifié et APIs sensibles.

La fiche transport doit classifier les réponses :

```text
sensitive/authenticated no-store
read-only potentially cacheable? → seulement si explicitement autorisé
secret response → no-store obligatoire
error response sensible → no-store
```

Aucun cache partagé ne doit mélanger tenants ou surfaces.

## 13. Fiche minimale d'exemple — non canonique

Exemple de **forme**, pas de route réelle :

```text
CONTRACT = API-WCP-0802
METHOD = <OWNER DECISION>
ROUTE = <OWNER DECISION>
SURFACE = WCP-ADMIN
AUTH = AUTH-PRIV
PERMISSION = servers.services.manage
ACTION = servers.service.reload
PATH = <OWNER DECISION>
QUERY = <OWNER DECISION>
HEADERS = Idempotency-Key + auth/session headers
BODY = <typed schema after path/query extraction>
SUCCESS STATUS = <OWNER DECISION>
SUCCESS DTO = JobAccepted v1
ERROR MAP = API Error Transport Map v1
CSRF = required for browser session mutation
CACHE = no-store
TEST = TEST-API-0802 + transport cases
```

SOURCE CODE ne doit pas remplir les `<OWNER DECISION>` lui-même.

## 14. Quinze scénarios QA proposés

Identifiants locaux `HQ01–HQ15`, aucun TEST-* réservé. **PLANNED, zéro exécuté.**

| Cas | Entrée | Résultat attendu à ratifier |
|---|---|---|
| HQ01 | mutation via GET | 0 mutation ; méthode refusée |
| HQ02 | champ inconnu | VALIDATION_FAILED |
| HQ03 | champ requis manquant | VALIDATION_FAILED |
| HQ04 | même clé idempotence, même requête | même résultat logique, pas deuxième effet |
| HQ05 | même clé, requête différente | IDEMPOTENCY_CONFLICT |
| HQ06 | header/body idempotence divergents si I2 retenu | refus avant mutation |
| HQ07 | session USER sur route ADMIN | refus surface/auth |
| HQ08 | cookie valide, CSRF manquant | 403/no mutation pour navigateur |
| HQ09 | Origin incorrect | 403/no mutation |
| HQ10 | token non-cookie avec CSRF absent | résultat selon contrat token, pas application mécanique de règle session |
| HQ11 | champs d'une autre valeur `operation` inclus | validation refusée |
| HQ12 | cursor réutilisé avec autre tenant/filter | aucune fuite ; cursor refusé/invalide |
| HQ13 | JobAccepted reçu | preuve que job/intention est durable, pas effet externe réussi |
| HQ14 | erreur interne | aucun stack/path/secret ; correlation_id sûr |
| HQ15 | réponse SecretCreated | no-store ; secret non récupérable ultérieurement si SHOW_ONCE/NEVER_REVEAL applicable |

## 15. Handoff propriétaire

### API / IPC / CONTRACTS

Répondre H01–H10 pour les endpoints **nécessaires à SC-M01** seulement avant d'étendre au catalogue complet.

Livrables attendus :

```text
Transport Profile v1
Error HTTP Map v1
DTO definitions v1
Pagination profile v1
Operation discriminator definitions for activated contracts
per-contract method/route binding
```

Les noms ci-dessus sont descriptifs, pas des identifiants de baseline réservés.

### SECURITY / HARDENING

Valider :

- auth transport par surface ;
- session/token coexistence ;
- CSRF/origin/CORS ;
- no-store ;
- safe errors ;
- secret projection.

### TESTS / QA

Rattacher HQ01–HQ15 aux TEST-API existants et définir les résultats exacts. Aucun PASS avant implémentation.

## 16. Critères de sortie

AUDIT-WCP-0103 est source-ready pour un endpoint lorsque sa fiche permet d'écrire sans invention :

1. route/méthode ;
2. emplacement et types de chaque input ;
3. DTO success ;
4. erreur/status ;
5. auth/surface/CSRF ;
6. idempotence ;
7. pagination/cache si applicable ;
8. tests positifs/négatifs.

```text
TARGETED REVIEW = COMPLETE
OWNER DECISIONS = NOT RECEIVED
ROUTES CREATED = NONE
OPENAPI CREATED = NONE
CANONICAL CONTENT CHANGED = NONE
APPLICATION TESTS EXECUTED = 0
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```
