# MASTER V4.0.5 --- Hosting Core Deep Specification --- Tranche 2

## Portée

DNS + TLS/ACME + Databases + Caches + DB web tools/SSO.

## Architecture

Domain ├── DNSZone → Provider/Authoritative Engine → RRsets →
DNSSEC/Delegation ├── Certificate → ACME Order → Challenge → Binding └──
Website/Mail consumers

HostingAccount ├── Database → DatabaseUser → Grants → Backup/Restore ├──
RedisInstance └── MemcachedInstance

## Principes structurants

DNS configuration et registrar delegation sont séparés. DNSSEC est un
lifecycle cryptographique. Un certificat émis et un certificat
effectivement servi sont deux états différents. Les bases et principals
sont scopés au tenant mais administrés via adapters privilégiés.
Redis/Memcached ne sont jamais exposés publiquement par défaut. Les
outils phpMyAdmin/pgAdmin/Adminer restent derrière une frontière
d'authentification et ne reçoivent pas de credentials via URL.

## Prochaine étape

**V4.0.6 --- Mail & Deliverability Deep Specification** : Postfix/MTA,
Dovecot IMAP/POP3, mailbox/alias/forwarder, quotas, Sieve/vacation,
Roundcube/SnappyMail, SMTP auth/submission, DKIM/SPF/DMARC,
ARC/MTA-STS/TLS-RPT, autodiscover/autoconfig, anti-spam/Rspamd,
antivirus/ClamAV, queues, reputation/blacklists, abuse/rate limits,
backup/restore et migration mail.
