// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/server.go
// Description: HTTP server lifecycle — Start, Shutdown, pruner wiring.
// ======================================================================

package api

import (
	"context"
	"fmt"
	"net/http"
	"time"

	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database"
)

type Server struct {
	cfg          *config.Config
	db           *database.DB
	httpServer   *http.Server
	prunerCancel context.CancelFunc
}

func New(cfg *config.Config, db *database.DB) *Server {
	mux := http.NewServeMux()
	s := &Server{cfg: cfg, db: db}
	s.registerRoutes(mux)
	s.httpServer = &http.Server{
		Addr:         cfg.ListenAddr,
		Handler:      mux,
		ReadTimeout:  15 * time.Second,
		WriteTimeout: 30 * time.Second,
		IdleTimeout:  120 * time.Second,
	}
	prunerCtx, cancel := context.WithCancel(context.Background())
	s.prunerCancel = cancel
	db.StartPruner(prunerCtx, time.Hour)
	return s
}

// Handler returns the underlying http.Handler (used in tests).
func (s *Server) Handler() http.Handler { return s.httpServer.Handler }

func (s *Server) Start() error {
	if err := s.httpServer.ListenAndServe(); err != nil {
		return fmt.Errorf("server.Start: %w", err)
	}
	return nil
}

func (s *Server) Shutdown(ctx context.Context) error {
	s.prunerCancel()
	return s.httpServer.Shutdown(ctx)
}

// ── System handlers ────────────────────────────────────────────────────

func (s *Server) handleHealth(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")
	fmt.Fprintf(w, `{"status":"ok","service":"wcp360","version":"v0.0.5","env":%q}`, s.cfg.Env)
}

func (s *Server) handleRoot(w http.ResponseWriter, r *http.Request) {
	if r.URL.Path != "/" {
		http.NotFound(w, r)
		return
	}
	w.Header().Set("Content-Type", "text/plain; charset=utf-8")
	fmt.Fprintln(w, "WCP360 v0.0.5 — Modern Web Control Panel")
	fmt.Fprintln(w, "Admin UI:  /admin/")
	fmt.Fprintln(w, "API:       /api/v1/")
	fmt.Fprintln(w, "Health:    /healthz")
}
