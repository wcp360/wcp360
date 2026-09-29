# Privileged Service Registry

## Objective

Prevent arbitrary service/unit control.

The daemon accepts a stable `service_id`, never a raw systemd unit string from UI/API.

## Registry examples

| service_id | Typical implementation | Allowed actions |
|---|---|---|
| `web.nginx` | nginx.service | status, reload, restart |
| `runtime.php84` | php8.4-fpm.service | status, reload, restart |
| `cache.redis` | redis-server.service | status, restart |
| `db.mariadb` | mariadb.service | status, restart |
| `db.postgresql` | postgresql service/cluster adapter | status, reload, restart |
| `mail.mta` | postfix/exim adapter | status, reload, restart |
| `mail.imap` | dovecot adapter | status, reload, restart |
| `security.fail2ban` | fail2ban.service | status, restart |
| `wcp.daemon` | WCP daemon unit | status; restart only through protected recovery path |

## Registry metadata

- supported OS families;
- discovered unit name;
- reload support;
- restart impact class;
- dependencies;
- health check;
- config schema;
- log source;
- privilege class.

## Rules

1. Unsupported service IDs return `UNSUPPORTED`.
2. A service with no safe reload must not expose reload.
3. Restart UI must show impacted dependent resources.
4. Restarting the WCP daemon itself requires a special recovery design and must not be casually exposed.