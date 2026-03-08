// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/pruner.go
// Description: Background goroutine pruning expired sessions every hour.
// ======================================================================

package database

import (
	"context"
	"log/slog"
	"time"

	"github.com/wcp360/wcp360/internal/database/queries"
)

func (db *DB) StartPruner(ctx context.Context, interval time.Duration) {
	go func() {
		slog.Info("session pruner started", "interval", interval)
		ticker := time.NewTicker(interval)
		defer ticker.Stop()
		for {
			select {
			case <-ctx.Done():
				slog.Info("session pruner stopped")
				return
			case <-ticker.C:
				pruneCtx, cancel := context.WithTimeout(context.Background(), 30*time.Second)
				n, err := queries.PruneExpiredSessions(pruneCtx, db.DB)
				cancel()
				if err != nil {
					slog.Error("session pruner error", "err", err)
				} else if n > 0 {
					slog.Info("session pruner: removed expired sessions", "count", n)
				}
			}
		}
	}()
}
