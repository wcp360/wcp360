# Changelog

<<<<<<< HEAD
## [v0.1.0] — 2026-03-08 — Provisioning · Email · Redis · FrankenPHP


## [v0.1.0] — 2026-03-08 — Provisioning · Email · Redis · FrankenPHP

### Added
- `internal/services/provisioner.go` — filesystem provisioning (ProvisionTenant + DeprovisionTenant, path-traversal-safe)
- `internal/services/email.go` — Mailer interface + SMTPMailer (stdlib) + NoopMailer + NewMailer factory
- `internal/cache/redis.go` — stdlib RESP client, graceful degradation, nil-safe
- `POST /api/v1/tenants/{id}/invite` — send welcome email to tenant
- `DELETE /api/v1/tenants/{id}?purge=true` — hard-delete with filesystem removal
- `caddy/Caddyfile.dev` — HTTP-only development config
- Dockerfile multi-stage (scratch image)

### Changed
- `config.go` — SMTPHost/Port/User/Pass/From/StartTLS, RedisAddr/Password/DB, Domain; EmailEnabled(), RedisEnabled()
- `handlers/tenant.go` — CreateTenant provisions filesystem; DeleteTenant supports ?purge=true
- `api/server.go` — wires NewMailer + cache.New, closes Redis on Shutdown
- `caddy/Caddyfile` — FrankenPHP worker mode, TLS auto, wildcard tenant sites

## [v0.0.9] — 2026-03-08 — HTMX Toggle · Audit Pagination · Dashboard Tests

### Added
- `POST /admin/tenants/{id}/toggle-status` — HTMX fragment swap or plain redirect
- `GET /api/v1/audit?page=&per_page=` — paginated audit (COUNT(*) OVER() window function)
- `?audit_limit=` on tenant detail (whitelist 10/25/50/100)
- 13 web UI integration tests (dashboard_test.go)

## [v0.0.8] — 2026-03-08 — CI Pipeline · Tenant Detail Page · buildQS

### Added
- `.github/workflows/ci.yml` — go vet → golangci-lint → test -race → coverage artifact
- `tenant_detail.html` — full detail page with edit form and audit timeline
- `GET /admin/tenants/{id}` + `POST /admin/tenants/{id}/update`
- `buildQS` template funcMap helper for clean pagination URLs
- `queries.GetTenantByUsername` (NOCASE indexed)

## [v0.0.7] — 2026-03-08 — Rate Limiter Fixes · Per-Tenant Audit · Lint

### Fixed
- Retry-After header: `strconv.Itoa()` replaces single-char `string(rune())` bug
- Rate limiter cleanup goroutine now stops on ctx.Done()

### Added
- `Server.bgCtx/bgCancel` shared by pruner + rate limiter
- `GET /api/v1/tenants/{id}/audit` — per-tenant audit log
- `config.IsProd()` helper
- `.golangci.yml` with errcheck, bodyclose, contextcheck, misspell

## [v0.0.6] — 2026-03-08 — Rate Limiting · Template Cache · Filters

### Added
- IP sliding-window rate limiter (5 req/min, X-Real-IP aware, Retry-After headers)
- Template renderer prewarm cache (sync.Map, zero ParseFS per request)
- Cookie Secure flag via `SetSessionCookie(isProd bool)`
- Tenant list filters: `?search=&status=&plan=` on API + web UI
### Added
- Filesystem provisioning: `ProvisionTenant` / `DeprovisionTenant`
- Email invites: `Mailer` interface, `SMTPMailer`, `NoopMailer`
- Redis cache client (stdlib RESP, graceful degradation)
- `POST /api/v1/tenants/{id}/invite`
- `caddy/Caddyfile.dev` — HTTP-only development config
- `config.EmailEnabled()`, `config.RedisEnabled()` helpers

### Changed
- `CreateTenant` provisions filesystem after DB insert
- `DeleteTenant ?purge=true` removes home directory
- Caddy config: FrankenPHP worker mode + TLS auto + wildcard tenant sites

## [v0.0.9] — 2026-03-08 — Toggle · Audit Pagination · Dashboard Tests

### Added
- `POST /admin/tenants/{id}/toggle-status` — HTMX badge fragment
- `GET /api/v1/audit?page=1&per_page=50` — paginated mode
- `?audit_limit=` on tenant detail page (10/25/50/100)
- 13 web UI integration tests

