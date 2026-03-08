// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.1.0
// Website: https://www.wcp360.com
// File: cmd/wcp360/main.go
// Description: Entry point — loads config, opens DB, starts server.
//              Handles SIGTERM/SIGINT for graceful shutdown (30s timeout).
// ======================================================================

package main

import (
	"context"
	"log/slog"
	"os"
	"os/signal"
	"syscall"
	"time"

	"github.com/wcp360/wcp360/internal/api"
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database"
)

func main() {
	cfg, err := config.Load()
	if err != nil {
		slog.Error("failed to load config", "err", err)
		os.Exit(1)
	}

	setupLogger(cfg.LogLevel)
	slog.Info("WCP360 starting",
		"version", "v0.1.0",
		"env", cfg.Env,
		"addr", cfg.ListenAddr)

	db, err := database.Open(cfg.DatabasePath)
	if err != nil {
		slog.Error("failed to open database", "err", err)
		os.Exit(1)
	}
	defer db.Close()

	if err := database.Migrate(db.DB); err != nil {
		slog.Error("migration failed", "err", err)
		os.Exit(1)
	}

	if err := database.Seed(db.DB, cfg); err != nil {
		slog.Error("seeding failed", "err", err)
		os.Exit(1)
	}

	srv := api.New(cfg, db)

	// Graceful shutdown on SIGTERM / SIGINT
	stop := make(chan os.Signal, 1)
	signal.Notify(stop, syscall.SIGTERM, syscall.SIGINT)

	go func() {
		slog.Info("HTTP server listening", "addr", cfg.ListenAddr)
		if err := srv.Start(); err != nil {
			slog.Error("server stopped", "err", err)
		}
	}()

	<-stop
	slog.Info("shutdown signal received")

	ctx, cancel := context.WithTimeout(context.Background(), 30*time.Second)
	defer cancel()

	if err := srv.Shutdown(ctx); err != nil {
		slog.Error("graceful shutdown failed", "err", err)
		os.Exit(1)
	}
	slog.Info("WCP360 stopped cleanly")
}

func setupLogger(level string) {
	var lvl slog.Level
	switch level {
	case "debug":
		lvl = slog.LevelDebug
	case "warn":
		lvl = slog.LevelWarn
	case "error":
		lvl = slog.LevelError
	default:
		lvl = slog.LevelInfo
	}
	slog.SetDefault(slog.New(slog.NewJSONHandler(os.Stdout, &slog.HandlerOptions{
		Level: lvl,
	})))
}
