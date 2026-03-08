// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/handlers/audit.go
// Description: GET /api/v1/audit — audit log JSON endpoint (RoleRoot).
// ======================================================================

package handlers

import (
	"log/slog"
	"net/http"
	"strconv"

	"github.com/wcp360/wcp360/internal/database/queries"
)

func (h *Handlers) GetAuditLog(w http.ResponseWriter, r *http.Request) {
	limit := 50
	if v := r.URL.Query().Get("limit"); v != "" {
		if n, err := strconv.Atoi(v); err == nil && n >= 1 && n <= 500 {
			limit = n
		}
	}
	entries, err := queries.GetAuditLog(r.Context(), h.db.DB, limit)
	if err != nil {
		slog.Error("GetAuditLog: query", "err", err)
		writeError(w, http.StatusInternalServerError, "failed to fetch audit log")
		return
	}
	if entries == nil { entries = []queries.AuditEntry{} }
	writeJSON(w, http.StatusOK, map[string]any{
		"data": entries, "total": len(entries), "limit": limit,
	})
}
