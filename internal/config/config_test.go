// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine | V0.1.0
// File: internal/config/config_test.go
// ======================================================================

package config

import (
	"os"
	"testing"
)

func TestDefaults(t *testing.T) {
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
	}
}
