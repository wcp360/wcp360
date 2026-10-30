# MASTER FAILURE & RECOVERY MATRIX

  -----------------------------------------------------------------------
  Défaillance             Comportement requis     Recovery attendu
  ----------------------- ----------------------- -----------------------
  API instance            pas de perte d'état     autre instance/restart
                          métier                  selon édition

  DB/state unavailable    mutations bloquées      restore/failover selon
                          proprement              architecture

  Queue/worker            jobs non perdus         retry/recovery sans
  unavailable                                     double mutation

  Agent offline           observed state stale,   reconnect + reconcile
                          mutation gouvernée      

  Secret store            pas de fallback         restore/failover
  unavailable             insecure                

  Adapter command fails   résultat explicite +    rollback/retry si sûr
                          evidence                

  Network/firewall/SSH    anti-lockout path       timed rollback/recovery
  change breaks access                            

  Upgrade fails           stop/health failure     rollback compatible ou
                                                  restore

  Backup destination      backup failed/degraded  alternate/retry selon
  fails                                           policy

  Telemetry unavailable   `unknown/stale`, jamais reconnect/reingest if
                          healthy                 supported
  -----------------------------------------------------------------------
