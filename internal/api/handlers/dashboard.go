// ======================================================================
// WCP 360 | V0.1.0 | internal/api/handlers/dashboard.go
// ======================================================================

package handlers

import (
	"errors"
	"fmt"
	"log/slog"
	"net/http"
	"net/url"
	"strconv"

	"github.com/wcp360/wcp360/internal/api/middleware"
	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/models"
	"github.com/wcp360/wcp360/internal/services"
	"github.com/wcp360/wcp360/internal/web"
)

// ── Page data types ───────────────────────────────────────────────────────

type baseData struct{ Admin, Page string }
type loginPageData struct{ Error, Username string }
type dashboardPageData struct{ baseData; Stats *queries.DashboardStats }
type flashMsg struct{ Success, Error string }
type tenantPagination struct{ Page, PerPage, Total, TotalPages int }
type filterData struct{ Search, Status, Plan string }
type tenantsPageData struct {
	baseData
	Tenants    []models.TenantResponse
	Pagination tenantPagination
	From, To   int
	Flash      flashMsg
	Filter     filterData
}
type tenantDetailPageData struct {
	baseData
	Tenant       models.TenantResponse
	AuditEntries []queries.AuditEntry
	AuditLimit   int
	Flash        flashMsg
}
type auditPageData struct {
	baseData
	Entries []queries.AuditEntry
	Limit   int
}

// ── Constructor ───────────────────────────────────────────────────────────

type DashboardHandlers struct {
	*Handlers
	renderer *web.Renderer
}

func NewDashboard(h *Handlers) *DashboardHandlers {
	return &DashboardHandlers{Handlers: h, renderer: web.NewRenderer()}
}

func adminFromCtx(r *http.Request) string {
	if claims := middleware.ClaimsFromContext(r.Context()); claims != nil {
		return claims.Username
	}
	return "?"
}

// ── Login ─────────────────────────────────────────────────────────────────

func (d *DashboardHandlers) ShowLogin(w http.ResponseWriter, r *http.Request) {
	if cookie, err := r.Cookie(middleware.SessionCookieName); err == nil {
		if _, err := services.ValidateWebSession(cookie.Value, d.cfg.JWTSecret, d.db.DB, r.Context()); err == nil {
			http.Redirect(w, r, "/admin/", http.StatusSeeOther)
			return
		}
	}
	d.renderer.RenderLogin(w, http.StatusOK, loginPageData{})
}

func (d *DashboardHandlers) ProcessLogin(w http.ResponseWriter, r *http.Request) {
	if err := r.ParseForm(); err != nil {
		d.renderer.RenderLogin(w, http.StatusBadRequest, loginPageData{Error: "Invalid form"})
		return
	}
	username, password := r.FormValue("username"), r.FormValue("password")
	result, err := services.LoginAdmin(r.Context(), d.db.DB, d.cfg, username, password)
	if err != nil {
		if errors.Is(err, services.ErrInvalidCredentials) {
			slog.Info("web: login failed", "username", username)
			d.renderer.RenderLogin(w, http.StatusUnauthorized, loginPageData{
				Error: "Invalid username or password", Username: username,
			})
			return
		}
		d.renderer.RenderLogin(w, http.StatusInternalServerError, loginPageData{Error: "Internal server error"})
		return
	}
	middleware.SetSessionCookie(w, result.Token, result.ExpiresAt, d.cfg.IsProd())
	queries.LogAction(r.Context(), d.db.DB, result.Admin.Username, queries.ActionAdminLogin, "", "", r.RemoteAddr)
	slog.Info("web: admin login", "username", result.Admin.Username)
	http.Redirect(w, r, "/admin/", http.StatusSeeOther)
}

func (d *DashboardHandlers) ProcessLogout(w http.ResponseWriter, r *http.Request) {
	if cookie, err := r.Cookie(middleware.SessionCookieName); err == nil {
		if claims, err := services.ValidateWebSession(cookie.Value, d.cfg.JWTSecret, d.db.DB, r.Context()); err == nil {
			queries.InvalidateSession(r.Context(), d.db.DB, claims.ID)
			queries.LogAction(r.Context(), d.db.DB, claims.Username, queries.ActionAdminLogout, "", "", r.RemoteAddr)
		}
	}
	middleware.ClearSessionCookie(w)
	http.Redirect(w, r, "/admin/login", http.StatusSeeOther)
}

// ── Dashboard ─────────────────────────────────────────────────────────────

func (d *DashboardHandlers) ShowDashboard(w http.ResponseWriter, r *http.Request) {
	stats, err := queries.GetDashboardStats(r.Context(), d.db.DB)
	if err != nil {
		slog.Error("web: dashboard stats", "err", err)
		stats = &queries.DashboardStats{}
	}
	d.renderer.Render(w, http.StatusOK, "dashboard", dashboardPageData{
		baseData: baseData{Admin: adminFromCtx(r), Page: "dashboard"},
		Stats:    stats,
	})
}

// ── Tenants list ──────────────────────────────────────────────────────────

