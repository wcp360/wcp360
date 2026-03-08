// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/config/config.go
// Description: Configuration loader — YAML file + environment variable overrides.
// ======================================================================

package config

import (
	"fmt"
	"os"
	"strings"

	"gopkg.in/yaml.v3"
)

type Config struct {
	ListenAddr        string `yaml:"listen_addr"`
	Env               string `yaml:"env"`
	LogLevel          string `yaml:"log_level"`
	DatabasePath      string `yaml:"database_path"`
	DataDir           string `yaml:"data_dir"`
	JWTSecret         string `yaml:"jwt_secret"`
	AdminEmail        string `yaml:"admin_email"`
	AdminUsername     string `yaml:"admin_username"`
	AdminPasswordHash string `yaml:"admin_password_hash"`
}

var searchPaths = []string{
	"/etc/wcp360/wcp360.yaml",
	"./wcp360.yaml",
	"./configs/wcp360.yaml",
}

func Load() (*Config, error) {
	cfg := defaults()
	for _, path := range searchPaths {
		data, err := os.ReadFile(path)
		if err != nil {
			continue
		}
		if err := yaml.Unmarshal(data, cfg); err != nil {
			return nil, fmt.Errorf("config: parse error in %s: %w", path, err)
		}
		break
	}
	applyEnvOverrides(cfg)
	if err := cfg.validate(); err != nil {
		return nil, fmt.Errorf("config: validation failed: %w", err)
	}
	return cfg, nil
}

func defaults() *Config {
	return &Config{
		ListenAddr:    ":8080",
		Env:           "production",
		LogLevel:      "info",
		DatabasePath:  "/var/lib/wcp360/state.db",
		DataDir:       "/srv/www",
		AdminUsername: "admin",
	}
}

func applyEnvOverrides(cfg *Config) {
	envMap := map[string]*string{
		"WCP360_LISTEN_ADDR":         &cfg.ListenAddr,
		"WCP360_ENV":                 &cfg.Env,
		"WCP360_LOG_LEVEL":           &cfg.LogLevel,
		"WCP360_DATABASE_PATH":       &cfg.DatabasePath,
		"WCP360_DATA_DIR":            &cfg.DataDir,
		"WCP360_JWT_SECRET":          &cfg.JWTSecret,
		"WCP360_ADMIN_EMAIL":         &cfg.AdminEmail,
		"WCP360_ADMIN_USERNAME":      &cfg.AdminUsername,
		"WCP360_ADMIN_PASSWORD_HASH": &cfg.AdminPasswordHash,
	}
	for key, ptr := range envMap {
		if v := os.Getenv(key); v != "" {
			*ptr = v
		}
	}
}

func (c *Config) validate() error {
	if c.ListenAddr == "" {
		return fmt.Errorf("listen_addr is required")
	}
	validEnvs := map[string]bool{"development": true, "production": true, "test": true}
	if !validEnvs[c.Env] {
		return fmt.Errorf("env must be development|production|test, got %q", c.Env)
	}
	validLevels := map[string]bool{"debug": true, "info": true, "warn": true, "error": true}
	if !validLevels[strings.ToLower(c.LogLevel)] {
		return fmt.Errorf("log_level must be debug|info|warn|error, got %q", c.LogLevel)
	}
	if c.JWTSecret != "" && len(c.JWTSecret) < 32 {
		return fmt.Errorf("jwt_secret must be at least 32 characters")
	}
	if c.Env == "production" && c.AdminPasswordHash == "" {
		return fmt.Errorf("admin_password_hash is required in production")
	}
	if c.AdminPasswordHash != "" && !strings.HasPrefix(c.AdminPasswordHash, "$2") {
		return fmt.Errorf("admin_password_hash must be a bcrypt hash (starts with $2a$, $2b$, or $2y$)")
	}
	return nil
}
