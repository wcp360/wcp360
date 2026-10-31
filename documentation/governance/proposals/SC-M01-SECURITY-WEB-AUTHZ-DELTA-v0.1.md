# WCP360 — Isolation Web et composition d'autorisation — proposition ciblée v0.1

Date : 2026-10-02. **PROPOSAL v0.1 — REVIEW — NON NORMATIF — NON CONSOMMABLE PAR SOURCE CODE.**

PR #43 ; constats `AUDIT-WCP-0117` et `AUDIT-WCP-0118` ; dépendances 0103/0104/0105/0114 ; suivi #41 ; gate #13 inchangé.

Sources relues : Permission Registry v1.0, Security Baseline v1.0 et audit consolidé au main `9c1a6cb62e3a81e75cbe40bf28671ea717421be4`.

Aucun hostname, port, certificat, proxy, algorithme de rôle ou pouvoir d'override n'est rendu canonique par ce document. Décisions attendues de SECURITY / HARDENING et DEVOPS / BUILD / RELEASE ; API/IPC et Database interviennent uniquement sur leur représentation ; QA définit/exécute les preuves.

## 1. Faits établis sur les surfaces Web

Security impose :

```text
WCP-USER session != WCP-ADMIN session
separate origin
separate cookie name
separate server-side session namespace
separate surface claim
separate runtime Unix identity
```

Cookies :

```text
USER  = __Host-wcp_user_session
ADMIN = __Host-wcp_admin_session
Secure
HttpOnly
SameSite=Strict
Path=/
host-only
no Domain attribute
```

Le risque identifié par AUDIT-WCP-0117 est conditionnel : des ports distincts peuvent créer des origines distinctes, mais ne constituent pas à eux seuls une frontière d'envoi des cookies par host. Aucun déploiement fautif n'est affirmé.

## 2. Décision S01 — topologie admissible des surfaces

Trois catégories sont à distinguer :

| Option | Exemple logique | Observation |
|---|---|---|
| W1 — hostnames distincts | USER sur host U, ADMIN sur host A | cohérent avec cookies host-only et séparation d'origine ; **recommandé, PENDING OWNER DECISION** |
| W2 — même hostname, ports distincts | host H:portU / H:portA | ne doit pas être considéré comme isolation cookie suffisante à lui seul |
| W3 — même hostname, autre routage/path | host H avec routes USER/ADMIN | baseline actuelle `Path=/` ne fournit pas une isolation cookie par path ; nécessite une décision/changement explicite si retenu |

La recommandation W1 ne choisit aucun DNS réel.

Si Security/DevOps retiennent W2 ou W3, ils doivent documenter le mécanisme compensatoire qui empêche l'exposition inter-surface et démontrer son comportement dans le navigateur et au reverse proxy. Le serveur doit de toute façon rejeter toute session de mauvaise surface.

## 3. Décision S02 — invariant de cookie par surface

Proposition à ratifier :

1. un endpoint USER n'utilise jamais le cookie ADMIN comme credential de repli ;
2. un endpoint ADMIN n'utilise jamais le cookie USER comme credential de repli ;
3. présence simultanée de cookies USER+ADMIN ne provoque aucune sélection heuristique ;
4. chaque runtime consulte seulement le namespace de session de sa surface ;
5. la surface de session est validée côté serveur, pas déduite du nom de cookie uniquement ;
6. logout USER ne devient pas logout ADMIN par alias ; logout ADMIN révoque en plus les contextes privilégiés exigés par Security ;
7. aucun cookie n'est copié d'une surface vers l'autre au login, MFA, élévation ou impersonation.

Le traitement d'une requête contenant le « mauvais » cookie mais pas le bon doit être défini comme non authentifié/refusé selon l'endpoint, sans fuite permettant de confirmer une session d'autre surface.

## 4. Décision S03 — Origin / CSRF / WebSocket

Pour mutations navigateur :

```text
valid surface session
+ synchronizer CSRF token de la même session/surface
+ exact expected Origin de la surface
```

À ratifier :

- token CSRF USER non accepté sur ADMIN et inversement ;
- Origin de l'autre surface refusé même si les deux appartiennent au même opérateur ;
- les endpoints WSS explicitement autorisés dans CSP conservent surface/session/authz ;
- une reconnexion de canal ne contourne pas rotation/revocation de session ;
- un reverse proxy ne réécrit pas silencieusement l'Origin en une valeur de confiance.

