# MASTER FEATURE MATRIX --- WCP v3.3

Cette matrice est un **contrôle de couverture**, pas une promesse que
chaque module est déjà implémenté. `Specified` signifie documenté dans
le pack.

  -------------------------------------------------------------------------------------------------------------
  Domaine      Fonction                            Surface       API   Audit   Job       Priorité   Statut
  ------------ ----------------------------------- ------------- ----- ------- --------- ---------- -----------
  Core         System/OS/Kernel                    WCP-ADMIN     No    Yes     Yes       P0         Specified

  Web          Apache                              WCP-ADMIN +   Yes   Yes     Yes       P0         Specified
                                                   delegated                                        
                                                   WCP-USER site                                    
                                                   config                                           

  Web          Nginx                               WCP-ADMIN +   Yes   Yes     Yes       P0         Specified
                                                   delegated                                        
                                                   WCP-USER site                                    
                                                   config                                           

  Web          LiteSpeed/OpenLiteSpeed             WCP-ADMIN +   Yes   Yes     Yes       P1         Specified
                                                   WCP-USER                                         
                                                   where                                            
                                                   assigned                                         

  Runtime      PHP/FPM multi-version               Both          Yes   Yes     Yes       P0         Specified

  Runtime      Node/Python/Ruby/Go                 Both          Yes   Yes     Yes       P1         Specified

  Apps         WordPress Toolkit                   Both          Yes   Yes     Yes       P0         Specified

  Apps         Application Auto-installer          Both          Yes   Yes     Yes       P1         Specified

  Files        Advanced File Manager               WCP-USER      Yes   Yes     Yes       P0         Specified

  Database     MySQL/MariaDB/PostgreSQL            Both          Yes   Yes     Yes       P0         Specified

  Database     phpMyAdmin/pgAdmin/Adminer SSO      WCP-USER      Yes   Yes     No        P1         Specified

  Cache        Redis/Memcached                     Both          Yes   Yes     Yes       P1         Specified

  Mail         Postfix/Exim/Dovecot                Both          Yes   Yes     Yes       P0         Specified

  Mail         DKIM/SPF/DMARC/Autoconfig/Sieve     Both          Yes   Yes     Yes       P0         Specified

  DNS          BIND/Zones/DNSSEC/PTR               Both          Yes   Yes     Yes       P0         Specified

  Network      IPAM IPv4/IPv6                      WCP-ADMIN     Yes   Yes     Yes       P0         Specified
                                                                                                    V3.3

  Security     Firewall/Fail2ban/WAF               WCP-ADMIN     Yes   Yes     Yes       P0         Specified

  Security     Antivirus/Anti-malware              Both          Yes   Yes     Yes       P0         Specified

  Security     MFA/2FA/Step-up                     Both          Yes   Yes     No        P0         Specified

  Security     SELinux/AppArmor/CVE/CrowdSec       WCP-ADMIN     Yes   Yes     Yes       P1         Specified
                                                                                                    V3.3

  Hosting      Quotas/Isolation                    Both          Yes   Yes     Yes       P0         Specified

  Hosting      Plans/Packages                      Both          Yes   Yes     Yes       P0         Specified

  Hosting      Root→Reseller→User                  Both          Yes   Yes     No        P0         Specified

  Support      Impersonation                       Both          Yes   Yes     No        P0         Specified

  Automation   Git/CI-CD                           WCP-USER +    Yes   Yes     Yes       P1         Specified
                                                   policy admin                                     

  Automation   Workers/Queues/RabbitMQ             Both          Yes   Yes     Yes       P1         Specified

  Billing      WHMCS/Blesta/FossBilling/HostBill   WCP-ADMIN     Yes   Yes     Yes       P1         Specified

  Billing      Metering/Billing usage export       WCP-ADMIN +   Yes   Yes     Yes       P0         Specified
                                                   reseller                                         V3.3
                                                   visibility                                       

  Backup       Backup/Restore/DR                   Both          Yes   Yes     Yes       P0         Specified

  Backup       Integrity/Restore                   WCP-ADMIN     Yes   Yes     Yes       P0         Specified
               testing/Immutability                                                                 V3.3

  Migration    Migration Center/importers          WCP-ADMIN     Yes   Yes     Yes       P0         Specified
                                                                                                    V3.3

  Identity     Enterprise SSO                      WCP-ADMIN +   Yes   Yes     No        P1         Specified
                                                   users                                            V3.3

  Support      Ticketing/KB/Status                 Both          Yes   Yes     Mixed     P1         Specified
                                                                                                    V3.3

  UX           White-label/Quick Launcher/Themes   Both          Yes   Yes     No        P1         Specified

  Platform     REST API-first                      Both/API      Yes   Yes     Yes       P0         Specified
                                                   clients                                          

  Platform     GraphQL optional                    API clients   Yes   Yes     Depends   P2         Specified

  Platform     Plugin/Extension Manager            WCP-ADMIN     Yes   Yes     Yes       P1         Specified

  Licensing    License Manager                     WCP-ADMIN     Yes   Yes     Yes       P1         Specified
                                                                                                    V3.3

  Infra        Cluster/Storage/SDN/Compute         WCP-ADMIN     Yes   Yes     Yes       P1         Specified

  AI           AI missions/approval/evidence       WCP-ADMIN     Yes   Yes     Yes       P2         Specified
                                                   primarily                                        
  -------------------------------------------------------------------------------------------------------------

## Règles de lecture

-   **Both** : responsabilités séparées entre WCP-ADMIN et WCP-USER.
-   **Job** `Mixed/Depends` : seules les opérations longues ou mutantes
    pertinentes deviennent des jobs.
-   Les priorités P0/P1/P2 sont des recommandations V3.3 à valider par
    roadmap produit.

## V3.4 Systems & Operations coverage

-   Linux network stack + nftables/firewalld: **P0 / Specified**
-   Authoritative DNS primary-secondary/TSIG: **P0 / Specified**
-   Mail Deliverability Center: **P0 / Specified**
-   Telemetry architecture/adapters: **P0 / Specified**
-   Backup engines/destinations: **P0 / Specified**
-   SSH Server/User Access: **P0 / Specified**
-   API Service Accounts: **P0 / Specified**
-   Advanced ACME/TLS: **P0 / Specified**
-   MFA Recovery, SCIM, SIEM, CLI, plugin trust: **P1 / Specified**
-   Bare-metal/Kubernetes/IaC providers: **P2 / Extension point
    specified**
