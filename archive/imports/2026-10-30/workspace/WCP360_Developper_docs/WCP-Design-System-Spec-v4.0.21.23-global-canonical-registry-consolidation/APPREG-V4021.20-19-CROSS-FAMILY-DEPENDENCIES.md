# V4.0.21.20 --- Files / Runtime Cross-Family Dependencies

Dependencies: Account/Plan/Entitlement/Quota/Isolation →
ManagedRoot/Access → Site/Web/PHP/TLS/ DNS → DB/Cache →
Git/Runtime/Secrets → Deploy → Cron/Workers.

WordPress additionally consumes DB, web, TLS, backup and
malware/security. Staging consumes clone/backup, DNS/TLS and mail
suppression. Dependency Jobs consume outbound network policy and
observability.

Account suspension stops/locks governed access and execution according
to policy without deleting desired application data.