Cette section ne choisit pas les hostnames ou chemins WSS.

## 5. Décision S04 — preuve de non-exposition

QA doit distinguer :

```text
cookie stored by browser
cookie selected by browser for request
cookie observed by ingress/proxy
cookie forwarded to backend
cookie accepted as authentication
```

Un test qui vérifie seulement que le backend ignore le mauvais cookie ne prouve pas qu'il n'a jamais été envoyé à l'infrastructure.

Le critère exact « non-exposition » doit être défini par Security/DevOps selon la topologie retenue.

## 6. Faits établis sur les permissions

Permission Registry :

```text
explicit DENY
> account override
> role grants
> plan / feature envelope
> default policy
```

Règles additionnelles :

- DEMO = restriction dure additionnelle ;
- reseller delegation ne dépasse jamais l'enveloppe effective du reseller ;
- temporary privilege ne modifie pas les rôles persistants ;
- approval ne remplace jamais l'autorisation à l'exécution ;
- UI visibility ne donne aucun droit ;
- context switching ne change pas permission.

Security définit séparément :

```text
AUTHZ =
authenticated_identity
∩ surface
∩ action
∩ permission
∩ role_constraints
∩ entitlement
∩ resource
∩ tenant_boundary
∩ ownership/delegation
∩ policy/context
∩ authentication_strength
∩ resource_state
∩ approval_when_required
```

Ces deux mécanismes ne doivent pas être implémentés comme un unique « premier match ALLOW ».

## 7. Décision S05 — modèle en deux étapes

Proposition à ratifier :

### Étape A — résolution de la permission effective

La priorité du Permission Registry calcule un **état de permission effectif** pour un `permission_id` et un principal/contexte donné.

Sortie logique proposée :

```text
ALLOW / DENY / NOT_GRANTED
+ source/rationale interne sûre
```

Les noms exacts de l'enum ne sont pas imposés.

### Étape B — décision d'autorisation

Un `ALLOW` de l'étape A devient seulement une condition nécessaire.

L'autorisation finale exige ensuite l'intersection Security complète.

Ainsi :

```text
permission_effective = ALLOW
AND entitlement missing
→ DENY final
```

et :

```text
permission_effective = ALLOW
AND tenant/ownership invalid
→ DENY final
```

Aucun `ALLOW` de rôle/override ne court-circuite les gates Security.

## 8. Décision S06 — portée des account overrides

Le registre établit leur priorité mais pas, dans les extraits consommables, leur **plafond sémantique exact**.

Deux axes doivent être décidés séparément :

### O1 — override négatif

Proposition forte :

```text
explicit DENY reste supérieur
override négatif peut réduire les droits
aucun grant de rôle ne réactive la permission
```

### O2 — override positif

Question propriétaire :

Peut-il :

- seulement rétablir/modifier une permission dans une enveloppe déjà autorisée ;
- dépasser un plan/feature envelope ;
- créer un entitlement ;
- seulement modifier un champ commercial sans modifier Security entitlement ?

**Ne pas déduire la réponse du nom `iam.account_overrides.manage`.**

Recommandation de sécurité, PENDING OWNER DECISION : un override positif ne doit jamais contourner les contraintes Security indépendantes (tenant, ownership, auth strength, approval, resource state, DEMO hard restriction, etc.). Le rapport avec plan/feature/entitlement doit être explicitement publié.

## 9. Décision S07 — délégation reseller

Proposition à ratifier :

```text
delegated_permission
=
requested permission
∩ reseller effective permission
∩ delegation permission ceiling
∩ delegated tenant/resource scope
∩ current non-revoked delegation
```

Puis l'intersection Security complète s'applique encore au reseller agissant.

Une délégation ne transfère jamais ownership.

Une modification/revocation après enqueue mais avant effet doit être revalidée lorsque Security l'exige ; le job ne conserve pas un droit périmé par simple snapshot historique.

## 10. Décision S08 — temporary privilege / privileged session / approval

Les concepts restent distincts :

```text
base permission
privileged session / auth strength
approval
break-glass
impersonation
```

Aucune élévation temporaire ne modifie les rôles persistants.

Un approval valide ne crée pas la permission de base et n'annule pas un DENY.

Une privileged session valide ne change pas le tenant ownership/delegation.

Un contexte break-glass suit son contrat propre et ne devient pas un « superuser permanent ».

