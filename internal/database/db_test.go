// ======================================================================
// WCP 360 | V0.1.0 | internal/database/db_test.go
// ======================================================================

package database

import (
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
}
