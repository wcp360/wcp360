// ======================================================================
// WCP 360 | V0.1.0 | internal/api/handlers/dashboard_test.go
// Description: Web UI integration tests — 13 cases.
// ======================================================================

package handlers_test

import (
	"context"
	"fmt"
	"net/http"
	"net/http/httptest"
	"net/url"
	"os"
	"strings"
	"testing"

	"github.com/wcp360/wcp360/internal/api"
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database"
	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/models"
)

func newWebServer(t *testing.T) (http.Handler, func()) {
	t.Helper()
	dbPath := t.TempDir() + "/test.db"
	db, err := database.Open(dbPath)
	if err != nil { t.Fatal("Open:", err) }
	if err := database.Migrate(db.DB); err != nil { t.Fatal("Migrate:", err) }
	cfg := &config.Config{
		Env: "test", JWTSecret: "test-secret-for-web-ui-32chars!",
		AdminUsername: "admin", AdminEmail: "admin@test.com",
		AdminPasswordHash: "$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj3oW6J9BmZe",
		DataDir: t.TempDir(),
	}
	if err := database.Seed(db.DB, cfg); err != nil { t.Fatal("Seed:", err) }
	srv := api.New(cfg, db)
	return srv.Handler(), func() { db.Close(); os.Remove(dbPath) }
}

func loginWebSession(t *testing.T, h http.Handler) string {
	t.Helper()
	form := url.Values{"username": {"admin"}, "password": {"admin123"}}
	req := httptest.NewRequest("POST", "/admin/login", strings.NewReader(form.Encode()))
	req.Header.Set("Content-Type", "application/x-www-form-urlencoded")
	rr := httptest.NewRecorder()
	h.ServeHTTP(rr, req)
	for _, c := range rr.Result().Cookies() {
		if c.Name == "wcp_session" { return c.Value }
	}
	t.Fatal("no session cookie after login")
	return ""
}

func webReq(method, path, sessionToken string, body string) *http.Request {
	var bodyReader *strings.Reader
	if body != "" { bodyReader = strings.NewReader(body) } else { bodyReader = strings.NewReader("") }
	req := httptest.NewRequest(method, path, bodyReader)
	if sessionToken != "" {
		req.AddCookie(&http.Cookie{Name: "wcp_session", Value: sessionToken})
	}
	if body != "" { req.Header.Set("Content-Type", "application/x-www-form-urlencoded") }
	return req
}

func TestWeb_LoginPage(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	req := httptest.NewRequest("GET", "/admin/login", nil)
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 200 { t.Errorf("login page: want 200, got %d", rr.Code) }
	if !strings.Contains(rr.Body.String(), "WCP360") { t.Error("login page missing WCP360") }
}

func TestWeb_DashboardRedirectsUnauthenticated(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	req := httptest.NewRequest("GET", "/admin/", nil)
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 303 { t.Errorf("want 303 redirect, got %d", rr.Code) }
}

func TestWeb_DashboardAuthenticated(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	req := webReq("GET", "/admin/", token, "")
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 200 { t.Errorf("dashboard: want 200, got %d", rr.Code) }
	if !strings.Contains(rr.Body.String(), "Dashboard") { t.Error("missing Dashboard heading") }
}

func TestWeb_TenantList(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	req := webReq("GET", "/admin/tenants", token, "")
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 200 { t.Errorf("tenant list: want 200, got %d", rr.Code) }
}

func TestWeb_TenantDetail_Found(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	// Create a tenant first via API to get an ID
	db2, _ := database.Open(t.TempDir() + "/x.db") // just for the ID pattern
	_ = db2; db2.Close()
	// Use the web server's own handler to create
	form := url.Values{"username":{"webtest"},"email":{"w@t.com"},"plan":{"starter"},"disk_quota_mb":{"1024"},"bandwidth_mb":{"10240"},"max_sites":{"1"}}
	req := webReq("POST", "/admin/tenants", token, form.Encode())
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	// Now fetch detail
	req2 := webReq("GET", "/admin/tenants/1", token, "")
	rr2 := httptest.NewRecorder(); h.ServeHTTP(rr2, req2)
	if rr2.Code != 200 { t.Errorf("detail: want 200, got %d", rr2.Code) }
}

func TestWeb_TenantDetail_NotFound(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	req := webReq("GET", "/admin/tenants/99999", token, "")
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	// Should redirect to tenants list with error
	if rr.Code != 303 { t.Errorf("not-found: want 303, got %d", rr.Code) }
}

