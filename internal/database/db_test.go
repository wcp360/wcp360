// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/database/db_test.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine | V0.0.5
// File: internal/database/db_test.go
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package database

import (
<<<<<<< HEAD
	"testing"
	"os"
	"path/filepath"
)

func TestOpenAndMigrate(t *testing.T) {
	dir := t.TempDir()
	dbPath := filepath.Join(dir, "test.db")
	db, err := Open(dbPath)
	if err != nil { t.Fatal("Open:", err) }
	defer db.Close()
	if err := Migrate(db.DB); err != nil { t.Fatal("Migrate:", err) }
	// Run twice — idempotent
	if err := Migrate(db.DB); err != nil { t.Fatal("Migrate (2nd):", err) }
	// Verify tables exist
	tables := []string{"admins","tenants","sessions","audit_log","schema_migrations"}
	for _, tbl := range tables {
		var n int
		err := db.QueryRow(`SELECT COUNT(*) FROM `+tbl).Scan(&n)
		if err != nil { t.Errorf("table %s missing or broken: %v", tbl, err) }
	}
	_ = os.Remove(dbPath)
=======
	"context"
	"os"
	"testing"

	"github.com/wcp360/wcp360/internal/config"
)

func TestOpen(t *testing.T) {
	f, _ := os.CreateTemp("", "wcp360-test-*.db")
	f.Close()
	t.Cleanup(func() { os.Remove(f.Name()) })

	db, err := Open(f.Name())
	if err != nil {
		t.Fatalf("Open() error: %v", err)
	}
	defer db.Close()

	if err := db.Ping(context.Background()); err != nil {
		t.Errorf("Ping() failed: %v", err)
	}
}

func TestMigrate(t *testing.T) {
	f, _ := os.CreateTemp("", "wcp360-migrate-*.db")
	f.Close()
	t.Cleanup(func() { os.Remove(f.Name()) })

	db, err := Open(f.Name())
	if err != nil {
		t.Fatal(err)
	}
	defer db.Close()

	if err := db.Migrate(); err != nil {
		t.Fatalf("first Migrate() error: %v", err)
	}
	if err := db.Migrate(); err != nil {
		t.Fatalf("second Migrate() (idempotent) error: %v", err)
	}

	tables := []string{"admins", "tenants", "sessions", "audit_log", "schema_migrations"}
	for _, table := range tables {
		var name string
		err := db.QueryRow(`SELECT name FROM sqlite_master WHERE type='table' AND name=?`, table).Scan(&name)
		if err != nil || name != table {
			t.Errorf("expected table %q to exist", table)
		}
	}
}

func TestSeed(t *testing.T) {
	f, _ := os.CreateTemp("", "wcp360-seed-*.db")
	f.Close()
	t.Cleanup(func() { os.Remove(f.Name()) })

	db, _ := Open(f.Name())
	defer db.Close()
	db.Migrate()

	cfg := &config.Config{
		AdminUsername:     "testroot",
		AdminPasswordHash: "$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LZYlqAqA.aqAq",
		AdminEmail:        "root@test.com",
		Env:               "production",
	}

	if err := db.Seed(cfg); err != nil {
		t.Fatalf("first Seed() error: %v", err)
	}
	if err := db.Seed(cfg); err != nil {
		t.Fatalf("second Seed() (idempotent) error: %v", err)
	}

	var count int
	db.QueryRow("SELECT COUNT(*) FROM admins").Scan(&count)
	if count != 1 {
		t.Errorf("expected 1 admin, got %d", count)
	}
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}
