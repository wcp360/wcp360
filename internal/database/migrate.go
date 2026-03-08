// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/database/migrate.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/migrate.go
// Description: Embedded SQL migration runner — idempotent, versioned.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package database

import (
<<<<<<< HEAD
	"database/sql"
	_ "embed"
	"fmt"
	"log/slog"
)

//go:embed migrations/001_initial.sql
var migration001 string

type migration struct {
	version int
	sql     string
}

var migrations = []migration{
	{1, migration001},
}

func Migrate(db *sql.DB) error {
	for _, m := range migrations {
		var exists int
		err := db.QueryRow(
			`SELECT COUNT(*) FROM schema_migrations WHERE version = ?`, m.version).Scan(&exists)
		// Table might not exist yet
		if err != nil || exists == 0 {
			if _, err := db.Exec(m.sql); err != nil {
				return fmt.Errorf("migrate: apply v%d: %w", m.version, err)
			}
			if _, err := db.Exec(
				`INSERT OR IGNORE INTO schema_migrations(version) VALUES(?)`, m.version); err != nil {
				return fmt.Errorf("migrate: record v%d: %w", m.version, err)
			}
			slog.Info("migration applied", "version", m.version)
		}
	}
	return nil
}
=======
	"context"
	"embed"
	"fmt"
	"io/fs"
	"log/slog"
	"sort"
	"strconv"
	"strings"
	"time"
)

//go:embed ../../migrations/*.sql
var migrationsFS embed.FS

type migration struct {
	Version int
	Name    string
	SQL     string
}

func (db *DB) Migrate() error {
	ctx, cancel := context.WithTimeout(context.Background(), 60*time.Second)
	defer cancel()

	_, err := db.ExecContext(ctx, `
		CREATE TABLE IF NOT EXISTS schema_migrations (
			version    INTEGER PRIMARY KEY,
			name       TEXT    NOT NULL,
			applied_at TEXT    NOT NULL DEFAULT (datetime('now', 'utc'))
		)`)
	if err != nil {
		return fmt.Errorf("migrate: bootstrap schema_migrations: %w", err)
	}

	migrations, err := loadMigrations()
	if err != nil {
		return fmt.Errorf("migrate: load files: %w", err)
	}

	var currentVersion int
	if err := db.QueryRowContext(ctx, "SELECT COALESCE(MAX(version), 0) FROM schema_migrations").Scan(&currentVersion); err != nil {
		return fmt.Errorf("migrate: read version: %w", err)
	}

	applied := 0
	for _, m := range migrations {
		if m.Version <= currentVersion {
			continue
		}
		slog.Info("applying migration", "version", m.Version, "name", m.Name)
		tx, err := db.BeginTx(ctx, nil)
		if err != nil {
			return fmt.Errorf("migrate: begin tx v%d: %w", m.Version, err)
		}
		if _, err := tx.ExecContext(ctx, m.SQL); err != nil {
			tx.Rollback()
			return fmt.Errorf("migrate: execute v%d: %w", m.Version, err)
		}
		if _, err := tx.ExecContext(ctx, `INSERT INTO schema_migrations (version, name) VALUES (?, ?)`, m.Version, m.Name); err != nil {
			tx.Rollback()
			return fmt.Errorf("migrate: record v%d: %w", m.Version, err)
		}
		if err := tx.Commit(); err != nil {
			return fmt.Errorf("migrate: commit v%d: %w", m.Version, err)
		}
		slog.Info("migration applied", "version", m.Version, "name", m.Name)
		applied++
	}

	if applied == 0 {
		slog.Info("database schema up to date", "version", currentVersion)
	}
	return nil
}

func loadMigrations() ([]migration, error) {
	var migrations []migration
	err := fs.WalkDir(migrationsFS, ".", func(path string, d fs.DirEntry, err error) error {
		if err != nil || d.IsDir() || !strings.HasSuffix(path, ".sql") {
			return err
		}
		name := d.Name()
		parts := strings.SplitN(name, "_", 2)
		if len(parts) != 2 {
			return fmt.Errorf("migration %q must be NNN_name.sql", name)
		}
		version, err := strconv.Atoi(parts[0])
		if err != nil {
			return fmt.Errorf("migration %q version is not a number", name)
		}
		content, err := migrationsFS.ReadFile(path)
		if err != nil {
			return fmt.Errorf("read migration %s: %w", path, err)
		}
		migrations = append(migrations, migration{
			Version: version,
			Name:    strings.TrimSuffix(parts[1], ".sql"),
			SQL:     string(content),
		})
		return nil
	})
	if err != nil {
		return nil, err
	}
	sort.Slice(migrations, func(i, j int) bool { return migrations[i].Version < migrations[j].Version })
	return migrations, nil
}
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
