// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/handlers/handlers.go
// Description: Handlers struct constructor — holds shared cfg + db.
// ======================================================================

package handlers

import (
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database"
)

type Handlers struct {
	cfg *config.Config
	db  *database.DB
}

func New(cfg *config.Config, db *database.DB) *Handlers {
	return &Handlers{cfg: cfg, db: db}
}
