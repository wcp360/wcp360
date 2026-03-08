# Changelog

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
