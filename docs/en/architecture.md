# Architecture

## Security Invariants

| ID | Description |
|----|-------------|
| INV-1 | JWT HS256, 24h TTL, JTI blocklist |
| INV-2 | bcrypt cost=12, timing-safe auth |
| INV-3 | Secrets never in logs |
| INV-4 | No admin endpoint without Bearer or cookie auth |
| INV-5 | Cookie: HttpOnly + SameSite=Strict |
| INV-6 | SQL: parameterised queries only |
| INV-7 | FHS: /opt /etc /var/lib /srv/www |
| INV-8 | audit_log: append-only, no DELETE/UPDATE |

## FHS Layout

```
/opt/wcp360/bin/wcp360    ← compiled binary
/etc/wcp360/wcp360.yaml   ← config (chmod 600)
/var/lib/wcp360/state.db  ← SQLite database
/var/log/wcp360/          ← logs
/srv/www/<username>/      ← tenant home dirs
```
