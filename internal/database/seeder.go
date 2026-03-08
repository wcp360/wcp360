// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/database/seeder.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/seeder.go
// Description: Creates root admin from config on first boot. Idempotent.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package database

import (
<<<<<<< HEAD
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
=======
	"context"
	"fmt"
	"log/slog"
	"time"

	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database/queries"
)

func (db *DB) Seed(cfg *config.Config) error {
	ctx, cancel := context.WithTimeout(context.Background(), 10*time.Second)
	defer cancel()

	exists, err := queries.AdminExists(ctx, db.DB)
	if err != nil {
		return fmt.Errorf("seeder: check admins: %w", err)
	}
	if exists {
		slog.Debug("seeder: admin table populated, skipping")
		return nil
	}

	if cfg.AdminUsername == "" || cfg.AdminPasswordHash == "" {
		return fmt.Errorf("seeder: admin_username and admin_password_hash must be set in config")
	}

	id, err := queries.CreateAdmin(ctx, db.DB, cfg.AdminUsername, cfg.AdminPasswordHash, cfg.AdminEmail, "root")
	if err != nil {
		return fmt.Errorf("seeder: create root admin: %w", err)
	}

	slog.Info("seeder: root admin created", "id", id, "username", cfg.AdminUsername)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	return nil
}
