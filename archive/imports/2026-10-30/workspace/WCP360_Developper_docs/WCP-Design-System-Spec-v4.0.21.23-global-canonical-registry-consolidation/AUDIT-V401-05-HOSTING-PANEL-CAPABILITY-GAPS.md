# V4.0.1 --- Hosting Panel Capability Gap Scan

  Sujet                       Occurrences lexicales Audit
  ------------------------- ----------------------- ---------
  FTP/SFTP management                            77 PRESENT
  SSH keys / shell access                         5 PRESENT
  IPv6                                            8 PRESENT
  Reverse DNS / PTR                               8 PRESENT
  DNSSEC                                         26 PRESENT
  CAA                                             5 PRESENT
  Email queue                                     3 PRESENT
  Mail reputation / RBL                           6 PRESENT
  Greylisting                                     4 PRESENT
  ARC                                             0 MISSING
  MTA-STS / TLS-RPT                              10 PRESENT
  Catch-all mail                                  5 PRESENT
  Mailing lists                                   5 PRESENT
  WebDAV                                          0 MISSING
  WebSocket proxy                                 1 PRESENT
  HTTP/2                                          3 PRESENT
  HTTP/3 / QUIC                                   6 PRESENT
  Brotli                                          0 MISSING
  ModSecurity / WAF                              20 PRESENT
  OWASP CRS                                       0 MISSING
  Disk SMART                                      0 MISSING
  RAID monitoring                                 4 PRESENT
  Inode quota                                     3 PRESENT
  Bandwidth accounting                            9 PRESENT
  Process limits                                  1 PRESENT
  OOM handling                                   42 PRESENT
  NTP/Chrony                                      5 PRESENT
  Swap                                            1 PRESENT
  sysctl                                          0 MISSING
  SELinux                                         6 PRESENT
  AppArmor                                        6 PRESENT
  API tokens                                      4 PRESENT
  IP allowlist                                    0 MISSING
  Rate limiting                                  13 PRESENT
  CSRF                                            8 PRESENT
  CSP                                             0 MISSING
  Password policy                                 0 MISSING
  Account lockout                                15 PRESENT
  Backup encryption                               0 MISSING
  Granular restore                                2 PRESENT
  Offsite backup                                  0 MISSING
  S3                                             15 PRESENT
  Backup immutability                             0 MISSING
  WP staging                                     13 PRESENT
  WP-CLI                                          1 PRESENT
  Vulnerability feed                              0 MISSING
  Composer                                        7 PRESENT
  npm/yarn/pnpm                                   9 PRESENT
  Python venv                                     3 PRESENT
  Passenger                                      10 PRESENT
  Supervisor                                      6 PRESENT
  RabbitMQ                                       11 PRESENT
  Redis                                          21 PRESENT
  Memcached                                      18 PRESENT
  phpMyAdmin                                      9 PRESENT
  pgAdmin                                         6 PRESENT
  Adminer                                         6 PRESENT
  WHMCS                                           9 PRESENT
  Blesta                                          8 PRESENT
  FossBilling                                     7 PRESENT
  HostBill                                        8 PRESENT
  Webhooks                                       45 PRESENT
  Idempotency                                    32 PRESENT
  Maintenance mode                                1 PRESENT
  Feature flags                                   2 PRESENT
  White-label                                     7 PRESENT
  Localization / RTL                             22 PRESENT
  WCAG                                            8 PRESENT
  Global search / Ctrl+K                          5 PRESENT

`MISSING` exige vérification manuelle. `PRESENT` ne signifie pas que le
contrat est assez détaillé.