## 11. Décision S09 — fail closed et absence de droit

Le propriétaire doit fixer la représentation interne de :

- permission inconnue ;
- permission connue non accordée ;
- DENY explicite ;
- feature/plan absent ;
- entitlement absent ;
- scope/délégation hors limite.

Pour l'extérieur, éviter les différences de réponse qui révèlent inutilement l'existence de ressources cross-tenant. Le mapping HTTP relève du delta transport/API.

## 12. Seize scénarios QA proposés

Identifiants locaux `SQ01–SQ16`, aucun TEST-* réservé. **PLANNED, zéro exécuté.**

| Cas | Situation | Résultat attendu à ratifier |
|---|---|---|
| SQ01 | USER et ADMIN sur hostnames distincts | chaque requête de surface n'expose/accepte que le credential attendu selon critère S04 |
| SQ02 | même hostname / ports distincts dans fixture | démontrer que les ports seuls ne sont pas utilisés comme garantie de cookie |
| SQ03 | cookie ADMIN envoyé à endpoint USER sans cookie USER | aucune auth USER dérivée ; aucun détail de session ADMIN divulgué |
| SQ04 | cookie USER envoyé à endpoint ADMIN | aucune auth ADMIN dérivée |
| SQ05 | cookies USER+ADMIN présents | sélection déterministe de la surface ; aucun fallback |
| SQ06 | CSRF USER sur session ADMIN | refus sans mutation |
| SQ07 | Origin USER vers mutation ADMIN | refus sans mutation |
| SQ08 | rôle accorde permission mais entitlement absent | DENY final |
| SQ09 | rôle accorde permission, tenant ownership invalide | DENY final |
| SQ10 | rôle accorde permission + DENY explicite | DENY final |
| SQ11 | override négatif + rôle positif | DENY/NOT_GRANTED selon modèle ratifié, jamais ALLOW par le rôle |
| SQ12 | override positif + contrainte Security absente | DENY final |
| SQ13 | délégation demande permission au-delà du reseller | DENY |
| SQ14 | délégation révoquée entre enqueue et effet | refus à la revalidation ; aucun effet |
| SQ15 | approval valide mais permission absente/DENY | aucun effet |
| SQ16 | changement de contexte sans changement de permission | aucun droit nouveau dérivé du seul context switch |

SQ01–SQ07 nécessitent la topologie de déploiement candidate pour devenir exécutables. SQ08–SQ16 peuvent être définis au niveau moteur une fois l'algorithme ratifié et les structures DB accessibles.

## 13. Handoff propriétaire

### SECURITY / HARDENING

Décider :

```text
S01 admissible surface topology =
S02 cross-surface cookie handling =
S03 CSRF/Origin cross-surface rules =
S04 definition of non-exposure =
S05 permission-resolution vs final-authz composition =
S06 positive/negative account override ceilings =
S07 reseller delegation composition =
S08 temporary privilege / approval composition =
S09 absent/deny representation and safe external behavior =
```

### DEVOPS / BUILD / RELEASE

Pour S01–S04 :

- topology/routing ;
- TLS/cert boundary ;
- reverse proxy behavior ;
- header/cookie forwarding ;
- observability sans logger les cookies.

### API / IPC / CONTRACTS

Aligner les erreurs et revalidation avec les contrats transport/action, sans redéfinir Security.

### DATABASE / DATA MODEL

Après #6 : pointer les représentations existantes des overrides, grants, entitlements, délégations et révisions. Aucun schéma créé ici.

### TESTS / QA

Rattacher SQ01–SQ16 aux familles existantes ; exécuter seulement quand environnement et implémentation existent.

## 14. Critères de sortie

AUDIT-WCP-0117 exige une topologie admise + preuve de cookies/sessions par surface.

AUDIT-WCP-0118 exige une table/algorithme propriétaire montrant que la résolution de permission et l'intersection Security se composent sans bypass.

```text
TARGETED REVIEW = COMPLETE
OWNER DECISIONS = NOT RECEIVED
HOSTNAMES / PORTS CREATED = NONE
AUTHZ ENGINE IMPLEMENTED = NONE
CANONICAL CONTENT CHANGED = NONE
APPLICATION TESTS EXECUTED = 0
SC-M01 = PAUSED — TARGETED EVIDENCE
SC-M02 = NOT AUTHORIZED
CODE-BASELINE = NONE
```
