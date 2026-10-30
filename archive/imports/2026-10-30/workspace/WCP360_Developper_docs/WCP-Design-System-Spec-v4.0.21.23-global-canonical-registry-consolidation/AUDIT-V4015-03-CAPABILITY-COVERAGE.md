# V4.0.15 --- Capability Coverage Evidence

## Probes textuels du corpus

Ces comptes ne mesurent pas la qualité ; ils vérifient que les surfaces
majeures sont réellement présentes dans les documents.

  -----------------------------------------------------------------------
  Surface                             Signaux trouvés
  ----------------------------------- -----------------------------------
  Mail anti-spam / quarantine /       `rspamd`=28, `antispam`=74,
  reputation                          `quarantine`=74, `reputation`=36

  Installer / first-run /             `first boot`=3, `setup wizard`=3,
  finalization                        `production readiness`=6,
                                      `preflight`=195

  Repair / rescue / admin recovery    `repair center`=43, `rescue`=7,
                                      `lockout recovery`=2,
                                      `recovery mode`=4

  Transfer from competing panels      `cpanel`=11, `plesk`=13,
                                      `directadmin`=11, `hestiacp`=3

  Cluster / HA / fencing              `fencing`=35, `quorum`=110,
                                      `split-brain`=12, `placement`=151

  Lifecycle / signed updates          `provenance`=72, `signed`=26,
                                      `update channel`=3, `rollback`=244

  Backup / restore / DR               `backup`=586, `restore`=193,
                                      `disaster recovery`=13

  Observability / incidents           `metrics`=67, `logs`=533,
                                      `incident`=167, `alert`=210

  API / webhooks / billing            `webhook`=223, `billing`=316,
                                      `idempotency`=97, `api`=1036

  CMS / WordPress / Git               `wordpress`=51, `staging`=35,
                                      `git`=147, `deployment`=109

  Multi-tenant / reseller /           `reseller`=127, `impersonation`=70,
  impersonation                       `tenant`=475

  DNSSEC / ACME / certificates        `dnssec`=81, `acme`=97,
                                      `certificate`=119

  IPv6 / NAT / IPAM                   `ipv6`=30, `nat`=474, `ipam`=42

  Abuse / rate limiting               `abuse`=42, `rate limit`=36,
                                      `throttle`=7

  Accessibility / dark-light / search `accessibility`=1, `dark`=52,
                                      `light`=248, `search`=29
  -----------------------------------------------------------------------

## Conclusion

Les préoccupations majeures discutées --- notamment antispam, réputation
mail, wizard, finalisation, repair/rescue, migration, HA, updates,
backup/DR, observability, API/billing, CMS/runtimes, reseller et réseau
--- possèdent désormais des ancrages explicites dans le corpus.
