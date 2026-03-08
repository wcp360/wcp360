// ======================================================================
// WCP 360 | V0.1.0 | internal/services/provisioner_test.go
// ======================================================================

package services

import (
	"os"
	"path/filepath"
	"testing"
)

func TestProvisionTenant_CreatesLayout(t *testing.T) {
	dataDir := t.TempDir()
	fs, err := ProvisionTenant(dataDir, "alice")
	if err != nil { t.Fatalf("ProvisionTenant: %v", err) }
	for _, d := range []string{fs.HomeDir, fs.PublicHTML, fs.LogsDir, fs.TmpDir} {
		if info, err := os.Stat(d); err != nil || !info.IsDir() {
			t.Errorf("dir %q missing or not a dir: %v", d, err)
		}
	}
	if _, err := os.Stat(filepath.Join(fs.HomeDir, ".keep")); err != nil {
		t.Error(".keep missing:", err)
	}
}

func TestProvisionTenant_Idempotent(t *testing.T) {
	dataDir := t.TempDir()
	if _, err := ProvisionTenant(dataDir, "bob"); err != nil { t.Fatal(err) }
	indexPath := filepath.Join(dataDir, "bob", "public_html", "index.html")
	os.WriteFile(indexPath, []byte("<h1>custom</h1>"), 0644)
	if _, err := ProvisionTenant(dataDir, "bob"); err != nil { t.Fatal(err) }
	got, _ := os.ReadFile(indexPath)
	if string(got) != "<h1>custom</h1>" { t.Error("index.html overwritten") }
}

func TestProvisionTenant_PathTraversal(t *testing.T) {
	dataDir := t.TempDir()
	for _, u := range []string{"../etc", "a/b", "a\x00b", ""} {
		if _, err := ProvisionTenant(dataDir, u); err == nil {
			t.Errorf("expected error for username %q", u)
		}
	}
}

func TestDeprovisionTenant(t *testing.T) {
	dataDir := t.TempDir()
	ProvisionTenant(dataDir, "carol")
	if err := DeprovisionTenant(dataDir, "carol"); err != nil { t.Fatal(err) }
	home := filepath.Join(dataDir, "carol")
	if _, err := os.Stat(home); !os.IsNotExist(err) {
		t.Error("home dir should be removed")
	}
}

func TestDeprovisionTenant_Idempotent(t *testing.T) {
	dataDir := t.TempDir()
	if err := DeprovisionTenant(dataDir, "dave"); err != nil {
		t.Errorf("deprovision non-existent: expected nil, got %v", err)
	}
}

func TestDeprovisionTenant_SafetyCheck(t *testing.T) {
	if err := DeprovisionTenant("/tmp/wcp360-test", "../etc"); err == nil {
		t.Error("expected path traversal error")
	}
}

func TestNewTenantFS_Paths(t *testing.T) {
	fs := NewTenantFS("/srv/www", "alice")
	if fs.HomeDir != "/srv/www/alice" { t.Errorf("HomeDir = %q", fs.HomeDir) }
	if fs.PublicHTML != "/srv/www/alice/public_html" { t.Errorf("PublicHTML = %q", fs.PublicHTML) }
}
