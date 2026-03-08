// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/db.go
// Description: SQLite open + WAL mode + pragmas. Pure Go, no CGO.
// ======================================================================

package database

import (
	"context"
	"database/sql"
	"fmt"
	"log/slog"
	"os"
	"path/filepath"
	"time"

	_ "github.com/ncruces/go-sqlite3/driver"
	_ "github.com/ncruces/go-sqlite3/embed"
)

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
}
