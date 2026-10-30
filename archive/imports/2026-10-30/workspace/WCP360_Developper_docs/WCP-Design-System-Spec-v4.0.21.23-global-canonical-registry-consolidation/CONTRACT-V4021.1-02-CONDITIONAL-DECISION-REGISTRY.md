# V4.0.21.1 --- Conditional Decision Registry

## Statut

**NORMATIVE CORRECTION**

Les conditions ne sont plus du texte libre : elles doivent pointer vers
un Decision ID.

  ---------------------------------------------------------------------------------------------------------------------------------------------------------
  Decision ID             Condition source                TRUE branch                                                               FALSE branch
  ----------------------- ------------------------------- ------------------------------------------------------------------------- -----------------------
  `DEC-WEB-RELOAD-001`    rendered Nginx generation       `ManagedGenerationActivate → ServiceReload(nginx)`                        no wcpd call; verify
                          fingerprint differs from active                                                                           current generation
                          generation                                                                                                

  `DEC-PHP-RELOAD-001`    rendered PHP-FPM pool           `ManagedGenerationActivate → ServiceReload(php-fpm)`                      no wcpd call; verify
                          generation fingerprint differs                                                                            current pool

  `DEC-MAIL-RELOAD-001`   generated Postfix/Dovecot       `ManagedGenerationActivate → ServiceReload(postfix/dovecot as changed)`   no reload; verify
                          map/config fingerprint differs                                                                            active map

  `DEC-DNS-MODE-001`      `dns_zone.mode=MANAGED_LOCAL`   PowerDNS adapter owns apply/verify                                        no PowerDNS mutation;
                                                                                                                                    external DNS state is
                                                                                                                                    advisory/verified only

  `DEC-TLS-POLICY-001`    domain TLS policy is            ACME issue/renew workflow                                                 no ACME mutation
                          `MANAGED_ACME`                                                                                            

  `DEC-SECRET-DUAL-001`   provider declares dual-validity deploy new → verify → switch → revoke old                                 bounded maintenance
                          capability                                                                                                cutover contract
                                                                                                                                    required

  `DEC-DELETE-DATA-001`   delete plan dataset             typed purge adapter + absence verify                                      disposition=`RETAIN`:
                          disposition=`PURGE`                                                                                       detach and record
                                                                                                                                    retention object

  `DEC-STEPUP-001`        action matrix says REQUIRED     valid fresh step-up proof required before transaction                     no step-up challenge

  `DEC-REPAIR-001`        finding `remediation_type` maps execute that named remediation contract                                   reject as
                          to allowlisted remediation                                                                                non-remediable
                          contract                                                                                                  
  ---------------------------------------------------------------------------------------------------------------------------------------------------------

## Règle

Toute future condition d'exécution privilégiée doit être représentée par
:
`Decision ID + input field/evidence + true branch + false branch + verification`.
