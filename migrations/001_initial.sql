-- ======================================================================
<<<<<<< HEAD
-- WCP 360 | V0.1.0 | migrations/001_initial.sql
-- ======================================================================
=======
-- WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
-- ======================================================================
-- Creator: HADJ RAMDANE Yacine
-- Contact: yacine@wcp360.com
-- Version: V0.0.5
-- File: migrations/001_initial.sql
-- Description: Initial schema — admins, tenants, sessions, audit_log.
-- ======================================================================

CREATE TABLE IF NOT EXISTS schema_migrations (
    version     INTEGER PRIMARY KEY,
    name        TEXT    NOT NULL,
    applied_at  TEXT    NOT NULL DEFAULT (datetime('now', 'utc'))
);
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee

CREATE TABLE IF NOT EXISTS admins (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    username      TEXT    NOT NULL UNIQUE COLLATE NOCASE,
<<<<<<< HEAD
    email         TEXT    NOT NULL UNIQUE COLLATE NOCASE,
    password_hash TEXT    NOT NULL,
    role          TEXT    NOT NULL DEFAULT 'admin',
    created_at    TEXT    NOT NULL DEFAULT (datetime('now','utc')),
    updated_at    TEXT    NOT NULL DEFAULT (datetime('now','utc'))
=======
    password_hash TEXT    NOT NULL,
    email         TEXT    NOT NULL DEFAULT '',
    role          TEXT    NOT NULL DEFAULT 'admin',
    is_active     INTEGER NOT NULL DEFAULT 1,
    last_login_at TEXT,
    created_at    TEXT    NOT NULL DEFAULT (datetime('now', 'utc')),
    updated_at    TEXT    NOT NULL DEFAULT (datetime('now', 'utc'))
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
);

CREATE TABLE IF NOT EXISTS tenants (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    username      TEXT    NOT NULL UNIQUE COLLATE NOCASE,
<<<<<<< HEAD
    email         TEXT    NOT NULL COLLATE NOCASE,
=======
    email         TEXT    NOT NULL,
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
    plan          TEXT    NOT NULL DEFAULT 'starter',
    status        TEXT    NOT NULL DEFAULT 'active',
    disk_quota_mb INTEGER NOT NULL DEFAULT 1024,
    bandwidth_mb  INTEGER NOT NULL DEFAULT 10240,
    max_sites     INTEGER NOT NULL DEFAULT 1,
    home_dir      TEXT    NOT NULL DEFAULT '',
<<<<<<< HEAD
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
=======
    created_at    TEXT    NOT NULL DEFAULT (datetime('now', 'utc')),
    updated_at    TEXT    NOT NULL DEFAULT (datetime('now', 'utc')),
    deleted_at    TEXT
);

CREATE INDEX IF NOT EXISTS idx_tenants_username ON tenants(username);
CREATE INDEX IF NOT EXISTS idx_tenants_status   ON tenants(status);

CREATE TABLE IF NOT EXISTS sessions (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    jti         TEXT    NOT NULL UNIQUE,
    username    TEXT    NOT NULL,
    role        TEXT    NOT NULL,
    invalidated INTEGER NOT NULL DEFAULT 0,
    expires_at  TEXT    NOT NULL,
    created_at  TEXT    NOT NULL DEFAULT (datetime('now', 'utc'))
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
);

CREATE INDEX IF NOT EXISTS idx_sessions_jti        ON sessions(jti);
CREATE INDEX IF NOT EXISTS idx_sessions_expires_at ON sessions(expires_at);

CREATE TABLE IF NOT EXISTS audit_log (
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
<<<<<<< HEAD
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
=======
    actor      TEXT    NOT NULL,
    action     TEXT    NOT NULL,
    target     TEXT    NOT NULL DEFAULT '',
    detail     TEXT    NOT NULL DEFAULT '',
    ip_address TEXT    NOT NULL DEFAULT '',
    created_at TEXT    NOT NULL DEFAULT (datetime('now', 'utc'))
);

CREATE INDEX IF NOT EXISTS idx_audit_log_actor      ON audit_log(actor);
CREATE INDEX IF NOT EXISTS idx_audit_log_action     ON audit_log(action);
CREATE INDEX IF NOT EXISTS idx_audit_log_created_at ON audit_log(created_at);
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
