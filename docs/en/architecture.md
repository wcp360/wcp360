# Architecture

<<<<<<< HEAD
## Overview

WCP360 is a single-binary Linux hosting control panel.

```
Browser / API Client
        │
        ▼
  Caddy (HTTP/3, TLS auto, FrankenPHP)
        │
        ▼
  WCP360 Binary (Go)
  ┌─────────────────────────────────────────┐
  │  cmd/wcp360/main.go  (entry point)      │
  │                                          │
  │  internal/api/                           │
  │    server.go   — http.Server + lifecycle │
  │    routes.go   — all route registrations │
  │    handlers/   — HTTP handlers           │
  │    middleware/ — auth, logging, rate-limit│
  │                                          │
  │  internal/services/                      │
  │    auth_service.go  — login/session      │
  │    provisioner.go   — filesystem layout  │
  │    email.go         — Mailer interface   │
  │                                          │
  │  internal/database/                      │
  │    db.go, migrate.go, seeder.go          │
  │    queries/  — typed SQL functions       │
  │                                          │
  │  internal/cache/                         │
  │    redis.go — optional stdlib RESP client│
  │                                          │
  │  internal/web/                           │
  │    renderer.go — prewarm template cache  │
  │    templates/  — Go HTML templates       │
  └─────────────────────────────────────────┘
        │                    │
        ▼                    ▼
   SQLite (WAL)        Redis (optional)
   /var/lib/wcp360/    graceful degradation
   state.db
```

## Request Lifecycle

```
Request → Caddy → Go HTTP/2
  → middleware.Logging
  → middleware.RateLimiter (login/API only)
  → middleware.RequireAuth / RequireWebAuth
  → Handler
    → queries.*  (parameterised SQL)
    → services.* (business logic)
  → JSON response / HTML template
```

## Security Invariants

| ID | Invariant |
|----|-----------|
| INV-1 | JWT HS256, 24h TTL, JTI blocklist in sessions table |
| INV-2 | bcrypt cost=12, timing-safe comparison |
| INV-3 | Secrets never appear in logs or responses |
| INV-4 | Every admin endpoint requires auth middleware |
| INV-5 | Cookie: HttpOnly + SameSite=Strict + Secure (prod) |
| INV-6 | All SQL uses parameterised queries — no string concat |
| INV-7 | FHS layout: /opt/wcp360 /etc/wcp360 /var/lib/wcp360 /srv/www |
| INV-8 | audit_log is append-only — no DELETE or UPDATE ever |

## Directory Layout

```
/opt/wcp360/          binary + static assets
/etc/wcp360/          wcp360.yaml config
/var/lib/wcp360/      state.db (SQLite)
/srv/www/<username>/  tenant home directories
  public_html/        web root (755)
  logs/               access + error logs (750)
  tmp/                PHP temp files (700)
  .keep               sentinel file
/var/log/wcp360/      application + Caddy logs
```

## Key Design Decisions

- **Single binary**: Go embeds HTML templates via `//go:embed`. No runtime assets needed.
- **SQLite WAL**: Sufficient for a control panel workload. WAL allows concurrent reads.
- **Redis optional**: If Redis is unreachable or unconfigured, all cache operations degrade to no-ops silently.
- **HTMX**: Used for the status toggle fragment swap. Full page fallback always works without JS.
- **FrankenPHP**: PHP worker mode keeps processes resident — TTFB < 50ms for WordPress.
- **No ORMs**: All database access uses typed query functions with `database/sql`.
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
