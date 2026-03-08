// ======================================================================
// WCP 360 | V0.1.0 | internal/database/seeder.go
// ======================================================================

package database

import (
	"database/sql"
	"fmt"
	"log/slog"

	"github.com/wcp360/wcp360/internal/config"
)

func Seed(db *sql.DB, cfg *config.Config) error {
	if cfg.AdminPasswordHash == "" {
		slog.Debug("seeder: no admin_password_hash configured, skipping")
		return nil
	}
	var count int
	if err := db.QueryRow(`SELECT COUNT(*) FROM admins`).Scan(&count); err != nil {
		return fmt.Errorf("seeder: count admins: %w", err)
	}
	if count > 0 {
		return nil
	}
	_, err := db.Exec(
		`INSERT INTO admins(username, email, password_hash, role) VALUES(?,?,?,?)`,
		cfg.AdminUsername, cfg.AdminEmail, cfg.AdminPasswordHash, "root",
	)
	if err != nil {
		return fmt.Errorf("seeder: insert admin: %w", err)
	}
	slog.Info("seeder: root admin created", "username", cfg.AdminUsername)
	return nil
}
