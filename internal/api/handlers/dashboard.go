// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/api/handlers/dashboard.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/handlers/dashboard.go
// Description: Web UI handlers — login, dashboard, tenants page, audit page.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package handlers

import (
	"errors"
	"fmt"
	"log/slog"
	"net/http"
<<<<<<< HEAD
	"net/url"
=======
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	"strconv"

	"github.com/wcp360/wcp360/internal/api/middleware"
	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/models"
	"github.com/wcp360/wcp360/internal/services"
	"github.com/wcp360/wcp360/internal/web"
)

<<<<<<< HEAD
// ── Page data types ───────────────────────────────────────────────────────

type baseData struct{ Admin, Page string }
type loginPageData struct{ Error, Username string }
type dashboardPageData struct{ baseData; Stats *queries.DashboardStats }
type flashMsg struct{ Success, Error string }
type tenantPagination struct{ Page, PerPage, Total, TotalPages int }
type filterData struct{ Search, Status, Plan string }
=======
type baseData struct {
	Admin string
	Page  string
}

type loginPageData struct {
	Error    string
	Username string
}

type dashboardPageData struct {
	baseData
	Stats *queries.DashboardStats
}

type flashMsg struct {
	Success string
	Error   string
}

type tenantPagination struct {
	Page       int
	PerPage    int
	Total      int
	TotalPages int
}

>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
type tenantsPageData struct {
	baseData
	Tenants    []models.TenantResponse
	Pagination tenantPagination
<<<<<<< HEAD
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
=======
	From       int
	To         int
	Flash      flashMsg
}

>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
type auditPageData struct {
	baseData
	Entries []queries.AuditEntry
	Limit   int
}

<<<<<<< HEAD
// ── Constructor ───────────────────────────────────────────────────────────

=======
// DashboardHandlers embeds *Handlers and adds the template renderer.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
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

<<<<<<< HEAD
// ── Login ─────────────────────────────────────────────────────────────────
=======
// ── Login ─────────────────────────────────────────────────────────────
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee

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
<<<<<<< HEAD
		d.renderer.RenderLogin(w, http.StatusBadRequest, loginPageData{Error: "Invalid form"})
		return
	}
	username, password := r.FormValue("username"), r.FormValue("password")
	result, err := services.LoginAdmin(r.Context(), d.db.DB, d.cfg, username, password)
	if err != nil {
		if errors.Is(err, services.ErrInvalidCredentials) {
			slog.Info("web: login failed", "username", username)
=======
		d.renderer.RenderLogin(w, http.StatusBadRequest, loginPageData{Error: "Invalid form submission"})
		return
	}
	username := r.FormValue("username")
	password := r.FormValue("password")

	result, err := services.LoginAdmin(r.Context(), d.db.DB, d.cfg, username, password)
	if err != nil {
		if errors.Is(err, services.ErrInvalidCredentials) {
			slog.Info("web: login failed", "username", username, "ip", r.RemoteAddr)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
			d.renderer.RenderLogin(w, http.StatusUnauthorized, loginPageData{
				Error: "Invalid username or password", Username: username,
			})
			return
		}
<<<<<<< HEAD
		d.renderer.RenderLogin(w, http.StatusInternalServerError, loginPageData{Error: "Internal server error"})
		return
	}
	middleware.SetSessionCookie(w, result.Token, result.ExpiresAt, d.cfg.IsProd())
	queries.LogAction(r.Context(), d.db.DB, result.Admin.Username, queries.ActionAdminLogin, "", "", r.RemoteAddr)
	slog.Info("web: admin login", "username", result.Admin.Username)
=======
		slog.Error("web: login error", "err", err)
		d.renderer.RenderLogin(w, http.StatusInternalServerError, loginPageData{
			Error: "Internal server error — please try again",
		})
		return
	}

	middleware.SetSessionCookie(w, result.Token, result.ExpiresAt)
	queries.LogAction(r.Context(), d.db.DB, result.Admin.Username, queries.ActionAdminLogin, "", "", r.RemoteAddr)
	slog.Info("web: admin login", "username", result.Admin.Username, "ip", r.RemoteAddr)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	http.Redirect(w, r, "/admin/", http.StatusSeeOther)
}

