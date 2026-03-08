# WCP360 — Modern Web Control Panel

<<<<<<< HEAD
**Version:** v0.1.0 | **License:** MIT | **Language:** Go 1.22

WCP360 is a next-generation Linux-native hosting control panel — a modern alternative to cPanel, WHM, and Plesk.

## Stack

| Component | Technology |
|-----------|-----------|
| Backend | Go 1.22 — single static binary |
| Web server | Caddy v2 with HTTP/3 + TLS auto |
| PHP runtime | FrankenPHP worker mode |
| Database | SQLite (WAL mode, no CGO) |
| Cache | Redis (optional, graceful degradation) |
| Observability | log/slog JSON structured logging |

## Quick Start (Development)

```bash
# 1. Clone
git clone https://github.com/wcp360/wcp360.git
cd wcp360

# 2. Install dependencies (generates go.sum)
make tidy

# 3. Edit config
cp wcp360.yaml wcp360.local.yaml
# Set env: development, jwt_secret, admin credentials

# 4. Run
make run

# 5. Open browser
open http://localhost:8080/admin/login
# Default credentials: admin / admin123
```

## Quick Start (VPS)

```bash
# Ubuntu 22.04 / 24.04
sudo bash scripts/install.sh

# Or with Docker
docker run -d \
  -p 8080:8080 \
  -v /etc/wcp360:/etc/wcp360 \
  -v /var/lib/wcp360:/var/lib/wcp360 \
  -v /srv/www:/srv/www \
  wcp360:v0.1.0
```

## API

All JSON API endpoints require a Bearer token obtained from `POST /api/v1/auth/login`.

```bash
# Login
TOKEN=$(curl -s -X POST http://localhost:8080/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"username":"admin","password":"admin123"}' | jq -r .token)

# List tenants
curl -H "Authorization: Bearer $TOKEN" \
  http://localhost:8080/api/v1/tenants

# Create tenant
curl -X POST -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"username":"alice","plan":"starter","disk_quota_mb":2048}' \
  http://localhost:8080/api/v1/tenants
```

## Environment Variables

All config fields can be overridden with `WCP360_*` env variables:

```
WCP360_LISTEN_ADDR         default: :8080
WCP360_ENV                 default: production
WCP360_DATABASE_PATH       default: /var/lib/wcp360/state.db
WCP360_JWT_SECRET          required in production
WCP360_ADMIN_PASSWORD_HASH required in production (bcrypt)
WCP360_DOMAIN              default: localhost
WCP360_SMTP_HOST           optional — enables email
WCP360_REDIS_ADDR          optional — enables cache
```

## Links

- [Changelog](changelog.md)
- [Architecture](architecture.md)
- [GitHub](https://github.com/wcp360/wcp360)
- [Website](https://www.wcp360.com)
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
