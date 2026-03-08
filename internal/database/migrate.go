// ======================================================================
// WCP 360 | V0.1.0 | internal/database/migrate.go
// ======================================================================

package database

import (
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
