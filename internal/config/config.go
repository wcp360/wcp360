// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
<<<<<<< HEAD
// Version: V0.1.0
// Website: https://www.wcp360.com
// File: internal/config/config.go
// Description: Configuration loader — YAML + WCP360_* env overrides.
=======
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/config/config.go
// Description: Configuration loader — YAML file + environment variable overrides.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
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
<<<<<<< HEAD
	Domain            string `yaml:"domain"`
	SMTPHost          string `yaml:"smtp_host"`
	SMTPPort          int    `yaml:"smtp_port"`
	SMTPUsername      string `yaml:"smtp_username"`
	SMTPPassword      string `yaml:"smtp_password"`
	SMTPFrom          string `yaml:"smtp_from"`
	SMTPStartTLS      bool   `yaml:"smtp_starttls"`
	RedisAddr         string `yaml:"redis_addr"`
	RedisPassword     string `yaml:"redis_password"`
	RedisDB           int    `yaml:"redis_db"`
=======
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
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
<<<<<<< HEAD
		ListenAddr:   ":8080",
		Env:          "production",
		LogLevel:     "info",
		DatabasePath: "/var/lib/wcp360/state.db",
		DataDir:      "/srv/www",
		AdminUsername: "admin",
		SMTPPort:     587,
		SMTPStartTLS: true,
		Domain:       "localhost",
=======
		ListenAddr:    ":8080",
		Env:           "production",
		LogLevel:      "info",
		DatabasePath:  "/var/lib/wcp360/state.db",
		DataDir:       "/srv/www",
		AdminUsername: "admin",
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	}
}

func applyEnvOverrides(cfg *Config) {
<<<<<<< HEAD
	strMap := map[string]*string{
=======
	envMap := map[string]*string{
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
		"WCP360_LISTEN_ADDR":         &cfg.ListenAddr,
		"WCP360_ENV":                 &cfg.Env,
		"WCP360_LOG_LEVEL":           &cfg.LogLevel,
		"WCP360_DATABASE_PATH":       &cfg.DatabasePath,
		"WCP360_DATA_DIR":            &cfg.DataDir,
		"WCP360_JWT_SECRET":          &cfg.JWTSecret,
		"WCP360_ADMIN_EMAIL":         &cfg.AdminEmail,
		"WCP360_ADMIN_USERNAME":      &cfg.AdminUsername,
		"WCP360_ADMIN_PASSWORD_HASH": &cfg.AdminPasswordHash,
<<<<<<< HEAD
		"WCP360_DOMAIN":              &cfg.Domain,
		"WCP360_SMTP_HOST":           &cfg.SMTPHost,
		"WCP360_SMTP_USERNAME":       &cfg.SMTPUsername,
		"WCP360_SMTP_PASSWORD":       &cfg.SMTPPassword,
		"WCP360_SMTP_FROM":           &cfg.SMTPFrom,
		"WCP360_REDIS_ADDR":          &cfg.RedisAddr,
		"WCP360_REDIS_PASSWORD":      &cfg.RedisPassword,
	}
	for key, ptr := range strMap {
=======
	}
	for key, ptr := range envMap {
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
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
<<<<<<< HEAD
		return fmt.Errorf("admin_password_hash must be a bcrypt hash")
	}
	if c.SMTPPort < 1 || c.SMTPPort > 65535 {
		c.SMTPPort = 587
	}
	return nil
}

func (c *Config) IsProd() bool       { return c.Env == "production" }
func (c *Config) EmailEnabled() bool  { return c.SMTPHost != "" }
func (c *Config) RedisEnabled() bool  { return c.RedisAddr != "" }
=======
		return fmt.Errorf("admin_password_hash must be a bcrypt hash (starts with $2a$, $2b$, or $2y$)")
	}
	return nil
}
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
