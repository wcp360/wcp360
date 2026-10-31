# V4.0.21.17 --- Domain / Site Operations

  -------------------------------------------------------------------------------------------------------
  Operation             Resource        Permission         Job             Key guards         Closure
  --------------------- --------------- ------------------ --------------- ------------------ -----------
  `DOMAIN-CREATE`       domain          domain.create      REQUIRED        scope,             READY
                                                                           entitlement, quota 

  `DOMAIN-UPDATE`       domain          domain.update      REQUIRED when   ownership          READY
                                                           system-backed                      

  `DOMAIN-DELETE`       domain          domain.delete      REQUIRED        child impact       READY

  `SITE-CREATE`         site            site.create        REQUIRED        account active,    READY
                                                                           capability         

  `SITE-SUSPEND`        site            site.suspend       REQUIRED        lifecycle          READY

  `SITE-UNSUSPEND`      site            site.unsuspend     REQUIRED        lifecycle          READY

  `SITE-DELETE`         site            site.delete        REQUIRED        retention/backup   READY
                                                                           policy             

  `DOCUMENT-ROOT-SET`   document_root   site.root.update   REQUIRED        managed-root       READY
                                                                           containment        
  -------------------------------------------------------------------------------------------------------

Domain/site delete computes DNS/TLS/web/runtime dependency impact before
mutation.
