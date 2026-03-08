// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: cmd/wcp360/main.go
// Description: Binary entry point — config → DB → migrate → seed → HTTP server.
// ======================================================================

package main

import (
	"context"
	"errors"
	"log/slog"
	"net/http"
	"os"
	"os/signal"
	"syscall"
	"time"

	"github.com/wcp360/wcp360/internal/api"
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database"
)

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
		os.Exit(1)
	}
	defer db.Close()

	if err := db.Migrate(); err != nil {
		slog.Error("database migration failed", "err", err)
		os.Exit(1)
	}

	if err := db.Seed(cfg); err != nil {
		slog.Error("database seeding failed", "err", err)
		os.Exit(1)
	}

	srv := api.New(cfg, db)

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
