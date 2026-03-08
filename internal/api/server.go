// ======================================================================
// WCP 360 | V0.1.0 | internal/api/server.go
// ======================================================================

package api

import (
	"context"
	"fmt"
	"net/http"
	"time"

	"github.com/wcp360/wcp360/internal/api/middleware"
	"github.com/wcp360/wcp360/internal/cache"
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database"
	"github.com/wcp360/wcp360/internal/services"
)

type Server struct {
	cfg         *config.Config
	db          *database.DB
	httpServer  *http.Server
	bgCancel    context.CancelFunc
	loginRL     *middleware.RateLimiter
	redisClient *cache.Client
}

func New(cfg *config.Config, db *database.DB) *Server {
	bgCtx, bgCancel := context.WithCancel(context.Background())

	loginRL := middleware.NewRateLimiter(bgCtx, 5, time.Minute, 5*time.Minute)

	mailer := services.NewMailer(cfg)
	var redisClient *cache.Client
	if cfg.RedisEnabled() {
		redisClient = cache.New(cfg.RedisAddr, cfg.RedisPassword, cfg.RedisDB)
	}

	mux := http.NewServeMux()
	s := &Server{cfg: cfg, db: db, bgCancel: bgCancel, loginRL: loginRL, redisClient: redisClient}
	s.registerRoutes(mux, mailer, redisClient)

	s.httpServer = &http.Server{
		Addr:         cfg.ListenAddr,
		Handler:      mux,
		ReadTimeout:  15 * time.Second,
		WriteTimeout: 30 * time.Second,
		IdleTimeout:  120 * time.Second,
	}
	db.StartPruner(bgCtx, time.Hour)
	return s
}

func (s *Server) Handler() http.Handler { return s.httpServer.Handler }

func (s *Server) Start() error {
	if err := s.httpServer.ListenAndServe(); err != nil {
		return fmt.Errorf("server.Start: %w", err)
	}
	return nil
}

func (s *Server) Shutdown(ctx context.Context) error {
	s.bgCancel()
	if s.redisClient != nil { s.redisClient.Close() }
	return s.httpServer.Shutdown(ctx)
}

func (s *Server) handleHealth(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")
	fmt.Fprintf(w, `{"status":"ok","service":"wcp360","version":"v0.1.0","env":%q}`, s.cfg.Env)
}

func (s *Server) handleRoot(w http.ResponseWriter, r *http.Request) {
	if r.URL.Path != "/" { http.NotFound(w, r); return }
	w.Header().Set("Content-Type", "text/plain; charset=utf-8")
	fmt.Fprintln(w, "WCP360 v0.1.0 — Modern Web Control Panel")
	fmt.Fprintln(w, "Admin UI:  /admin/")
	fmt.Fprintln(w, "API:       /api/v1/")
	fmt.Fprintln(w, "Health:    /healthz")
}