func TestWeb_TenantAuditLimit(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	form := url.Values{"username":{"auditlimtest"},"plan":{"starter"},"disk_quota_mb":{"1024"},"bandwidth_mb":{"10240"},"max_sites":{"1"}}
	webReqCreate := webReq("POST", "/admin/tenants", token, form.Encode())
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, webReqCreate)
	req := webReq("GET", "/admin/tenants/1?audit_limit=10", token, "")
	rr2 := httptest.NewRecorder(); h.ServeHTTP(rr2, req)
	if rr2.Code != 200 { t.Errorf("audit limit: want 200, got %d", rr2.Code) }
}

func TestWeb_UpdateTenant_InvalidStatus(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	form := url.Values{"username":{"updtest"},"plan":{"starter"},"disk_quota_mb":{"1024"},"bandwidth_mb":{"10240"},"max_sites":{"1"}}
	webReq1 := webReq("POST", "/admin/tenants", token, form.Encode())
	rr1 := httptest.NewRecorder(); h.ServeHTTP(rr1, webReq1)
	// Update with invalid status
	upd := url.Values{"status":{"deleted"},"plan":{"pro"},"disk_quota_mb":{"2048"},"bandwidth_mb":{"20480"},"max_sites":{"2"}}
	req := webReq("POST", "/admin/tenants/1/update", token, upd.Encode())
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 303 { t.Errorf("invalid status update: want 303, got %d", rr.Code) }
	loc := rr.Header().Get("Location")
	if !strings.Contains(loc, "error") { t.Errorf("expected error in redirect, got %q", loc) }
}

func TestWeb_ToggleTenant_Plain(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	form := url.Values{"username":{"toggletest"},"plan":{"starter"},"disk_quota_mb":{"1024"},"bandwidth_mb":{"10240"},"max_sites":{"1"}}
	webReq1 := webReq("POST", "/admin/tenants", token, form.Encode())
	rr1 := httptest.NewRecorder(); h.ServeHTTP(rr1, webReq1)
	req := webReq("POST", "/admin/tenants/1/toggle-status", token, "")
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 303 { t.Errorf("toggle plain: want 303, got %d", rr.Code) }
}

func TestWeb_ToggleTenant_HTMX(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	form := url.Values{"username":{"htmxtest"},"plan":{"starter"},"disk_quota_mb":{"1024"},"bandwidth_mb":{"10240"},"max_sites":{"1"}}
	webReq1 := webReq("POST", "/admin/tenants", token, form.Encode())
	rr1 := httptest.NewRecorder(); h.ServeHTTP(rr1, webReq1)
	req := webReq("POST", "/admin/tenants/1/toggle-status", token, "")
	req.Header.Set("HX-Request", "true")
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 200 { t.Errorf("toggle HTMX: want 200, got %d", rr.Code) }
	body := rr.Body.String()
	if !strings.Contains(body, "status-badge") { t.Errorf("HTMX: expected badge fragment, got %q", body) }
}

func TestWeb_ToggleTenant_HTMX_NotFound(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	req := webReq("POST", "/admin/tenants/99999/toggle-status", token, "")
	req.Header.Set("HX-Request", "true")
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 404 { t.Errorf("toggle HTMX 404: want 404, got %d", rr.Code) }
}

func TestWeb_AuditPage(t *testing.T) {
	h, cleanup := newWebServer(t); defer cleanup()
	token := loginWebSession(t, h)
	req := webReq("GET", "/admin/audit", token, "")
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 200 { t.Errorf("audit page: want 200, got %d", rr.Code) }
}

// ── helpers used by tenant_integration_test.go ────────────────────────────

func newTestServerAndDB(t *testing.T) (*http.Server, *database.DB, *config.Config, func()) {
	t.Helper()
	dbPath := t.TempDir() + "/int.db"
	db, err := database.Open(dbPath)
	if err != nil { t.Fatal(err) }
	database.Migrate(db.DB)
	cfg := &config.Config{
		Env: "test", JWTSecret: "integration-secret-32-characters!!",
		AdminUsername: "admin", AdminEmail: "admin@test.com",
		AdminPasswordHash: "$2a$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewdBPj3oW6J9BmZe",
		DataDir: t.TempDir(),
	}
	database.Seed(db.DB, cfg)
	return nil, db, cfg, func() { db.Close(); os.Remove(dbPath) }
}

func createTestTenantForDashboard(t *testing.T, db *database.DB, cfg *config.Config, username string) int64 {
	t.Helper()
	req := &models.CreateTenantRequest{Username: username, Email: username + "@t.com", Plan: "starter", DiskQuotaMB: 1024, BandwidthMB: 10240, MaxSites: 1}
	req.Validate()
	id, err := queries.CreateTenant(context.Background(), db.DB, req, cfg.DataDir)
	if err != nil { t.Fatal("createTestTenant:", err) }
	return id
}

func itoa(n int64) string { return fmt.Sprintf("%d", n) }
