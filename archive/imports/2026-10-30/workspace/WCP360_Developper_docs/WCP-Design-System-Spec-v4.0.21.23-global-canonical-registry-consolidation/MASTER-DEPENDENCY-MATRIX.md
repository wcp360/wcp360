# MASTER DEPENDENCY MATRIX

## But

Rendre explicites prérequis, conflits et impacts avant
installation/activation.

  ---------------------------------------------------------------------------
  Module                  Dépendances principales     Conflits/risques à
                                                      contrôler
  ----------------------- --------------------------- -----------------------
  Web server              ports/IP/TLS/runtime        collision 80/443,
                                                      ownership config

  Mail stack              hostname, DNS, TLS, ports   ports SMTP/IMAP,
                                                      réputation

  DNS authoritative       IP, port 53, zones          resolver local/port 53

  Firewall                network backend, management lockout
                          access                      

  PHP/runtime             packages, web adapter       version/extensions
                                                      incompatibles

  DB                      storage, memory,            port collision,
                          ports/socket                destructive upgrade

  Backup                  engine + destination +      capacity, retention,
                          credentials                 egress

  Migration               source adapter +            overwrite/collision
                          destination capacity        

  Redis/Memcached         memory, socket/network      exposure, quota

  Container/VM            CPU/RAM/storage/network     overcommit, bridge/IP
                                                      conflicts

  Enterprise SSO          identity adapter + fallback admin lockout
                          policy                      

  TLS/ACME                DNS/HTTP challenge + CA     failed renewal, rate
                                                      limits

  Monitoring              collectors/storage/export   cardinality/storage
                                                      growth

  Plugin                  compatibility + requested   privilege expansion
                          permissions                 
  ---------------------------------------------------------------------------

## Installation transaction

`Detect → Resolve dependencies → Show plan/diff → Validate ports/resources → Apply → Health check → Commit/Rollback`.

## Ports/services

Les ports ne sont pas codés comme vérité universelle dans ce document :
chaque adapter fournit ses listeners réels et WCP détecte les
collisions.
