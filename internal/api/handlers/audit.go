// ======================================================================
// WCP 360 | V0.1.0 | internal/api/handlers/audit.go
// ======================================================================

package handlers

import (
	"errors"
	"log/slog"
	"net/http"

	"github.com/wcp360/wcp360/internal/database/queries"
)

func (h *Handlers) GetAuditLog(w http.ResponseWriter, r *http.Request) {
	if r.URL.Query().Has("page") {
		page, perPage := parsePaginationParams(r)
		ap, err := queries.GetAuditLogPaginated(r.Context(), h.db.DB, page, perPage)
		if err != nil { slog.Error("GetAuditLog paginated", "err", err); writeError(w, 500, "failed to fetch audit log"); return }
		if ap.Entries == nil { ap.Entries = []queries.AuditEntry{} }
		writeJSON(w, 200, map[string]any{"data": ap.Entries, "pagination": NewPagination(page, perPage, ap.Total)})
		return
	}
	limit := parseLimit(r, 50, 500)
	entries, err := queries.GetAuditLog(r.Context(), h.db.DB, limit)
	if err != nil { writeError(w, 500, "failed to fetch audit log"); return }
	if entries == nil { entries = []queries.AuditEntry{} }
	writeJSON(w, 200, map[string]any{"data": entries, "total": len(entries), "limit": limit})
}

func (h *Handlers) GetTenantAuditLog(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { return }
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) { writeError(w, 404, "tenant not found"); return }
		writeError(w, 500, "internal server error"); return
	}
	limit := parseLimit(r, 50, 500)
	entries, err := queries.GetAuditLogByTarget(r.Context(), h.db.DB, tenant.Username, limit)
	if err != nil { writeError(w, 500, "failed to fetch audit log"); return }
	if entries == nil { entries = []queries.AuditEntry{} }
	writeJSON(w, 200, map[string]any{
		"tenant_id": tenant.ID, "tenant_username": tenant.Username,
		"data": entries, "total": len(entries), "limit": limit,
	})
}
