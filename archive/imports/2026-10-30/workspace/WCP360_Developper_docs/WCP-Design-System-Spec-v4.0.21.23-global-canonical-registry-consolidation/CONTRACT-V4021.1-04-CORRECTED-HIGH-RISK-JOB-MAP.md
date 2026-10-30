# V4.0.21.1 --- Corrected High-risk Job Mapping

## Statut

**NORMATIVE CORRECTION**

  -----------------------------------------------------------------------------------------------------------------
  Job                    Decision IDs             wcpd maximum              Adapter IDs       Verification
  ---------------------- ------------------------ ------------------------- ----------------- ---------------------
  `account.suspend`      `DEC-WEB-RELOAD-001`,    `UserLock`, plus          `ADP-FS`,         login locked;
                         `DEC-PHP-RELOAD-001`,    activation/reload only    `ADP-NGINX`,      public/app/mail
                         `DEC-MAIL-RELOAD-001`    through the referenced    `ADP-PHPFPM`,     execution disabled
                                                  decisions                 `ADP-POSTFIX`,    according owned
                                                                            `ADP-DOVECOT`     resources

  `account.resume`       same three Decision IDs  `UserUnlock`, then        same explicit     desired workloads
                         by explicit reference    decision-governed         adapter set       observed
                                                  activation/reload                           

  `account.delete`       `DEC-DELETE-DATA-001`    `UserLock`, typed         `ADP-FS`,         dependency plan fully
                                                  managed-file/permission   `ADP-NGINX`,      satisfied; UID
                                                  operations, `UserDelete`  `ADP-PHPFPM`,     absent; retained
                                                  last                      `ADP-POWERDNS`,   datasets catalogued
                                                                            `ADP-MARIADB`,    
                                                                            `ADP-POSTFIX`,    
                                                                            `ADP-DOVECOT`,    
                                                                            `ADP-BACKUP`      

  `domain.provision`     `DEC-DNS-MODE-001`,      only RPCs selected by     `ADP-NGINX`,      branch-specific
                         `DEC-TLS-POLICY-001`,    `DEC-WEB-RELOAD-001`      `ADP-POWERDNS`,   evidence for
                         `DEC-WEB-RELOAD-001`                               `ADP-ACME`        binding/DNS/TLS

  `domain.delete`        `DEC-DNS-MODE-001`,      decision-governed         `ADP-NGINX`,      no active managed
                         `DEC-WEB-RELOAD-001`     generation                `ADP-POWERDNS`,   binding/routing
                                                  activation/reload         `ADP-POSTFIX`     

  `mailbox.create`       `DEC-MAIL-RELOAD-001`    `DirectoryEnsure`,        `ADP-FS`,         auth/delivery/quota
                                                  `PermissionReconcile`,    `ADP-POSTFIX`,    observed
                                                  then decision-governed    `ADP-DOVECOT`     
                                                  activation/reload                           

  `mailbox.suspend`      `DEC-MAIL-RELOAD-001`    decision-governed         `ADP-POSTFIX`,    auth/submission
                                                  activation/reload         `ADP-DOVECOT`     denied, retained data
                                                                                              untouched

  `mailbox.resume`       `DEC-MAIL-RELOAD-001`    decision-governed         `ADP-POSTFIX`,    auth/delivery
                                                  activation/reload         `ADP-DOVECOT`     restored

  `mailbox.delete`       `DEC-MAIL-RELOAD-001`,   decision-governed         `ADP-FS`,         auth/routing absent;
                         `DEC-DELETE-DATA-001`    activation/reload + typed `ADP-POSTFIX`,    disposition verified
                                                  filesystem disposition    `ADP-DOVECOT`     

  `backup.run`           none                     no generic root RPC;      `ADP-BACKUP`,     complete manifest +
                                                  dataset readers are       `ADP-OBJECT`      checksum + object
                                                  adapter-specific and                        HEAD
                                                  predeclared by dataset                      
                                                  contract                                    

  `restore.run`          `DEC-DELETE-DATA-001`    only dataset-specific     `ADP-BACKUP`,     per-dataset verify
                         not used; restore        named RPC contracts       `ADP-OBJECT`,     then readiness
                         conflict plan controls   referenced by restore     `ADP-FS`,         
                         writes                   manifest type; unknown    `ADP-MARIADB`,    
                                                  dataset type rejects      `ADP-POSTFIX`,    
                                                                            `ADP-DOVECOT`,    
                                                                            `ADP-NGINX`,      
                                                                            `ADP-PHPFPM`      

  `repair.apply`         `DEC-REPAIR-001`         exact remediation         adapter ID stored finding-specific
                                                  contract selected by      by remediation    postcondition
                                                  finding; no generic RPC   contract          

  `readiness.evaluate`   none                     `ServiceStatus`,          `ADP-SYSTEMD`,    readiness evidence
                                                  `ListenerInspect` only    plus read-only    set complete
                                                                            observers of      
                                                                            installed         
                                                                            adapters          

  `service.reconcile`    none                     RPC list must come from a adapter ID bound  equality or explicit
                                                  named drift-remediation   to drift class    conflict
                                                  contract; unknown drift                     
                                                  class =\> `CONFLICTING`,                    
                                                  no mutation                                 
  -----------------------------------------------------------------------------------------------------------------

`same` in this table is not used as a normative shortcut; where prose
says "same three Decision IDs", the canonical values are
`DEC-WEB-RELOAD-001`, `DEC-PHP-RELOAD-001`, `DEC-MAIL-RELOAD-001`.
