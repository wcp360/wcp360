# V4.0.20 --- P0 Repository Blockers

## Statut

**INDEPENDENT AUDIT --- V4.0.20 --- NO CODE**

## Mandat

Cette passe n'ajoute aucune capability produit. Elle tente de réfuter
les `PASS` de V4.0.19 et vérifie si les contrats sont réellement assez
déterministes pour autoriser un repository skeleton sans laisser de
décisions P0 implicites aux développeurs.

## Règle d'audit

Un `PASS` n'est accepté que si le contrat permet de déterminer sans
invention locale : - la ressource et ses invariants ; - l'action exacte
; - l'autorisation et le scope exacts ; - la transaction ou le Job exact
; - le RPC/adaptateur exact ou un `N/A` non ambigu ; - la vérification
observée ; - l'audit/event ; - l'oracle d'acceptation.

Une famille générique, un wildcard, `policy based`, `when needed`,
`according to adapter`, `*`, `CRUD`, `+ actions` ou `N/A` ambigu ne
ferme pas à lui seul une décision P0.

  ---------------------------------------------------------------------------------------------
  ID                      Finding                 Closure required
  ----------------------- ----------------------- ---------------------------------------------
  **P0-001**              Logical resource        Définir par ressource les champs, types
                          schemas are generic     logiques, required/nullable, relations,
                                                  cardinalités, uniqueness, versioning et
                                                  invariants cross-field. Aucun SQL exécutable.

  **P0-002**              API actions are         Décomposer chaque mutation en méthode +
                          grouped/wildcarded      chemin + request intent + sync/async +
                                                  response/status + precondition/idempotency +
                                                  error classes.

  **P0-003**              Job catalog contains    Remplacer `certificate.*`, `database.*`,
                          wildcard families       `mailbox.*`, etc. par un Job ID canonique par
                                                  opération et vérifier que tout Job référencé
                                                  existe.

  **P0-004**              RPC/adapter mapping is  Pour chaque Job, lister RPC exacts ordonnés,
                          not action-exact        adapter exact, target class et evidence ;
                                                  `when root needed` est interdit dans la
                                                  matrice finale.

  **P0-005**              Secret rotation remains Séparer secret metadata, secret material
                          ambiguous               generation, storage, owning-resource rollout,
                                                  verification, rollback et invalidation de
                                                  l'ancienne version.

  **P0-006**              RBAC scope semantics    Remplacer
                          remain generic          `tenant/tree/admin according actor binding`
                                                  et `policy/risk based` par règles de scope,
                                                  delegation, step-up et impersonation
                                                  action-par-action.

  **P0-007**              Audit/event contracts   Définir audit action ID, event type,
                          lack payload schemas    mandatory safe fields, actor/effective actor,
                                                  outcome, resource version, correlation/job ID
                                                  et redaction class.

  **P0-008**              Acceptance IDs are      Pour chaque SEC/E2E ID : preconditions,
                          labels, not complete    actor, exact actions, fixtures, expected
                          oracles                 state/evidence, negative assertions, audit
                                                  evidence, cleanup/recovery.

  **P0-009**              Delete/retention policy Définir delete semantics par ressource :
                          is over-generalized     soft-delete, revoke, retention, purge
                                                  eligibility, dependency handling, orphan
                                                  policy et observed absence.

  **P0-010**              Cross-resource          Fermer les graphes domain↔site↔DNS↔TLS,
                          orchestration is        plan↔quota, mailbox↔mail-domain,
                          under-specified         restore↔services, account delete↔owned
                                                  resources et leurs partial-failure semantics.

  **P0-011**              Canonical               Fermer UID/GID allocation, account system
                          naming/allocation       name, paths, DB/user names, mailbox/domain
                          constraints are         normalization, collision rules et reserved
                          incomplete              identifiers.

  **P0-012**              Version/migration       Définir schema/spec/template/RPC
                          contracts lack concrete compatibility state machine, migration
                          state transitions       ordering, expand/migrate/contract gates,
                                                  failed migration recovery et downgrade
                                                  blockers.
  ---------------------------------------------------------------------------------------------

## Gate

Les 12 findings doivent être `PASS` ou `N/A` réellement justifié avant
un nouvel audit Repository Entry. Aucun ne nécessite une nouvelle
capability produit.
