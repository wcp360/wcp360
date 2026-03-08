// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/config/config_test.go
// Description: Unit tests for configuration loader.
// ======================================================================

package config

import (
	"os"
	"testing"
)

func TestDefaults(t *testing.T) {
	cfg, err := Load()
	if err != nil {
		t.Fatalf("Load() with defaults should not error, got: %v", err)
	}
	if cfg.ListenAddr != ":8080" {
		t.Errorf("expected :8080, got %s", cfg.ListenAddr)
	}
}

func TestEnvOverride(t *testing.T) {
	t.Setenv("WCP360_LISTEN_ADDR", ":9090")
	t.Setenv("WCP360_ENV", "development")

	cfg, err := Load()
	if err != nil {
		t.Fatalf("unexpected error: %v", err)
	}
	if cfg.ListenAddr != ":9090" {
		t.Errorf("expected :9090, got %s", cfg.ListenAddr)
	}
}

func TestYAMLLoad(t *testing.T) {
	content := `listen_addr: ":7777"
env: "test"
log_level: "debug"
admin_email: "test@example.com"
`
	f, err := os.CreateTemp("", "wcp360-*.yaml")
	if err != nil {
		t.Fatal(err)
	}
	defer os.Remove(f.Name())
	f.WriteString(content)
	f.Close()

	orig := searchPaths
	searchPaths = []string{f.Name()}
	defer func() { searchPaths = orig }()

	cfg, err := Load()
	if err != nil {
		t.Fatalf("unexpected error: %v", err)
	}
	if cfg.ListenAddr != ":7777" {
		t.Errorf("expected :7777, got %s", cfg.ListenAddr)
	}
}

func TestValidation_InvalidEnv(t *testing.T) {
	t.Setenv("WCP360_ENV", "staging")
	_, err := Load()
	if err == nil {
		t.Fatal("expected validation error for invalid env")
	}
}
