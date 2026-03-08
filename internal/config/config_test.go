// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
<<<<<<< HEAD
// Creator: HADJ RAMDANE Yacine | V0.1.0
// File: internal/config/config_test.go
=======
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/config/config_test.go
// Description: Unit tests for configuration loader.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package config

import (
	"os"
	"testing"
)

func TestDefaults(t *testing.T) {
<<<<<<< HEAD
	cfg := defaults()
	if cfg.ListenAddr != ":8080" {
		t.Errorf("ListenAddr default = %q, want :8080", cfg.ListenAddr)
	}
	if cfg.SMTPPort != 587 {
		t.Errorf("SMTPPort default = %d, want 587", cfg.SMTPPort)
	}
}

func TestIsProd(t *testing.T) {
	cfg := &Config{Env: "production"}
	if !cfg.IsProd() { t.Error("IsProd() should be true for production") }
	cfg.Env = "development"
	if cfg.IsProd() { t.Error("IsProd() should be false for development") }
}

func TestEmailEnabled(t *testing.T) {
	cfg := &Config{}
	if cfg.EmailEnabled() { t.Error("EmailEnabled() false when no host") }
	cfg.SMTPHost = "smtp.example.com"
	if !cfg.EmailEnabled() { t.Error("EmailEnabled() true when host set") }
}

func TestRedisEnabled(t *testing.T) {
	cfg := &Config{}
	if cfg.RedisEnabled() { t.Error("RedisEnabled() false when no addr") }
	cfg.RedisAddr = "localhost:6379"
	if !cfg.RedisEnabled() { t.Error("RedisEnabled() true when addr set") }
}

func TestEnvOverride(t *testing.T) {
	os.Setenv("WCP360_LISTEN_ADDR", ":9090")
	defer os.Unsetenv("WCP360_LISTEN_ADDR")
	cfg := defaults()
	applyEnvOverrides(cfg)
	if cfg.ListenAddr != ":9090" {
		t.Errorf("env override failed, got %q", cfg.ListenAddr)
	}
}

func TestValidate_InvalidEnv(t *testing.T) {
	cfg := defaults()
	cfg.Env = "staging"
	cfg.AdminPasswordHash = "$2a$12$test"
	if err := cfg.validate(); err == nil {
		t.Error("expected validation error for invalid env")
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	}
}
