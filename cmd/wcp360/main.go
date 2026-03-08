// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
<<<<<<< HEAD
// Version: V0.1.0
// Website: https://www.wcp360.com
// File: cmd/wcp360/main.go
// Description: Entry point — loads config, opens DB, starts server.
//              Handles SIGTERM/SIGINT for graceful shutdown (30s timeout).
=======
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: cmd/wcp360/main.go
// Description: Binary entry point — config → DB → migrate → seed → HTTP server.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package main

import (
	"context"
<<<<<<< HEAD
	"log/slog"
=======
	"errors"
	"log/slog"
	"net/http"
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	"os"
	"os/signal"
	"syscall"
	"time"

	"github.com/wcp360/wcp360/internal/api"
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database"
)

<<<<<<< HEAD
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
=======
var version = "dev"

func main() {
	slog.SetDefault(slog.New(slog.NewTextHandler(os.Stdout, &slog.HandlerOptions{Level: slog.LevelInfo})))

	cfg, err := config.Load()
	if err != nil {
		slog.Error("configuration error", "err", err)
		os.Exit(1)
	}
	slog.Info("WCP360 starting", "version", version, "listen", cfg.ListenAddr, "env", cfg.Env)

	db, err := database.Open(cfg.DatabasePath)
	if err != nil {
		slog.Error("database open failed", "path", cfg.DatabasePath, "err", err)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
		os.Exit(1)
	}
	defer db.Close()

<<<<<<< HEAD
	if err := database.Migrate(db.DB); err != nil {
		slog.Error("migration failed", "err", err)
		os.Exit(1)
	}

	if err := database.Seed(db.DB, cfg); err != nil {
		slog.Error("seeding failed", "err", err)
=======
	if err := db.Migrate(); err != nil {
		slog.Error("database migration failed", "err", err)
		os.Exit(1)
	}

	if err := db.Seed(cfg); err != nil {
		slog.Error("database seeding failed", "err", err)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
		os.Exit(1)
	}

	srv := api.New(cfg, db)

<<<<<<< HEAD
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
=======
	go func() {
		slog.Info("server ready", "addr", cfg.ListenAddr)
		if err := srv.Start(); err != nil && !errors.Is(err, http.ErrServerClosed) {
			slog.Error("server fatal error", "err", err)
			os.Exit(1)
		}
	}()

	quit := make(chan os.Signal, 1)
	signal.Notify(quit, syscall.SIGINT, syscall.SIGTERM)
	sig := <-quit
	slog.Info("shutdown initiated", "signal", sig.String())

	ctx, cancel := context.WithTimeout(context.Background(), 30*time.Second)
	defer cancel()
	if err := srv.Shutdown(ctx); err != nil {
		slog.Error("graceful shutdown error", "err", err)
	}
	slog.Info("WCP360 stopped cleanly")
}
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
