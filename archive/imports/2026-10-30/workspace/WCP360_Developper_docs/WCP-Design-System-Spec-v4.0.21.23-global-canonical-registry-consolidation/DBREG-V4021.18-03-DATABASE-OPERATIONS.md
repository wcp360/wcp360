# V4.0.21.18 --- Database Operations Registry

  ----------------------------------------------------------------------------------------------------------------------------
  Operation                     Permission                  Job            Guards                               Closure
  ----------------------------- --------------------------- -------------- ------------------------------------ --------------
  `DATABASE-CREATE`             database.create             REQUIRED       scope/entitlement/quota/capability   READY

  `DATABASE-DELETE`             database.delete             REQUIRED       impact/backup/retention              READY

  `DATABASE-RENAME`             database.update             REQUIRED       backend capability                   READY

  `DATABASE-PRINCIPAL-CREATE`   database.principal.create   REQUIRED       scope/quota                          READY

  `DATABASE-PRINCIPAL-ROTATE`   database.principal.rotate   REQUIRED       step-up                              READY

  `DATABASE-PRINCIPAL-LOCK`     database.principal.lock     REQUIRED       lifecycle                            READY

  `DATABASE-PRINCIPAL-DELETE`   database.principal.delete   REQUIRED       grant impact                         READY

  `DATABASE-GRANT-SET`          database.grant.update       REQUIRED       least privilege                      READY

  `DATABASE-IMPORT`             database.import             REQUIRED       size/format/security                 READY

  `DATABASE-EXPORT`             database.export             REQUIRED       scope/storage                        READY

  `DATABASE-REPAIR`             database.repair             REQUIRED       provider capability                  READY

  `DATABASE-RECONCILE`          database.reconcile          REQUIRED       observed drift                       READY
  ----------------------------------------------------------------------------------------------------------------------------

No API field accepts arbitrary privileged SQL for these lifecycle
operations. Advanced SQL execution belongs to database client tooling
under the DB user's own database privileges, not to the root daemon
contract.