func (d *DashboardHandlers) ProcessLogout(w http.ResponseWriter, r *http.Request) {
<<<<<<< HEAD
	if cookie, err := r.Cookie(middleware.SessionCookieName); err == nil {
		if claims, err := services.ValidateWebSession(cookie.Value, d.cfg.JWTSecret, d.db.DB, r.Context()); err == nil {
			queries.InvalidateSession(r.Context(), d.db.DB, claims.ID)
			queries.LogAction(r.Context(), d.db.DB, claims.Username, queries.ActionAdminLogout, "", "", r.RemoteAddr)
=======
	cookie, err := r.Cookie(middleware.SessionCookieName)
	if err == nil {
		if claims, err := services.ValidateWebSession(cookie.Value, d.cfg.JWTSecret, d.db.DB, r.Context()); err == nil {
			if err := queries.InvalidateSession(r.Context(), d.db.DB, claims.ID); err != nil {
				slog.Warn("web: invalidate session", "err", err)
			}
			queries.LogAction(r.Context(), d.db.DB, claims.Username, queries.ActionAdminLogout, "", "", r.RemoteAddr)
			slog.Info("web: admin logout", "username", claims.Username)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
		}
	}
	middleware.ClearSessionCookie(w)
	http.Redirect(w, r, "/admin/login", http.StatusSeeOther)
}

<<<<<<< HEAD
// ── Dashboard ─────────────────────────────────────────────────────────────
=======
// ── Dashboard ─────────────────────────────────────────────────────────
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee

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

<<<<<<< HEAD
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
=======
// ── Tenants ───────────────────────────────────────────────────────────

func (d *DashboardHandlers) ShowTenants(w http.ResponseWriter, r *http.Request) {
	page, perPage := parsePaginationParams(r)
	total, _ := queries.CountTenants(r.Context(), d.db.DB)
	tenants, _ := queries.ListTenantsPaginated(r.Context(), d.db.DB, page, perPage)

	resp := make([]models.TenantResponse, len(tenants))
	for i, t := range tenants { resp[i] = t.ToResponse() }

	pag := NewPagination(page, perPage, total)
	from := (page-1)*perPage + 1
	to := from + len(resp) - 1
	if total == 0 { from, to = 0, 0 }

	d.renderer.Render(w, http.StatusOK, "tenants", tenantsPageData{
		baseData:   baseData{Admin: adminFromCtx(r), Page: "tenants"},
		Tenants:    resp,
		Pagination: tenantPagination{Page: pag.Page, PerPage: pag.PerPage, Total: pag.Total, TotalPages: pag.TotalPages},
		From: from, To: to,
		Flash: flashMsg{
			Success: r.URL.Query().Get("success"),
			Error:   r.URL.Query().Get("error"),
		},
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	})
}

func (d *DashboardHandlers) CreateTenantWeb(w http.ResponseWriter, r *http.Request) {
	if err := r.ParseForm(); err != nil {
<<<<<<< HEAD
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
=======
		http.Redirect(w, r, "/admin/tenants?error=Invalid+form+data", http.StatusSeeOther)
		return
	}
	diskMB, _ := strconv.Atoi(r.FormValue("disk_quota_mb"))
	bwMB, _   := strconv.Atoi(r.FormValue("bandwidth_mb"))
	maxSites, _:= strconv.Atoi(r.FormValue("max_sites"))
	req := models.CreateTenantRequest{
		Username: r.FormValue("username"), Email: r.FormValue("email"),
		Plan: r.FormValue("plan"), DiskQuotaMB: diskMB, BandwidthMB: bwMB, MaxSites: maxSites,
	}
	if err := req.Validate(); err != nil {
		http.Redirect(w, r, "/admin/tenants?error="+urlEncode(err.Error()), http.StatusSeeOther)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
		return
	}
	exists, _ := queries.TenantUsernameExists(r.Context(), d.db.DB, req.Username)
	if exists {
<<<<<<< HEAD
		http.Redirect(w, r, "/admin/tenants?error="+url.QueryEscape(fmt.Sprintf("username %q exists", req.Username)), http.StatusSeeOther)
=======
		http.Redirect(w, r, "/admin/tenants?error="+urlEncode(fmt.Sprintf("username %q already exists", req.Username)), http.StatusSeeOther)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
		return
	}
	id, err := queries.CreateTenant(r.Context(), d.db.DB, &req, d.cfg.DataDir)
	if err != nil {
<<<<<<< HEAD
		http.Redirect(w, r, "/admin/tenants?error=Failed+to+create+tenant", http.StatusSeeOther)
		return
	}
	services.ProvisionTenant(d.cfg.DataDir, req.Username)
	queries.LogAction(r.Context(), d.db.DB, adminFromCtx(r), queries.ActionTenantCreate, req.Username, fmt.Sprintf(`{"plan":%q}`, req.Plan), r.RemoteAddr)
	slog.Info("web: tenant created", "id", id, "username", req.Username)
	http.Redirect(w, r, "/admin/tenants?success="+url.QueryEscape("Tenant "+req.Username+" created"), http.StatusSeeOther)
=======
		slog.Error("web: create tenant", "err", err)
		http.Redirect(w, r, "/admin/tenants?error=Failed+to+create+tenant", http.StatusSeeOther)
		return
	}
	actor := adminFromCtx(r)
	queries.LogAction(r.Context(), d.db.DB, actor, queries.ActionTenantCreate, req.Username,
		fmt.Sprintf(`{"plan":"%s"}`, req.Plan), r.RemoteAddr)
	slog.Info("web: tenant created", "id", id, "username", req.Username)
	http.Redirect(w, r, "/admin/tenants?success=Tenant+"+req.Username+"+created+successfully", http.StatusSeeOther)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}

func (d *DashboardHandlers) DeleteTenantWeb(w http.ResponseWriter, r *http.Request) {
	id, ok := parseIDParam(w, r, "id")
<<<<<<< HEAD
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
=======
	if !ok {
		http.Redirect(w, r, "/admin/tenants?error=Invalid+tenant+ID", http.StatusSeeOther)
		return
	}
	tenant, err := queries.GetTenantByID(r.Context(), d.db.DB, id)
	if err != nil {
		http.Redirect(w, r, "/admin/tenants?error=Tenant+not+found", http.StatusSeeOther)
		return
	}
	if err := queries.SoftDeleteTenant(r.Context(), d.db.DB, id); err != nil {
		http.Redirect(w, r, "/admin/tenants?error=Failed+to+delete+tenant", http.StatusSeeOther)
		return
	}
	actor := adminFromCtx(r)
	queries.LogAction(r.Context(), d.db.DB, actor, queries.ActionTenantDelete, tenant.Username, `{"type":"soft_delete"}`, r.RemoteAddr)
	http.Redirect(w, r, "/admin/tenants?success=Tenant+"+tenant.Username+"+deleted", http.StatusSeeOther)
}

// ── Audit ─────────────────────────────────────────────────────────────

func (d *DashboardHandlers) ShowAudit(w http.ResponseWriter, r *http.Request) {
	limit := 50
	if v := r.URL.Query().Get("limit"); v != "" {
		if n, err := strconv.Atoi(v); err == nil && n >= 1 && n <= 500 { limit = n }
	}
	entries, _ := queries.GetAuditLog(r.Context(), d.db.DB, limit)
	d.renderer.Render(w, http.StatusOK, "audit", auditPageData{
		baseData: baseData{Admin: adminFromCtx(r), Page: "audit"},
		Entries: entries, Limit: limit,
	})
}

// ── Utilities ─────────────────────────────────────────────────────────

func urlEncode(s string) string {
	var out []byte
	for i := 0; i < len(s); i++ {
		c := s[i]
		if (c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z') || (c >= '0' && c <= '9') || c == '-' || c == '_' || c == '.' {
			out = append(out, c)
		} else if c == ' ' {
			out = append(out, '+')
		} else {
			out = append(out, '%', hexChar(c>>4), hexChar(c&0xf))
		}
	}
	return string(out)
}

func hexChar(n byte) byte {
	if n < 10 { return '0' + n }
	return 'a' + n - 10
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}
