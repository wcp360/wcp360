// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/handlers/tenant.go
// Description: Tenant JSON API CRUD handlers with pagination.
// ======================================================================

package handlers

import (
	"errors"
	"fmt"
	"log/slog"
	"net/http"

	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/models"
)

func (h *Handlers) ListTenants(w http.ResponseWriter, r *http.Request) {
	page, perPage := parsePaginationParams(r)
	total, err := queries.CountTenants(r.Context(), h.db.DB)
	if err != nil {
		slog.Error("ListTenants: count", "err", err)
		writeError(w, http.StatusInternalServerError, "failed to count tenants")
		return
	}
	tenants, err := queries.ListTenantsPaginated(r.Context(), h.db.DB, page, perPage)
	if err != nil {
		slog.Error("ListTenants: query", "err", err)
		writeError(w, http.StatusInternalServerError, "failed to list tenants")
		return
	}
	resp := make([]models.TenantResponse, len(tenants))
	for i, t := range tenants { resp[i] = t.ToResponse() }
	writePaginated(w, resp, NewPagination(page, perPage, total))
}

func (h *Handlers) CreateTenant(w http.ResponseWriter, r *http.Request) {
	var req models.CreateTenantRequest
	if !decodeJSON(w, r, &req) { return }
	if err := req.Validate(); err != nil {
		writeError(w, http.StatusBadRequest, err.Error())
		return
	}
	exists, err := queries.TenantUsernameExists(r.Context(), h.db.DB, req.Username)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "internal server error")
		return
	}
	if exists {
		writeError(w, http.StatusConflict, fmt.Sprintf("username %q already exists", req.Username))
		return
	}
	id, err := queries.CreateTenant(r.Context(), h.db.DB, &req, h.cfg.DataDir)
	if err != nil {
		slog.Error("CreateTenant", "err", err)
		writeError(w, http.StatusInternalServerError, "failed to create tenant")
		return
	}
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "tenant created but could not be fetched")
		return
	}
	actor := actorFromContext(r)
	queries.LogAction(r.Context(), h.db.DB, actor, queries.ActionTenantCreate, req.Username,
		fmt.Sprintf(`{"plan":"%s","disk_mb":%d}`, req.Plan, req.DiskQuotaMB), r.RemoteAddr)
	slog.Info("tenant created", "id", id, "username", req.Username, "actor", actor)
	writeJSON(w, http.StatusCreated, tenant.ToResponse())
}

func (h *Handlers) GetTenant(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { return }
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) {
			writeError(w, http.StatusNotFound, "tenant not found")
			return
		}
		writeError(w, http.StatusInternalServerError, "failed to get tenant")
		return
	}
	writeJSON(w, http.StatusOK, tenant.ToResponse())
}

func (h *Handlers) UpdateTenant(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { return }
	var req models.UpdateTenantRequest
	if !decodeJSON(w, r, &req) { return }
	if err := req.Validate(); err != nil {
		writeError(w, http.StatusBadRequest, err.Error())
		return
	}
	if err := queries.UpdateTenant(r.Context(), h.db.DB, id, &req); err != nil {
		if errors.Is(err, queries.ErrNotFound) {
			writeError(w, http.StatusNotFound, "tenant not found")
			return
		}
		writeError(w, http.StatusInternalServerError, "failed to update tenant")
		return
	}
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil {
		writeError(w, http.StatusInternalServerError, "tenant updated but could not be fetched")
		return
	}
	actor := actorFromContext(r)
	queries.LogAction(r.Context(), h.db.DB, actor, queries.ActionTenantUpdate, tenant.Username,
		fmt.Sprintf(`{"status":"%s","plan":"%s"}`, req.Status, req.Plan), r.RemoteAddr)
	writeJSON(w, http.StatusOK, tenant.ToResponse())
}

func (h *Handlers) DeleteTenant(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { return }
	tenant, err := queries.GetTenantByID(r.Context(), h.db.DB, id)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) {
			writeError(w, http.StatusNotFound, "tenant not found")
			return
		}
		writeError(w, http.StatusInternalServerError, "internal server error")
		return
	}
	if err := queries.SoftDeleteTenant(r.Context(), h.db.DB, id); err != nil {
		if errors.Is(err, queries.ErrNotFound) {
			writeError(w, http.StatusNotFound, "tenant not found")
			return
		}
		writeError(w, http.StatusInternalServerError, "failed to delete tenant")
		return
	}
	actor := actorFromContext(r)
	queries.LogAction(r.Context(), h.db.DB, actor, queries.ActionTenantDelete, tenant.Username,
		`{"type":"soft_delete"}`, r.RemoteAddr)
	writeJSON(w, http.StatusOK, map[string]string{"message": "tenant deleted", "username": tenant.Username})
}
