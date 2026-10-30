# V4.0.21.1 --- Canonical Adapter Registry

## Statut

**NORMATIVE CORRECTION**

  ----------------------------------------------------------------------------------------------------------------
  Adapter ID        Boundary                Credential/privilege model     Allowed operation families
  ----------------- ----------------------- ------------------------------ ---------------------------------------
  `ADP-FS`          managed filesystem      wcpd typed filesystem RPCs     ensure dir, ownership, permissions,
                    roots                                                  managed install, retention/purge

  `ADP-NGINX`       Nginx managed           wcpd activation/reload +       site/domain/TLS activation and
                    generations             unprivileged render/validate   verification

  `ADP-PHPFPM`      PHP-FPM managed pools   wcpd activation/reload +       runtime pool lifecycle
                                            unprivileged render/validate   

  `ADP-POWERDNS`    PowerDNS API/DB         scoped service credential      zone/RRset apply/observe
                    boundary                                               

  `ADP-ACME`        ACME provider           account key via secret         order/challenge/finalize/revoke
                                            reference                      

  `ADP-MARIADB`     MariaDB admin boundary  scoped DB service credential   schema/principal/grant/backup/restore

  `ADP-POSTFIX`     Postfix managed         wcpd generation                mail routing
                    maps/config             activation/reload              

  `ADP-DOVECOT`     Dovecot auth/mailbox    wcpd generation                mailbox auth/quota verification
                    config                  activation/reload              

  `ADP-RSPAMD`      Rspamd policy           scoped local service/config    spam policy/scan evidence
                                            boundary                       

  `ADP-BACKUP`      backup engine           scoped local dataset readers + manifest/chunk/restore orchestration
                                            secret refs                    

  `ADP-OBJECT`      S3-compatible storage   scoped provider credential     put/head/get/delete retained backup
                                                                           objects

  `ADP-SYSTEMD`     allowlisted services    wcpd                           service observation/control
                                            ServiceStatus/Reload/Restart   

  `ADP-SECURITY`    ClamAV/Maldet/Imunify   read/scan-specific service     scan/findings only
                    provider where          boundary                       
                    installed                                              

  `ADP-NOTIFY`      notification provider   route-scoped secret            test/deliver
  ----------------------------------------------------------------------------------------------------------------

Les termes `target adapter`, `provider adapter`,
`managed service adapters` et `all owned adapters` sont interdits comme
valeurs finales. Un Job doit référencer un ou plusieurs `ADP-*`.
