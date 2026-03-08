-- ======================================================================
-- WCP 360 | V0.1.0 | migrations/001_initial.sql
-- ======================================================================

CREATE TABLE IF NOT EXISTS admins (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    username      TEXT    NOT NULL UNIQUE COLLATE NOCASE,
    email         TEXT    NOT NULL UNIQUE COLLATE NOCASE,
    password_hash TEXT    NOT NULL,
    role          TEXT    NOT NULL DEFAULT 'admin',
    created_at    TEXT    NOT NULL DEFAULT (datetime('now','utc')),
    updated_at    TEXT    NOT NULL DEFAULT (datetime('now','utc'))
);

CREATE TABLE IF NOT EXISTS tenants (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    username      TEXT    NOT NULL UNIQUE COLLATE NOCASE,
    email         TEXT    NOT NULL COLLATE NOCASE,
    plan          TEXT    NOT NULL DEFAULT 'starter',
    status        TEXT    NOT NULL DEFAULT 'active',
    disk_quota_mb INTEGER NOT NULL DEFAULT 1024,
    bandwidth_mb  INTEGER NOT NULL DEFAULT 10240,
    max_sites     INTEGER NOT NULL DEFAULT 1,
    home_dir      TEXT    NOT NULL DEFAULT '',
    created_at    TEXT    NOT NULL DEFAULT (datetime('now','utc')),
    updated_at    TEXT    NOT NULL DEFAULT (datetime('now','utc')),
    deleted_at    TEXT
);

CREATE INDEX IF NOT EXISTS idx_tenants_username  ON tenants(username);
CREATE INDEX IF NOT EXISTS idx_tenants_status    ON tenants(status) WHERE deleted_at IS NULL;

CREATE TABLE IF NOT EXISTS sessions (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    jti        TEXT    NOT NULL UNIQUE,
    username   TEXT    NOT NULL,
    role       TEXT    NOT NULL,
    created_at TEXT    NOT NULL DEFAULT (datetime('now','utc')),
    expires_at TEXT    NOT NULL,
    revoked    INTEGER NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_sessions_jti        ON sessions(jti);
CREATE INDEX IF NOT EXISTS idx_sessions_expires_at ON sessions(expires_at);

CREATE TABLE IF NOT EXISTS audit_log (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    actor      TEXT NOT NULL,
    action     TEXT NOT NULL,
    target     TEXT NOT NULL DEFAULT '',
    detail     TEXT NOT NULL DEFAULT '',
    ip_address TEXT NOT NULL DEFAULT '',
    created_at TEXT NOT NULL DEFAULT (datetime('now','utc'))
);

CREATE INDEX IF NOT EXISTS idx_audit_log_actor  ON audit_log(actor);
CREATE INDEX IF NOT EXISTS idx_audit_log_target ON audit_log(target);
CREATE INDEX IF NOT EXISTS idx_audit_log_action ON audit_log(action);

CREATE TABLE IF NOT EXISTS schema_migrations (
    version    INTEGER PRIMARY KEY,
    applied_at TEXT NOT NULL DEFAULT (datetime('now','utc'))
);