func (d *DashboardHandlers) ShowTenants(w http.ResponseWriter, r *http.Request) {
	page, perPage := parsePaginationParams(r)
	filter := parseFilterParams(r)
	total, _ := queries.CountTenantsFiltered(r.Context(), d.db.DB, filter)
	tenants, _ := queries.ListTenantsPaginatedFiltered(r.Context(), d.db.DB, page, perPage, filter)
	resp := make([]models.TenantResponse, len(tenants))
	for i, t := range tenants { resp[i] = t.ToResponse() }
	pag := NewPagination(page, perPage, total)
	from, to := (page-1)*perPage+1, (page-1)*perPage+len(resp)
	if total == 0 { from, to = 0, 0 }
	d.renderer.Render(w, http.StatusOK, "tenants", tenantsPageData{
		baseData:   baseData{Admin: adminFromCtx(r), Page: "tenants"},
		Tenants:    resp,
		Pagination: tenantPagination{pag.Page, pag.PerPage, pag.Total, pag.TotalPages},
		From: from, To: to,
		Flash:  flashMsg{r.URL.Query().Get("success"), r.URL.Query().Get("error")},
		Filter: filterData{filter.Search, filter.Status, filter.Plan},
	})
}

func (d *DashboardHandlers) CreateTenantWeb(w http.ResponseWriter, r *http.Request) {
	if err := r.ParseForm(); err != nil {
		http.Redirect(w, r, "/admin/tenants?error=Invalid+form", http.StatusSeeOther)
		return
	}
	diskMB, _   := strconv.Atoi(r.FormValue("disk_quota_mb"))
	bwMB, _     := strconv.Atoi(r.FormValue("bandwidth_mb"))
	maxSites, _ := strconv.Atoi(r.FormValue("max_sites"))
	req := models.CreateTenantRequest{
		Username: r.FormValue("username"), Email: r.FormValue("email"), Plan: r.FormValue("plan"),
		DiskQuotaMB: diskMB, BandwidthMB: bwMB, MaxSites: maxSites,
	}
	if err := req.Validate(); err != nil {
		http.Redirect(w, r, "/admin/tenants?error="+url.QueryEscape(err.Error()), http.StatusSeeOther)
		return
	}
	exists, _ := queries.TenantUsernameExists(r.Context(), d.db.DB, req.Username)
	if exists {
		http.Redirect(w, r, "/admin/tenants?error="+url.QueryEscape(fmt.Sprintf("username %q exists", req.Username)), http.StatusSeeOther)
		return
	}
	id, err := queries.CreateTenant(r.Context(), d.db.DB, &req, d.cfg.DataDir)
	if err != nil {
		http.Redirect(w, r, "/admin/tenants?error=Failed+to+create+tenant", http.StatusSeeOther)
		return
	}
	services.ProvisionTenant(d.cfg.DataDir, req.Username)
	queries.LogAction(r.Context(), d.db.DB, adminFromCtx(r), queries.ActionTenantCreate, req.Username, fmt.Sprintf(`{"plan":%q}`, req.Plan), r.RemoteAddr)
	slog.Info("web: tenant created", "id", id, "username", req.Username)
	http.Redirect(w, r, "/admin/tenants?success="+url.QueryEscape("Tenant "+req.Username+" created"), http.StatusSeeOther)
}

func (d *DashboardHandlers) DeleteTenantWeb(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { http.Redirect(w, r, "/admin/tenants?error=Invalid+ID", http.StatusSeeOther); return }
	tenant, err := queries.GetTenantByID(r.Context(), d.db.DB, id)
	if err != nil { http.Redirect(w, r, "/admin/tenants?error=Tenant+not+found", http.StatusSeeOther); return }
	if err := queries.SoftDeleteTenant(r.Context(), d.db.DB, id); err != nil {
		http.Redirect(w, r, "/admin/tenants?error=Failed+to+delete", http.StatusSeeOther)
		return
	}
	queries.LogAction(r.Context(), d.db.DB, adminFromCtx(r), queries.ActionTenantDelete, tenant.Username, `{"type":"soft_delete"}`, r.RemoteAddr)
	http.Redirect(w, r, "/admin/tenants?success="+url.QueryEscape("Tenant "+tenant.Username+" deleted"), http.StatusSeeOther)
}

// ── Tenant detail ─────────────────────────────────────────────────────────

func (d *DashboardHandlers) ShowTenantDetail(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { http.Redirect(w, r, "/admin/tenants?error=Invalid+ID", http.StatusSeeOther); return }
	tenant, err := queries.GetTenantByID(r.Context(), d.db.DB, id)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) { http.Redirect(w, r, "/admin/tenants?error=Tenant+not+found", http.StatusSeeOther); return }
		http.Redirect(w, r, "/admin/tenants?error=Internal+error", http.StatusSeeOther); return
	}
	al := parseAuditLimit(r)
	entries, _ := queries.GetAuditLogByTarget(r.Context(), d.db.DB, tenant.Username, al)
	d.renderer.Render(w, http.StatusOK, "tenant_detail", tenantDetailPageData{
		baseData:     baseData{Admin: adminFromCtx(r), Page: "tenants"},
		Tenant:       tenant.ToResponse(),
		AuditEntries: entries,
		AuditLimit:   al,
		Flash:        flashMsg{r.URL.Query().Get("success"), r.URL.Query().Get("error")},
	})
}

