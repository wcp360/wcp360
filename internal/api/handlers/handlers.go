// ======================================================================
// WCP 360 | V0.1.0 | internal/api/handlers/handlers.go
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
