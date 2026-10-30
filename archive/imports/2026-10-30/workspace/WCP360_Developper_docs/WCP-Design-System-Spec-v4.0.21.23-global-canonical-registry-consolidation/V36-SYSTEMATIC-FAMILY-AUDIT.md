# V3.6 SYSTEMATIC FAMILY AUDIT

## Résultat systématique par famille

  -------------------------------------------------------------------------
  Famille                      Documents Readiness         Dépendances
                                                           directes
  ---------------- --------------------- ----------------- ----------------
  ACME / TLS                           2 READY             DNS, Secure
  Advanced                                                 Development
                                                           Normative

  AI, Automation &                     6 PARTIAL           API / CLI / SDK,
  Observability UX                                         Telemetry,
                                                           Secure
                                                           Development
                                                           Normative

  API / CLI / SDK                      3 READY             Product
                                                           Architecture &
                                                           Identity, Secure
                                                           Development
                                                           Normative

  App Runtimes /                       8 READY             Web / PHP,
  LiteSpeed                                                System / OS

  Apps /                              19 READY             WCP-USER, Web /
  Multitenancy /                                           PHP, Mail, API /
  API / Mail UX                                            CLI / SDK

  Backup / IPAM /                      7 READY             Backup / Shared
  Metering                                                 Storage, Linux
                                                           Network / DNS
                                                           Advanced

  Backup / Shared                      7 READY             System / OS,
  Storage                                                  Secure
                                                           Development
                                                           Normative

  Billing / File                       3 READY             API / CLI / SDK,
  Manager                                                  WCP-USER

  Cache / Queue                        5 READY             System / OS, WCP
                                                           Control Plane

  Compute / SDN                        4 PARTIAL           Storage /
                                                           Cluster, Linux
                                                           Network / DNS
                                                           Advanced,
                                                           Telemetry

  DNS                                  7 READY             System / OS,
                                                           Linux Network /
                                                           DNS Advanced

  Databases                            6 READY             System / OS,
                                                           Backup / Shared
                                                           Storage, Secure
                                                           Development
                                                           Normative

  Datacenter /                         5 OPTIONAL/FUTURE   Compute / SDN,
  Kubernetes / IaC                                         API / CLI / SDK

  Design                              20 READY             Design System
  Components & UX                                          Foundations
  Patterns                                                 

  Design System                       10 READY             Product
  Foundations                                              Architecture &
                                                           Identity

  Domains /                           10 READY             DNS, Web / PHP,
  Migration                                                Databases, Mail,
                                                           Backup / Shared
                                                           Storage

  Enterprise                           8 READY             Product
  Identity /                                               Architecture &
  Support                                                  Identity, API /
                                                           CLI / SDK

  Governance &                        27 READY             ---
  Master                                                   

  Governance, API                     15 READY             Product
  & Quality                                                Architecture &
                                                           Identity, Secure
                                                           Development
                                                           Normative

  Historical &                         5 HISTORICAL        ---
  Catalog                                                  

  Host Security /                      6 READY             System / OS,
  Storage Health                                           Secure
                                                           Development
                                                           Normative,
                                                           Telemetry

  Licensing                            2 PARTIAL           API / CLI / SDK,
                                                           WCP Control
                                                           Plane

  Linux Network /                     11 READY             System / OS,
  DNS Advanced                                             Secure
                                                           Development
                                                           Normative

  Mail                                 7 READY             System / OS,
                                                           DNS, ACME / TLS
                                                           Advanced, Host
                                                           Security /
                                                           Storage Health

  Mail / DB                            8 READY             Mail, Databases
  Advanced                                                 

  Mail                                 6 READY             Mail, DNS, ACME
  Deliverability                                           / TLS Advanced

  Monitoring / API                     3 READY             Telemetry, API /
  Tokens                                                   CLI / SDK

  Network / SSH /                     10 READY             System / OS,
  FTP                                                      Secure
                                                           Development
                                                           Normative

  Other                               14 PARTIAL           ---

  Plugin Trust /                       4 READY             Plugins /
  Audit SIEM                                               Security,
                                                           Telemetry,
                                                           Secure
                                                           Development
                                                           Normative

  Plugins /                            6 READY             Secure
  Security                                                 Development
                                                           Normative, WCP
                                                           Control Plane

  Product                              9 READY             Secure
  Architecture &                                           Development
  Identity                                                 Normative

  SSH / MFA / SCIM                     7 READY             Network / SSH /
                                                           FTP, Product
                                                           Architecture &
                                                           Identity

  Secure                              20 READY             ---
  Development                                              
  Normative                                                

  Storage /                            5 PARTIAL           System / OS,
  Cluster                                                  Linux Network /
                                                           DNS Advanced,
                                                           Telemetry

  System / OS                         10 READY             Secure
                                                           Development
                                                           Normative, WCP
                                                           Control Plane

  TLS / Backup                         9 READY             System / OS,
                                                           Secure
                                                           Development
                                                           Normative

  Telemetry                            6 READY             WCP Control
                                                           Plane, System /
                                                           OS

  WCP Control                         12 READY             Secure
  Plane                                                    Development
                                                           Normative,
                                                           Product
                                                           Architecture &
                                                           Identity

  WCP-ADMIN Shell                     10 READY             Product
                                                           Architecture &
                                                           Identity, Design
                                                           Components & UX
                                                           Patterns, API /
                                                           CLI / SDK

  WCP-USER                            10 READY             Product
                                                           Architecture &
                                                           Identity, Design
                                                           Components & UX
                                                           Patterns, API /
                                                           CLI / SDK

  Web / PHP                           15 READY             System / OS,
                                                           Secure
                                                           Development
                                                           Normative, TLS /
                                                           Backup
  -------------------------------------------------------------------------

## Règle de progression

Les familles READY passent ensuite à la **spécification
d'implémentation** (resource schemas, endpoints, permissions,
jobs/events, adapters, tests). Les familles PARTIAL ne bloquent pas le
cœur V1 sauf dépendance explicite.