func (d *DashboardHandlers) UpdateTenantWeb(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok { http.Redirect(w, r, "/admin/tenants?error=Invalid+ID", http.StatusSeeOther); return }
	if err := r.ParseForm(); err != nil {
		http.Redirect(w, r, fmt.Sprintf("/admin/tenants/%d?error=Invalid+form", id), http.StatusSeeOther)
		return
	}
	diskMB, _   := strconv.Atoi(r.FormValue("disk_quota_mb"))
	bwMB, _     := strconv.Atoi(r.FormValue("bandwidth_mb"))
	maxSites, _ := strconv.Atoi(r.FormValue("max_sites"))
	req := models.UpdateTenantRequest{
		Email: r.FormValue("email"), Plan: r.FormValue("plan"), Status: r.FormValue("status"),
		DiskQuotaMB: diskMB, BandwidthMB: bwMB, MaxSites: maxSites,
	}
	if err := req.Validate(); err != nil {
		http.Redirect(w, r, fmt.Sprintf("/admin/tenants/%d?error=%s", id, url.QueryEscape(err.Error())), http.StatusSeeOther)
		return
	}
	if err := queries.UpdateTenant(r.Context(), d.db.DB, id, &req); err != nil {
		if errors.Is(err, queries.ErrNotFound) { http.Redirect(w, r, "/admin/tenants?error=Not+found", http.StatusSeeOther); return }
		http.Redirect(w, r, fmt.Sprintf("/admin/tenants/%d?error=Update+failed", id), http.StatusSeeOther); return
	}
	tenant, _ := queries.GetTenantByID(r.Context(), d.db.DB, id)
	if tenant != nil {
		queries.LogAction(r.Context(), d.db.DB, adminFromCtx(r), queries.ActionTenantUpdate, tenant.Username, fmt.Sprintf(`{"status":%q,"plan":%q}`, req.Status, req.Plan), r.RemoteAddr)
	}
	http.Redirect(w, r, fmt.Sprintf("/admin/tenants/%d?success=Tenant+updated+successfully", id), http.StatusSeeOther)
}

// ── Toggle status (HTMX) ──────────────────────────────────────────────────

func (d *DashboardHandlers) ToggleTenantStatusWeb(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
	if !ok {
		if isHTMX(r) { http.Error(w, "invalid ID", http.StatusBadRequest); return }
		http.Redirect(w, r, "/admin/tenants?error=Invalid+ID", http.StatusSeeOther); return
	}
	newStatus, err := queries.ToggleTenantStatus(r.Context(), d.db.DB, id)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) {
			if isHTMX(r) { http.Error(w, "not found", http.StatusNotFound); return }
			http.Redirect(w, r, "/admin/tenants?error=Tenant+not+found", http.StatusSeeOther); return
		}
		if isHTMX(r) { http.Error(w, "error", http.StatusInternalServerError); return }
		http.Redirect(w, r, fmt.Sprintf("/admin/tenants/%d?error=Toggle+failed", id), http.StatusSeeOther); return
	}
	tenant, _ := queries.GetTenantByID(r.Context(), d.db.DB, id)
	username := "unknown"
	if tenant != nil { username = tenant.Username }
	action := queries.ActionTenantSuspend
	if newStatus == "active" { action = queries.ActionTenantUpdate }
	queries.LogAction(r.Context(), d.db.DB, adminFromCtx(r), action, username, fmt.Sprintf(`{"new_status":%q}`, newStatus), r.RemoteAddr)
	slog.Info("web: tenant status toggled", "id", id, "username", username, "new_status", newStatus)
	if isHTMX(r) {
		w.Header().Set("Content-Type", "text/html; charset=utf-8")
		fmt.Fprintf(w, `<span id="status-badge" class="badge badge-%s">%s</span>`, newStatus, newStatus)
		return
	}
	http.Redirect(w, r, fmt.Sprintf("/admin/tenants/%d?success=Status+changed+to+%s", id, newStatus), http.StatusSeeOther)
}

// ── Audit ─────────────────────────────────────────────────────────────────

func (d *DashboardHandlers) ShowAudit(w http.ResponseWriter, r *http.Request) {
	limit := parseLimit(r, 50, 500)
	entries, _ := queries.GetAuditLog(r.Context(), d.db.DB, limit)
	d.renderer.Render(w, http.StatusOK, "audit", auditPageData{
		baseData: baseData{Admin: adminFromCtx(r), Page: "audit"},
		Entries:  entries,
		Limit:    limit,
	})
}

// ── Helpers ───────────────────────────────────────────────────────────────

func isHTMX(r *http.Request) bool { return r.Header.Get("HX-Request") == "true" }

func parseAuditLimit(r *http.Request) int {
	allowed := map[int]bool{10: true, 25: true, 50: true, 100: true}
	v := r.URL.Query().Get("audit_limit")
	if v == "" { return 25 }
	n, err := strconv.Atoi(v)
	if err != nil || !allowed[n] { return 25 }
	return n
}
