# V4.0.21.19 --- Mail Cross-Family Dependency Closure

Mail depends on Account/Plan/Quota/Isolation, DNS, TLS, IPAM/rDNS,
filesystem, backup, monitoring/audit and optional malware/reputation
providers.

Domain deletion checks mail resources. IP migration recalculates
PTR/SPF/HELO/ outbound binding impact. DNS changes cannot silently
remove active mail auth records. Backup/restore validates destination
mail stack capability and ownership.

Mail readiness aggregates SMTP, IMAP/storage, TLS, DNS/auth, spam/AV and
queue health without conflating optional provider failures with core
protocol health.
