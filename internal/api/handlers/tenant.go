// ======================================================================
// WCP 360 | V0.1.0 | internal/api/handlers/tenant.go
// ======================================================================

package handlers

import (
	"errors"
	"fmt"
	"log/slog"
	"net/http"

	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/models"
	"github.com/wcp360/wcp360/internal/services"
)

func (h *Handlers) ListTenants(w http.ResponseWriter, r *http.Request) {
	page, perPage := parsePaginationParams(r)
	filter := parseFilterParams(r)
	total, err := queries.CountTenantsFiltered(r.Context(), h.db.DB, filter)
	if err != nil { writeError(w, http.StatusInternalServerError, "failed to count tenants"); return }
	tenants, err := queries.ListTenantsPaginatedFiltered(r.Context(), h.db.DB, page, perPage, filter)
	if err != nil { writeError(w, http.StatusInternalServerError, "failed to list tenants"); return }
	resp := make([]models.TenantResponse, len(tenants))
	for i, t := range tenants { resp[i] = t.ToResponse() }
	pag := NewPagination(page, perPage, total)
	writeJSON(w, http.StatusOK, map[string]any{
		"data": resp, "pagination": pag,
		"filter": map[string]string{"search": filter.Search, "status": filter.Status, "plan": filter.Plan},
	})
}

func (h *Handlers) CreateTenant(w http.ResponseWriter, r *http.Request) {
	var req models.CreateTenantRequest
	if !decodeJSON(w, r, &req) { return }
	if err := req.Validate(); err != nil { writeError(w, http.StatusBadRequest, err.Error()); return }
	exists, err := queries.TenantUsernameExists(r.Context(), h.db.DB, req.Username)
	if err != nil { writeError(w, http.StatusInternalServerError, "internal server error"); return }
	if exists { writeError(w, http.StatusConflict, fmt.Sprintf("username %q already exists", req.Username)); return }
	id, err := queries.CreateTenant(r.Context(), h.db.DB, &req, h.cfg.DataDir)
	if err != nil { slog.Error("CreateTenant", "err", err); writeError(w, http.StatusInternalServerError, "failed to create tenant"); return }
	if _, err := services.ProvisionTenant(h.cfg.DataDir, req.Username); err != nil {
		slog.Error("CreateTenant: provision", "username", req.Username, "err", err)
	}
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil { writeError(w, http.StatusInternalServerError, "tenant created but fetch failed"); return }
	actor := actorFromContext(r)
	queries.LogAction(r.Context(), h.db.DB, actor, queries.ActionTenantCreate, req.Username,
		fmt.Sprintf(`{"plan":%q}`, req.Plan), r.RemoteAddr)
	slog.Info("tenant created", "id", id, "username", req.Username)
	writeJSON(w, http.StatusCreated, tenant.ToResponse())
}

func (h *Handlers) GetTenant(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { return }
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) { writeError(w, http.StatusNotFound, "tenant not found"); return }
		writeError(w, http.StatusInternalServerError, "internal server error"); return
	}
	writeJSON(w, http.StatusOK, tenant.ToResponse())
}

func (h *Handlers) UpdateTenant(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { return }
	var req models.UpdateTenantRequest
	if !decodeJSON(w, r, &req) { return }
	if err := req.Validate(); err != nil { writeError(w, http.StatusBadRequest, err.Error()); return }
	if err := queries.UpdateTenant(r.Context(), h.db.DB, id, &req); err != nil {
		if errors.Is(err, queries.ErrNotFound) { writeError(w, http.StatusNotFound, "tenant not found"); return }
		writeError(w, http.StatusInternalServerError, "failed to update tenant"); return
	}
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil { writeError(w, http.StatusInternalServerError, "updated but fetch failed"); return }
	queries.LogAction(r.Context(), h.db.DB, actorFromContext(r), queries.ActionTenantUpdate,
		tenant.Username, fmt.Sprintf(`{"status":%q,"plan":%q}`, req.Status, req.Plan), r.RemoteAddr)
	writeJSON(w, http.StatusOK, tenant.ToResponse())
}

func (h *Handlers) DeleteTenant(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { return }
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) { writeError(w, http.StatusNotFound, "tenant not found"); return }
		writeError(w, http.StatusInternalServerError, "internal server error"); return
	}
	if err := queries.SoftDeleteTenant(r.Context(), h.db.DB, id); err != nil {
		if errors.Is(err, queries.ErrNotFound) { writeError(w, http.StatusNotFound, "tenant not found"); return }
		writeError(w, http.StatusInternalServerError, "failed to delete tenant"); return
	}
	purge := r.URL.Query().Get("purge") == "true"
	if purge {
		if err := services.DeprovisionTenant(h.cfg.DataDir, tenant.Username); err != nil {
			slog.Error("DeleteTenant: deprovision", "username", tenant.Username, "err", err)
		}
	}
	detail := `{"type":"soft_delete"}`
	if purge { detail = `{"type":"hard_delete","purge":true}` }
	queries.LogAction(r.Context(), h.db.DB, actorFromContext(r), queries.ActionTenantDelete,
		tenant.Username, detail, r.RemoteAddr)
	writeJSON(w, http.StatusOK, map[string]any{
		"message": "tenant deleted", "username": tenant.Username, "purged": purge,
	})
}

func (h *Handlers) InviteTenant(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { return }
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) { writeError(w, http.StatusNotFound, "tenant not found"); return }
		writeError(w, http.StatusInternalServerError, "internal server error"); return
	}
	if tenant.Email == "" { writeError(w, http.StatusBadRequest, "tenant has no email address"); return }
	if err := services.SendTenantInvite(h.mailer, h.cfg.Domain, tenant.Email, tenant.Username); err != nil {
		slog.Error("InviteTenant", "username", tenant.Username, "err", err)
		writeError(w, http.StatusInternalServerError, "failed to send invite"); return
	}
	queries.LogAction(r.Context(), h.db.DB, actorFromContext(r), "tenant.invite",
		tenant.Username, fmt.Sprintf(`{"email":%q}`, tenant.Email), r.RemoteAddr)
	writeJSON(w, http.StatusOK, map[string]string{
		"message": "invite sent", "username": tenant.Username, "email": tenant.Email,
	})
}
