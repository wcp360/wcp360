# V4.0.21.21 --- Operations Resource Registry

  ---------------------------------------------------------------------------------------------------------------------------------------------------
  Resource               Scope                                Lifecycle                                                 Criticality    Closure
  ---------------------- ------------------------------------ --------------------------------------------------------- -------------- --------------
  `backup_policy`        platform/reseller/account/resource   ACTIVE/PAUSED                                             P0             READY

  `backup_run`           policy/resource                      QUEUED/RUNNING/VERIFYING/VERIFIED/FAILED/EXPIRED          P0             READY

  `restore_run`          backup/target                        PLANNED/RUNNING/VERIFYING/SUCCEEDED/FAILED/ROLLING_BACK   P0             READY

  `backup_target`        platform/account                     ACTIVE/DEGRADED/UNAVAILABLE                               P0             READY

  `dr_plan`              platform                             ACTIVE/TESTING/DEGRADED                                   P0             READY

  `firewall_policy`      host/service                         ACTIVE/ERROR                                              P0             READY

  `waf_policy`           platform/site                        ACTIVE/MONITOR/BYPASSED_BY_POLICY/ERROR                   P0             READY

  `malware_finding`      account/file                         OPEN/QUARANTINED/REMEDIATED/IGNORED                       P0             READY

  `health_check`         resource/service                     HEALTHY/DEGRADED/UNHEALTHY/UNKNOWN                        P0             READY

  `metric_series_ref`    scoped resource                      ACTIVE/EXPIRED                                            P1             READY

  `alert_rule`           platform/reseller/account            ACTIVE/PAUSED                                             P0/P1          READY

  `incident`             platform/tenant                      OPEN/ACKNOWLEDGED/MITIGATING/RESOLVED                     P0/P1          READY

  `update_plan`          host/WCP/provider                    PLANNED/BLOCKED/READY/APPLIED/FAILED/ROLLED_BACK          P0             READY

  `maintenance_window`   host/services                        SCHEDULED/ACTIVE/COMPLETE/CANCELLED                       P1             READY

  `readiness_report`     host                                 BLOCKED/DEGRADED/READY                                    P0             READY
  ---------------------------------------------------------------------------------------------------------------------------------------------------
