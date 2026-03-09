// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.1.0
// Website: https://www.wcp360.com
// File: internal/api/handlers/handlers.go
// Description: Handlers struct constructor — holds shared cfg + db.
// ======================================================================

package handlers

import (
	"net/http"

	"github.com/wcp360/wcp360/internal/api/middleware"
	"github.com/wcp360/wcp360/internal/cache"
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database"
	"github.com/wcp360/wcp360/internal/services"
)

type Handlers struct {
	cfg    *config.Config
	db     *database.DB
	mailer services.Mailer
	cache  *cache.Client
}

func New(cfg *config.Config, db *database.DB, mailer services.Mailer, redisClient *cache.Client) *Handlers {
	return &Handlers{cfg: cfg, db: db, mailer: mailer, cache: redisClient}
}

func actorFromContext(r *http.Request) string {
	if claims := middleware.ClaimsFromContext(r.Context()); claims != nil {
		return claims.Username
	}
	return "system"
}
