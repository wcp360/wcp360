// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/seeder.go
// Description: Creates root admin from config on first boot. Idempotent.
// ======================================================================

package database

import (
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
	return nil
}
