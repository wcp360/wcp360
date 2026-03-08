# WCP360 — Modern Web Control Panel

WCP360 is a next-generation, Linux-native hosting control panel.

## Stack
- **Go 1.22+** — single static binary
- **Caddy 2** — HTTP/3, auto-TLS, Early Hints 103
- **FrankenPHP** — PHP worker mode (no FPM)
- **SQLite** (ncruces/go-sqlite3) — no CGO, no external DB

## Quick Start

```bash
cp wcp360.yaml wcp360.local.yaml  # edit jwt_secret + admin credentials
go run ./cmd/wcp360
# → http://localhost:8080/admin/login
```

## API Surface (v0.0.5)

| Method | Path | Auth |
|--------|------|------|
| GET | /healthz | none |
| POST | /api/v1/auth/login | none |
| POST | /api/v1/auth/logout | Bearer |
| GET | /api/v1/auth/me | Bearer |
| GET | /api/v1/tenants?page=1&per_page=20 | RoleRoot |
| POST | /api/v1/tenants | RoleRoot |
| GET | /api/v1/tenants/{id} | RoleRoot |
| PATCH | /api/v1/tenants/{id} | RoleRoot |
| DELETE | /api/v1/tenants/{id} | RoleRoot |
| GET | /api/v1/audit?limit=50 | RoleRoot |