## [v0.0.8] — 2026-03-08 — Detail Page · CI · buildQS

### Added
- `.github/workflows/ci.yml` — go vet + lint + test-race + coverage
- `GET /admin/tenants/{id}` — tenant detail page
- `POST /admin/tenants/{id}/update` — edit form
- `buildQS` template helper (omits empty query params)

## [v0.0.7] — 2026-03-08 — Bugfixes · Tenant Audit · Linting

### Fixed
- `Retry-After` header: was single char for values ≥ 10 (now `strconv.Itoa`)

### Added
- `GET /api/v1/tenants/{id}/audit` — per-tenant audit log
- `.golangci.yml` configuration
- Rate limiter cleanup goroutine now context-aware (stops on shutdown)

## [v0.0.6] — 2026-03-08 — Rate Limiter · Renderer Cache · Filters

### Added
- Rate limiter middleware (5 req/min/IP, X-Real-IP aware)
- Template renderer cache (`prewarm` at boot)
- Tenant filters: `?search=&status=&plan=`

## [v0.0.5] — 2026-03-08 — Admin Dashboard · Web UI

### Added
- Complete admin web UI: login, dashboard, tenant list, audit timeline
- Cookie-based session auth (`wcp_session`)
- HTMX support

## [v0.0.4] — 2026-03-08 — Tenant CRUD · Audit · Pruner

### Added
- 5 tenant endpoints (list, create, get, patch, soft-delete)
- `audit_log` table — fire-and-forget `LogAction`
- Session pruner goroutine

## [v0.0.3] — 2026-03-08 — Database · SQLite · Migrations

### Added
- `ncruces/go-sqlite3` (pure Go, no CGO)
- WAL mode, idempotent migrations, seeder
- JWT blocklist via sessions table

## [v0.0.2] — 2026-03-08 — Authentication

### Added
- JWT HS256, 24h TTL, JTI, bcrypt cost=12
- `POST /api/v1/auth/login|logout`, `GET /api/v1/auth/me`
- `RequireAuth` / `RequireRole` middleware

## [v0.0.1] — 2026-03-08 — Foundation

### Added
- Config loader (YAML + env), `GET /healthz`, Caddy config, systemd unit
=======
## [v0.0.5] — 2026-03-08 — Admin Dashboard

### Added
- Web UI admin panel (`/admin/*`) — login, dashboard, tenants list, audit log
- `internal/services/auth_service.go` — shared LoginAdmin + ValidateWebSession
- `internal/web/` — embedded HTML templates (html/template + embed.FS)
- `internal/api/middleware/webauth.go` — HttpOnly cookie auth, SameSite=Strict
- `internal/api/handlers/dashboard.go` — 7 web UI handlers
- `internal/api/handlers/audit.go` — GET /api/v1/audit
- `internal/database/queries/stats.go` — GetDashboardStats, CountTenants
- Pagination on GET /api/v1/tenants: `?page=1&per_page=20`
- Full integration test suite (10 tests, real JWT + real SQLite)

## [v0.0.4] — 2026-03-07 — Tenant CRUD + Audit + Pruner

### Added
- Full tenant CRUD (GET/POST/PATCH/DELETE /api/v1/tenants)
- Soft-delete (sets deleted_at, status=deleted)
- audit_log: fire-and-forget LogAction, append-only (INV-8)
- Background pruner goroutine (hourly session cleanup)
- Username validation regex + reserved names list

## [v0.0.3] — 2026-03-06 — Database

### Added
- SQLite WAL mode + full pragma set
- Migration runner (embedded SQL, versioned, idempotent)
- Seeder (root admin from config, first boot only)
- Schema: admins, tenants, sessions (JWT blocklist), audit_log
- Real logout: InvalidateSession marks JTI; RequireAuth checks blocklist

## [v0.0.2] — 2026-03-05 — Authentication

### Added
- JWT HS256 (24h TTL, JTI, GenerateToken/ValidateToken)
- bcrypt cost=12, timing-safe CheckPasswordTimingSafe
- RequireAuth / RequireRole middleware
- POST /api/v1/auth/login, /logout, GET /api/v1/auth/me

## [v0.0.1] — 2026-03-04 — Foundation

### Added
- Config loader (YAML + env overrides)
- GET /healthz endpoint
- Caddy reverse-proxy config
- Systemd unit (fully hardened)
- install.sh (interactive + --non-interactive)
- MkDocs Material documentation
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
