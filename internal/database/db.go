// ======================================================================
// WCP 360 | V0.1.0 | internal/database/db.go
// ======================================================================

package database

import (
	"context"
	"database/sql"
	"fmt"
	"log/slog"
	"time"

	_ "github.com/ncruces/go-sqlite3/driver"
	_ "github.com/ncruces/go-sqlite3/embed"
)

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
}
