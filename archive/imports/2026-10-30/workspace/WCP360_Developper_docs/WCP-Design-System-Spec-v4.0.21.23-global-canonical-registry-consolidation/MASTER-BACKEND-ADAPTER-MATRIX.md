# MASTER BACKEND ADAPTER MATRIX

  ---------------------------------------------------------------------------
  Capability              Contrat WCP                 Adapters actuels/prévus
  ----------------------- --------------------------- -----------------------
  Web server              WebServerAdapter            Apache, Nginx,
                                                      LiteSpeed,
                                                      OpenLiteSpeed

  PHP runtime             RuntimeAdapter              PHP-FPM

  App runtime             RuntimeAdapter              Node.js, Python,
                                                      Ruby/Passenger, Go

  Firewall                FirewallAdapter             nftables, firewalld

  Intrusion prevention    SecurityAdapter             Fail2ban, CrowdSec

  MAC                     MACAdapter                  SELinux, AppArmor

  DNS authoritative       DNSAdapter                  BIND; PowerDNS/Knot/NSD
                                                      futurs

  Anti-spam               MailSecurityAdapter         Rspamd, SpamAssassin

  Malware                 MalwareScannerAdapter       ClamAV, Maldet,
                                                      Imunify360

  Isolation               IsolationAdapter            native/chroot, LVE,
                                                      CageFS

  Backup engine           BackupEngineAdapter         Native, Restic, Borg

  Backup destination      StorageDestinationAdapter   Local, SFTP,
                                                      S3-compatible, NFS

  Metrics                 MetricsAdapter              Native collectors,
                                                      Prometheus

  Telemetry               TelemetryAdapter            OpenTelemetry

  Logs                    LogAdapter                  journal/files, Loki,
                                                      remote syslog

  Identity                IdentityProviderAdapter     Local, OIDC, SAML,
                                                      LDAP/AD

  Billing                 ProvisioningAdapter         WHMCS, Blesta,
                                                      FossBilling, HostBill

  Registrar               RegistrarAdapter            provider plugins

  External DNS            DNSProviderAdapter          provider plugins

  Bare metal              BMCAdapter                  Redfish, IPMI optional

  Orchestration           ContainerPlatformAdapter    Docker/LXC;
                                                      Kubernetes/K3s optional
  ---------------------------------------------------------------------------

## Principe

Un adapter déclare version, health, capabilities, configuration schema,
permissions requises et opérations supportées. Le cœur ne branche jamais
directement son UX sur un binaire fournisseur.
