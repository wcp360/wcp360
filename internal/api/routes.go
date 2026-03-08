// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/api/routes.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/routes.go
// Description: Route registration — web UI (/admin/*) + JSON API (/api/v1/*).
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package api

import (
	"net/http"

	"github.com/wcp360/wcp360/internal/api/handlers"
	"github.com/wcp360/wcp360/internal/api/middleware"
	"github.com/wcp360/wcp360/internal/auth"
<<<<<<< HEAD
	"github.com/wcp360/wcp360/internal/cache"
	"github.com/wcp360/wcp360/internal/services"
)

func (s *Server) registerRoutes(mux *http.ServeMux, mailer services.Mailer, redisClient *cache.Client) {
	h   := handlers.New(s.cfg, s.db, mailer, redisClient)
=======
)

func (s *Server) registerRoutes(mux *http.ServeMux) {
	h   := handlers.New(s.cfg, s.db)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	web := handlers.NewDashboard(h)

	withLog  := middleware.Logging
	withAuth := func(next http.Handler) http.Handler { return middleware.RequireAuth(s.cfg.JWTSecret, s.db.DB)(next) }
	withRoot := func(next http.Handler) http.Handler { return middleware.RequireRole(s.cfg.JWTSecret, s.db.DB, auth.RoleRoot)(next) }
	withWeb  := func(next http.Handler) http.Handler { return middleware.RequireWebAuth(s.cfg.JWTSecret, s.db.DB)(next) }
<<<<<<< HEAD
	withRL   := func(next http.Handler) http.Handler { return s.loginRL.Limit(next) }

	chain := func(h http.Handler, mws ...func(http.Handler) http.Handler) http.Handler {
		for i := len(mws) - 1; i >= 0; i-- { h = mws[i](h) }
		return h
=======

	chain := func(handler http.Handler, mws ...func(http.Handler) http.Handler) http.Handler {
		for i := len(mws) - 1; i >= 0; i-- { handler = mws[i](handler) }
		return handler
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	}

	// System
	mux.Handle("GET /healthz", chain(http.HandlerFunc(s.handleHealth), withLog))
	mux.Handle("GET /",        chain(http.HandlerFunc(s.handleRoot),   withLog))

	// Web UI — public
<<<<<<< HEAD
	mux.Handle("GET /admin/login",   chain(http.HandlerFunc(web.ShowLogin),    withLog))
	mux.Handle("POST /admin/login",  chain(http.HandlerFunc(web.ProcessLogin), withRL, withLog))
	mux.Handle("POST /admin/logout", chain(http.HandlerFunc(web.ProcessLogout), withLog))

	// Web UI — protected
	mux.Handle("GET /admin/",                          chain(http.HandlerFunc(web.ShowDashboard),      withWeb, withLog))
	mux.Handle("GET /admin/tenants",                   chain(http.HandlerFunc(web.ShowTenants),        withWeb, withLog))
	mux.Handle("POST /admin/tenants",                  chain(http.HandlerFunc(web.CreateTenantWeb),    withWeb, withLog))
	mux.Handle("GET /admin/tenants/{id}",              chain(http.HandlerFunc(web.ShowTenantDetail),   withWeb, withLog))
	mux.Handle("POST /admin/tenants/{id}/update",      chain(http.HandlerFunc(web.UpdateTenantWeb),    withWeb, withLog))
	mux.Handle("POST /admin/tenants/{id}/delete",      chain(http.HandlerFunc(web.DeleteTenantWeb),    withWeb, withLog))
	mux.Handle("POST /admin/tenants/{id}/toggle-status", chain(http.HandlerFunc(web.ToggleTenantStatusWeb), withWeb, withLog))
	mux.Handle("GET /admin/audit",                     chain(http.HandlerFunc(web.ShowAudit),          withWeb, withLog))

	// JSON API — Auth
	mux.Handle("POST /api/v1/auth/login",  chain(http.HandlerFunc(h.Login),  withRL, withLog))
=======
	mux.Handle("GET /admin/login",  chain(http.HandlerFunc(web.ShowLogin),     withLog))
	mux.Handle("POST /admin/login",  chain(http.HandlerFunc(web.ProcessLogin),  withLog))
	mux.Handle("POST /admin/logout", chain(http.HandlerFunc(web.ProcessLogout), withLog))

	// Web UI — protected
	mux.Handle("GET /admin/",                    chain(http.HandlerFunc(web.ShowDashboard),  withWeb, withLog))
	mux.Handle("GET /admin/tenants",             chain(http.HandlerFunc(web.ShowTenants),    withWeb, withLog))
	mux.Handle("POST /admin/tenants",            chain(http.HandlerFunc(web.CreateTenantWeb), withWeb, withLog))
	mux.Handle("POST /admin/tenants/{id}/delete", chain(http.HandlerFunc(web.DeleteTenantWeb), withWeb, withLog))
	mux.Handle("GET /admin/audit",               chain(http.HandlerFunc(web.ShowAudit),      withWeb, withLog))

	// JSON API — Auth
	mux.Handle("POST /api/v1/auth/login",  chain(http.HandlerFunc(h.Login),  withLog))
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	mux.Handle("POST /api/v1/auth/logout", chain(http.HandlerFunc(h.Logout), withAuth, withLog))
	mux.Handle("GET /api/v1/auth/me",      chain(http.HandlerFunc(h.Me),     withAuth, withLog))

	// JSON API — Tenants
<<<<<<< HEAD
	mux.Handle("GET /api/v1/tenants",         chain(http.HandlerFunc(h.ListTenants),  withRoot, withLog))
	mux.Handle("POST /api/v1/tenants",        chain(http.HandlerFunc(h.CreateTenant), withRoot, withLog))
	mux.Handle("GET /api/v1/tenants/{id}",    chain(http.HandlerFunc(h.GetTenant),    withRoot, withLog))
	mux.Handle("PATCH /api/v1/tenants/{id}",  chain(http.HandlerFunc(h.UpdateTenant), withRoot, withLog))
	mux.Handle("DELETE /api/v1/tenants/{id}", chain(http.HandlerFunc(h.DeleteTenant), withRoot, withLog))
	mux.Handle("GET /api/v1/tenants/{id}/audit",  chain(http.HandlerFunc(h.GetTenantAuditLog), withRoot, withLog))
	mux.Handle("POST /api/v1/tenants/{id}/invite", chain(http.HandlerFunc(h.InviteTenant),     withRoot, withLog))
=======
	mux.Handle("GET /api/v1/tenants",          chain(http.HandlerFunc(h.ListTenants),  withRoot, withLog))
	mux.Handle("POST /api/v1/tenants",         chain(http.HandlerFunc(h.CreateTenant), withRoot, withLog))
	mux.Handle("GET /api/v1/tenants/{id}",     chain(http.HandlerFunc(h.GetTenant),    withRoot, withLog))
	mux.Handle("PATCH /api/v1/tenants/{id}",   chain(http.HandlerFunc(h.UpdateTenant), withRoot, withLog))
	mux.Handle("DELETE /api/v1/tenants/{id}",  chain(http.HandlerFunc(h.DeleteTenant), withRoot, withLog))
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee

	// JSON API — Audit
	mux.Handle("GET /api/v1/audit", chain(http.HandlerFunc(h.GetAuditLog), withRoot, withLog))
}
