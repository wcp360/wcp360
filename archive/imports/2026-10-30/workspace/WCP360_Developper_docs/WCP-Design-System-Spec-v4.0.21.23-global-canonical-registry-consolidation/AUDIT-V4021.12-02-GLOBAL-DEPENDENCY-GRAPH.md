# V4.0.21.12 --- Global Dependency Graph

## Graphe de référence

Identity/Auth → RBAC/Scope → Tenant/Reseller/Account → Plan/Entitlement
→ Quota/Isolation → System Identity/Filesystem → Service resources.

Service branches: - Domain/Site → Webserver → Runtime/PHP → TLS → DNS. -
Database/Cache. - Mail → DNS/TLS → anti-spam/AV → deliverability →
webmail. - Files/Access → Git/Deploy → App catalog/WordPress → runtimes
→ cron/workers. - Backup/Restore consumes every resource family. -
Monitoring/Logs/Audit observe control plane, jobs and services. -
Firewall/WAF constrain service exposure. - Updates/Doctor/Installer
depend on capability registry and host profile.

## Anti-cycles

Resource definitions must not depend on UI. Entitlements do not depend
on concrete adapters. Adapters do not define authorization. Monitoring
evidence does not become desired-state authority. Backup metadata does
not own live resources.
