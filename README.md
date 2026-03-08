# WCP360 — Modern Web Control Panel

> Go · Caddy · FrankenPHP · SQLite

**WCP360** is a next-generation, Linux-native hosting control panel.

## Stack
- **Go 1.22+** — single static binary
- **Caddy 2** — HTTP/3, auto-TLS, Early Hints 103
- **FrankenPHP** — PHP worker mode (no FPM)
- **SQLite** — panel state (no external DB)
- **Redis** — object cache + async jobs (v0.1+)

## Quick Start

```bash
git clone https://github.com/wcp360/wcp360.git
cd wcp360
cp wcp360.yaml wcp360.local.yaml   # edit jwt_secret + admin credentials
go run ./cmd/wcp360
```

Then open: http://localhost:8080/admin/login

## API
```
POST /api/v1/auth/login
GET  /api/v1/auth/me
POST /api/v1/auth/logout
GET  /api/v1/tenants?page=1&per_page=20
POST /api/v1/tenants
GET  /api/v1/tenants/{id}
PATCH  /api/v1/tenants/{id}
DELETE /api/v1/tenants/{id}
GET  /api/v1/audit?limit=50
GET  /healthz
```

## Documentation
```bash
pip install mkdocs mkdocs-material
cd docs && mkdocs serve
```

## License
MIT — © HADJ RAMDANE Yacine
