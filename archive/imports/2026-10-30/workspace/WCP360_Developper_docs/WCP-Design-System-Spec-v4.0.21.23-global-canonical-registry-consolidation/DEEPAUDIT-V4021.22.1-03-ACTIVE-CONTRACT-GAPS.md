# V4.0.21.22.1 --- Gaps contractuels encore actifs

  ----------------------------------------------------------------------------------------------------------
  Gap                     Exigence de fermeture                                      Bloque code
  ----------------------- ---------------------------------------------------------- -----------------------
  API exact               méthode/path/request/capability/sync-async/status/errors   oui
                          par opération                                              

  Job/RPC/Adapter         locks/retry/timeout/cancel/RPC/adapter/evidence/recovery   oui

  RBAC                    capability/permission stable + scopes + bundles + step-up  oui

  Logical data model      clé/scope/cardinalité/unique/delete/version/sensitivity    oui

  Lifecycle               state machine + transitions + guards par resource/action   oui

  Audit/Event             action/event IDs + safe diff + severity + visibility +     oui
                          retention                                                  

  Security tests          IDs stables + expected result + affected                   oui
                          resources/permissions                                      

  E2E                     preconditions/actions/states/jobs/audit/outcome/recovery   oui

  Privileged RPC          authorization + targets/path roots + constraints +         oui
                          idempotency                                                

  Failure/recovery        retry/recovery assignés à chaque Job                       oui

  UI/API states           états obligatoires par resource/action                     oui

  Versioning              release/schema/resource/RPC/backup read/write              oui
                          compatibility                                              

  Error registry          propriétaire/sémantique/HTTP/CLI/UI                        oui

  P0 ledger               P0 → operation/policy/error/audit/evidence                 oui
  ----------------------------------------------------------------------------------------------------------

Les registres V4.0.21.16--21 améliorent fortement la couverture par
famille, mais ils ne remplacent pas encore ces tables globales
exhaustives.
