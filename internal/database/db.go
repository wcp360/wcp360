// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/database/db.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/db.go
// Description: SQLite open + WAL mode + pragmas. Pure Go, no CGO.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package database

import (
	"context"
	"database/sql"
	"fmt"
	"log/slog"
<<<<<<< HEAD
=======
	"os"
	"path/filepath"
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	"time"

	_ "github.com/ncruces/go-sqlite3/driver"
	_ "github.com/ncruces/go-sqlite3/embed"
)

<<<<<<< HEAD
type DB struct{ *sql.DB }

func Open(path string) (*DB, error) {
	db, err := sql.Open("sqlite3", path)
	if err != nil {
		return nil, fmt.Errorf("database.Open: %w", err)
	}
	pragmas := []string{
		"PRAGMA journal_mode=WAL",
		"PRAGMA synchronous=NORMAL",
		"PRAGMA foreign_keys=ON",
		"PRAGMA busy_timeout=5000",
		"PRAGMA cache_size=-20000",
	}
	for _, p := range pragmas {
		if _, err := db.Exec(p); err != nil {
			return nil, fmt.Errorf("database.Open pragma %q: %w", p, err)
		}
	}
	db.SetMaxOpenConns(1)
	db.SetMaxIdleConns(1)
	db.SetConnMaxLifetime(0)
	return &DB{db}, nil
}

func (db *DB) StartPruner(ctx context.Context, interval time.Duration) {
	go func() {
		ticker := time.NewTicker(interval)
		defer ticker.Stop()
		for {
			select {
			case <-ctx.Done():
				return
			case <-ticker.C:
				res, err := db.ExecContext(ctx,
					`DELETE FROM sessions WHERE expires_at < datetime('now','utc')`)
				if err != nil {
					slog.Warn("pruner: session cleanup error", "err", err)
					continue
				}
				n, _ := res.RowsAffected()
				if n > 0 {
					slog.Debug("pruner: expired sessions removed", "count", n)
				}
			}
		}
	}()
=======
type DB struct {
	*sql.DB
}

func Open(path string) (*DB, error) {
	dir := filepath.Dir(path)
	if err := os.MkdirAll(dir, 0750); err != nil {
		return nil, fmt.Errorf("database: create directory %s: %w", dir, err)
	}
	sqldb, err := sql.Open("sqlite3", path)
	if err != nil {
		return nil, fmt.Errorf("database: open %s: %w", path, err)
	}
	sqldb.SetMaxOpenConns(1)
	sqldb.SetMaxIdleConns(1)
	sqldb.SetConnMaxLifetime(0)

	db := &DB{sqldb}
	if err := db.applyPragmas(); err != nil {
		sqldb.Close()
		return nil, err
	}
	slog.Info("database opened", "path", path)
	return db, nil
}

func (db *DB) applyPragmas() error {
	pragmas := []struct{ name, value string }{
		{"journal_mode", "WAL"},
		{"foreign_keys", "ON"},
		{"busy_timeout", "5000"},
		{"synchronous", "NORMAL"},
		{"cache_size", "-65536"},
		{"temp_store", "MEMORY"},
		{"mmap_size", "536870912"},
	}
	ctx, cancel := context.WithTimeout(context.Background(), 10*time.Second)
	defer cancel()
	for _, p := range pragmas {
		if _, err := db.ExecContext(ctx, fmt.Sprintf("PRAGMA %s = %s", p.name, p.value)); err != nil {
			return fmt.Errorf("database: pragma %s: %w", p.name, err)
		}
	}
	var mode string
	if err := db.QueryRowContext(ctx, "PRAGMA journal_mode").Scan(&mode); err != nil {
		return fmt.Errorf("database: verify journal_mode: %w", err)
	}
	if mode != "wal" {
		return fmt.Errorf("database: expected WAL mode, got %q", mode)
	}
	slog.Debug("database pragmas applied", "journal_mode", mode)
	return nil
}

func (db *DB) Ping(ctx context.Context) error { return db.PingContext(ctx) }

func (db *DB) Close() error {
	if _, err := db.Exec("PRAGMA wal_checkpoint(TRUNCATE)"); err != nil {
		slog.Warn("database: wal checkpoint failed", "err", err)
	}
	slog.Info("database closed")
	return db.DB.Close()
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}
