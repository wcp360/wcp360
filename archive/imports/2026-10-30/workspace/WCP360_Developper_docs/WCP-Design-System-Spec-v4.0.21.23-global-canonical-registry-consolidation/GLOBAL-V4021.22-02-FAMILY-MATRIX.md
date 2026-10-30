# V4.0.21.22 --- Canonical Family Matrix

  ----------------------------------------------------------------------------------------------------------------
  Family                                                           Canonical population    Cross-family status
  ---------------------------------------------------------------- ----------------------- -----------------------
  Identity/Auth/MFA/RBAC/Account/Plan/Isolation                    V4.0.21.16              VALIDATION BASE

  Web/PHP/TLS/DNS                                                  V4.0.21.17              POPULATED

  Database/Cache                                                   V4.0.21.18              POPULATED

  Mail/Security/Deliverability                                     V4.0.21.19              POPULATED

  Files/Access/Git/Deploy/Runtimes/WordPress                       V4.0.21.20              POPULATED

  Backup/DR/Firewall/WAF/Malware/Observability/Updates/Installer   V4.0.21.21              POPULATED
  ----------------------------------------------------------------------------------------------------------------

All six families share the same control path: actor → operation → policy
decision → durable Job when required → closed privileged action →
observed verification → audit.
