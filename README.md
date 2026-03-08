<<<<<<< HEAD
# ⬡ WCP360 — Modern Web Control Panel

[![License: MIT](https://img.shields.io/badge/License-MIT-4EFFC5.svg)](LICENSE)
[![Go 1.22](https://img.shields.io/badge/Go-1.22-00ADD8.svg)](https://golang.org)
[![Version](https://img.shields.io/badge/Version-v0.1.0-4EFFC5.svg)](docs/en/changelog.md)

> A next-generation Linux-native hosting control panel — a modern alternative to cPanel, WHM, and Plesk.

**Creator:** HADJ RAMDANE Yacine · yacine@wcp360.com · [wcp360.com](https://www.wcp360.com)

---

## Stack

| | |
|---|---|
| **Backend** | Go 1.22 — single static binary, zero runtime dependencies |
| **Web Server** | Caddy v2 — HTTP/3, automatic TLS (Let's Encrypt) |
| **PHP** | FrankenPHP worker mode — TTFB < 50ms for WordPress/Laravel |
| **Database** | SQLite WAL — no external DB process required |
| **Cache** | Redis optional — graceful degradation if unavailable |
| **Frontend** | Server-rendered HTML + HTMX — works without JavaScript |

---

## Quick Start

### Development (local)

```bash
git clone https://github.com/wcp360/wcp360.git
cd wcp360

# Generate go.sum and download deps
make tidy

# Run in development mode (uses ./wcp360.yaml)
make run

# Open: http://localhost:8080/admin/login
# Credentials: admin / admin123
```

### VPS Installation (Ubuntu 22.04/24.04)

```bash
git clone https://github.com/wcp360/wcp360.git
cd wcp360
sudo bash scripts/install.sh

# Edit config and set your admin password hash
sudo nano /etc/wcp360/wcp360.yaml

# Generate bcrypt hash for your password:
htpasswd -bnBC 12 "" YOUR_PASSWORD | tr -d ':\n'

sudo systemctl start wcp360
# Open: http://YOUR_SERVER_IP:8080/admin/login
```

### Docker

```bash
docker build -t wcp360:v0.1.0 .

docker run -d \
  --name wcp360 \
  -p 8080:8080 \
  -v /etc/wcp360:/etc/wcp360 \
  -v /var/lib/wcp360:/var/lib/wcp360 \
  -v /srv/www:/srv/www \
  wcp360:v0.1.0
```

### GitHub Codespaces / Gitpod

```bash
make tidy
WCP360_ENV=development WCP360_ADMIN_PASSWORD_HASH="" make run
```

---

## Makefile Targets

```
make build       Compile binary → ./build/wcp360
make run         Run in development mode
make test        Run all tests with race detector
make test-cover  Run tests + open HTML coverage report
make check       go vet + tests (CI-safe)
make vet         go vet ./...
make lint        golangci-lint (auto-installs if missing)
make tidy        go mod tidy (generates go.sum)
make clean       Remove build artifacts
make install     Install binary to /usr/local/bin
make docker      Build Docker image wcp360:v0.1.0
```

---

## API Quick Reference

```bash
# Get token
TOKEN=$(curl -s -X POST http://localhost:8080/api/v1/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"username":"admin","password":"admin123"}' | jq -r .token)

# Create tenant
curl -X POST http://localhost:8080/api/v1/tenants \
  -H "Authorization: Bearer $TOKEN" \
  -H 'Content-Type: application/json' \
  -d '{"username":"alice","plan":"starter","disk_quota_mb":2048,"bandwidth_mb":20480,"max_sites":3}'

# List tenants (with filters)
curl "http://localhost:8080/api/v1/tenants?status=active&plan=pro&page=1&per_page=20" \
  -H "Authorization: Bearer $TOKEN"

# Send invite email
curl -X POST http://localhost:8080/api/v1/tenants/1/invite \
  -H "Authorization: Bearer $TOKEN"

# Delete with filesystem purge
curl -X DELETE "http://localhost:8080/api/v1/tenants/1?purge=true" \
  -H "Authorization: Bearer $TOKEN"
```

---

## Configuration

Copy `wcp360.yaml` to `/etc/wcp360/wcp360.yaml` (production) or use it in-place (development).

All fields can be overridden with environment variables:

```
WCP360_LISTEN_ADDR          :8080
WCP360_ENV                  development | production | test
WCP360_DATABASE_PATH        /var/lib/wcp360/state.db
WCP360_DATA_DIR             /srv/www
WCP360_JWT_SECRET           min 32 chars — required in production
WCP360_ADMIN_USERNAME       admin
WCP360_ADMIN_EMAIL          admin@example.com
WCP360_ADMIN_PASSWORD_HASH  bcrypt hash — required in production
WCP360_DOMAIN               panel.example.com
WCP360_SMTP_HOST            optional — enables email invites
WCP360_REDIS_ADDR           optional — enables JWT cache
```

---

## Project Structure

```
wcp360/
├── cmd/wcp360/main.go              Entry point
├── internal/
│   ├── api/
│   │   ├── server.go               HTTP server + lifecycle
│   │   ├── routes.go               Route registration
│   │   ├── handlers/               HTTP handlers (auth, tenant, audit, dashboard)
│   │   └── middleware/             Logging, auth, rate limiting
│   ├── auth/                       JWT + bcrypt
│   ├── cache/                      Redis client (stdlib, graceful degradation)
│   ├── config/                     Config loader (YAML + env)
│   ├── database/
│   │   ├── db.go, migrate.go, seeder.go, pruner.go
│   │   └── queries/                Typed SQL query functions
│   ├── models/                     Domain models + validation
│   ├── services/                   auth_service, provisioner, email
│   └── web/                        Template renderer + embedded HTML
├── migrations/001_initial.sql      Database schema
├── caddy/                          Caddyfile (prod + dev)
├── scripts/install.sh              VPS installer
├── docs/en/                        Documentation (MkDocs Material)
├── Makefile
├── Dockerfile
├── wcp360.yaml                     Example config
└── go.mod
```

---

## Security

- **JWT HS256** — 24h TTL, JTI blocklist in SQLite sessions table
- **bcrypt cost=12** — timing-safe comparison
- **Cookie**: HttpOnly + SameSite=Strict + Secure (production)
- **Rate limiting**: 5 req/min per IP on login endpoints
- **SQL**: 100% parameterised queries — no string concatenation
- **Audit log**: append-only — no DELETE or UPDATE ever
- **Filesystem**: path traversal protection on all provisioner operations
- **SMTP**: STARTTLS enforced, TLS 1.2 minimum

---

## License

MIT — see [LICENSE](LICENSE)

---

*WCP360 — Creator: HADJ RAMDANE Yacine — [wcp360.com](https://www.wcp360.com)*
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
