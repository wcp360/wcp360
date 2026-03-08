// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
<<<<<<< HEAD
// Version: V0.1.0
=======
// Version: V0.0.5
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// Website: https://www.wcp360.com
// File: internal/api/handlers/tenant_integration_test.go
// Description: Full httptest integration tests — real JWT + real SQLite DB.
// ======================================================================

package handlers_test

import (
	"bytes"
	"context"
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"os"
	"strconv"
	"testing"

	"github.com/wcp360/wcp360/internal/api"
	"github.com/wcp360/wcp360/internal/auth"
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database"
	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/models"
)

const testSecret = "integration-test-secret-at-least-32-chars-long!"

type testEnv struct {
	server *httptest.Server
	db     *database.DB
	cfg    *config.Config
}

func setupIntegration(t *testing.T) *testEnv {
	t.Helper()
	f, err := os.CreateTemp("", "wcp360-integration-*.db")
	if err != nil { t.Fatal("create temp db:", err) }
	t.Cleanup(func() { os.Remove(f.Name()) })
	f.Close()

	db, err := database.Open(f.Name())
	if err != nil { t.Fatal("open db:", err) }
	t.Cleanup(func() { db.Close() })
	if err := db.Migrate(); err != nil { t.Fatal("migrate:", err) }

	cfg := &config.Config{
		ListenAddr: ":0", Env: "test", LogLevel: "error",
		JWTSecret: testSecret, DataDir: "/srv/www",
		AdminUsername: "testadmin",
		AdminPasswordHash: "$2b$12$placeholder",
	}

	_, err = queries.CreateAdmin(context.Background(), db.DB,
		"testadmin", "$2b$12$placeholder", "test@example.com", "root")
	if err != nil { t.Fatal("seed admin:", err) }

	srv := api.New(cfg, db)
	ts := httptest.NewServer(srv.Handler())
	t.Cleanup(ts.Close)
	return &testEnv{server: ts, db: db, cfg: cfg}
}

func (e *testEnv) makeToken(t *testing.T, role auth.Role) string {
	t.Helper()
	tokenStr, jti, expiresAt, err := auth.GenerateToken("testadmin", role, testSecret)
	if err != nil { t.Fatalf("makeToken: %v", err) }
	if err := queries.RegisterSession(context.Background(), e.db.DB, jti, "testadmin", string(role), expiresAt); err != nil {
		t.Fatalf("registerSession: %v", err)
	}
	return tokenStr
}

func (e *testEnv) do(t *testing.T, method, path string, body any, token string) *http.Response {
	t.Helper()
	var bodyBytes []byte
	if body != nil {
		var err error
		if bodyBytes, err = json.Marshal(body); err != nil { t.Fatal("marshal:", err) }
	}
	req, err := http.NewRequest(method, e.server.URL+path, bytes.NewReader(bodyBytes))
	if err != nil { t.Fatal("new request:", err) }
	if body != nil { req.Header.Set("Content-Type", "application/json") }
	if token != "" { req.Header.Set("Authorization", "Bearer "+token) }
	resp, err := http.DefaultClient.Do(req)
	if err != nil { t.Fatal("do request:", err) }
	return resp
}

func decodeBody(t *testing.T, resp *http.Response, v any) {
	t.Helper()
	defer resp.Body.Close()
	if err := json.NewDecoder(resp.Body).Decode(v); err != nil {
		t.Fatalf("decode body: %v", err)
	}
}

func TestAPI_Healthz(t *testing.T) {
	e := setupIntegration(t)
	resp := e.do(t, "GET", "/healthz", nil, "")
	if resp.StatusCode != http.StatusOK {
		t.Errorf("healthz: expected 200, got %d", resp.StatusCode)
	}
	resp.Body.Close()
}

func TestAPI_Login_NoToken_Returns401(t *testing.T) {
	e := setupIntegration(t)
	resp := e.do(t, "GET", "/api/v1/auth/me", nil, "")
	if resp.StatusCode != http.StatusUnauthorized {
		t.Errorf("expected 401, got %d", resp.StatusCode)
	}
	resp.Body.Close()
}

func TestAPI_Me_ValidToken(t *testing.T) {
	e := setupIntegration(t)
	token := e.makeToken(t, auth.RoleRoot)
	resp := e.do(t, "GET", "/api/v1/auth/me", nil, token)
	if resp.StatusCode != http.StatusOK {
		t.Errorf("expected 200, got %d", resp.StatusCode)
	}
	var body struct {
		Username string `json:"username"`
		Role     string `json:"role"`
	}
	decodeBody(t, resp, &body)
	if body.Username != "testadmin" { t.Errorf("expected testadmin, got %s", body.Username) }
	if body.Role != "root" { t.Errorf("expected root, got %s", body.Role) }
}

func TestAPI_ListTenants_Empty_WithPagination(t *testing.T) {
	e := setupIntegration(t)
	token := e.makeToken(t, auth.RoleRoot)
	resp := e.do(t, "GET", "/api/v1/tenants?page=1&per_page=10", nil, token)
	if resp.StatusCode != http.StatusOK { t.Errorf("expected 200, got %d", resp.StatusCode) }
	var body struct {
		Pagination struct {
			Page    int `json:"page"`
			PerPage int `json:"per_page"`
			Total   int `json:"total"`
		} `json:"pagination"`
	}
	decodeBody(t, resp, &body)
	if body.Pagination.Total != 0 { t.Errorf("expected 0 tenants, got %d", body.Pagination.Total) }
}

func TestAPI_ListTenants_Forbidden_NonRoot(t *testing.T) {
	e := setupIntegration(t)
	tokenStr, jti, expiresAt, _ := auth.GenerateToken("testadmin", auth.RoleTenant, testSecret)
	queries.RegisterSession(context.Background(), e.db.DB, jti, "testadmin", "tenant", expiresAt)
	resp := e.do(t, "GET", "/api/v1/tenants", nil, tokenStr)
	if resp.StatusCode != http.StatusForbidden { t.Errorf("expected 403, got %d", resp.StatusCode) }
	resp.Body.Close()
}

func TestAPI_CreateTenant_Full_Cycle(t *testing.T) {
	e := setupIntegration(t)
	token := e.makeToken(t, auth.RoleRoot)

	// 1. Create
	createResp := e.do(t, "POST", "/api/v1/tenants", models.CreateTenantRequest{
		Username: "alice", Email: "alice@example.com", Plan: "starter",
	}, token)
	if createResp.StatusCode != http.StatusCreated { t.Fatalf("expected 201, got %d", createResp.StatusCode) }
	var tenant models.TenantResponse
	decodeBody(t, createResp, &tenant)
	if tenant.Username != "alice" { t.Errorf("expected alice, got %s", tenant.Username) }
	if tenant.HomeDir != "/srv/www/alice" { t.Errorf("expected /srv/www/alice, got %s", tenant.HomeDir) }

	// 2. Get
	getResp := e.do(t, "GET", "/api/v1/tenants/"+strconv.FormatInt(tenant.ID, 10), nil, token)
	if getResp.StatusCode != http.StatusOK { t.Errorf("GetTenant: expected 200, got %d", getResp.StatusCode) }
	getResp.Body.Close()

	// 3. List — total=1
	listResp := e.do(t, "GET", "/api/v1/tenants", nil, token)
	var listBody struct{ Pagination struct{ Total int `json:"total"` } `json:"pagination"` }
	decodeBody(t, listResp, &listBody)
	if listBody.Pagination.Total != 1 { t.Errorf("expected total=1, got %d", listBody.Pagination.Total) }

	// 4. Update
	patchResp := e.do(t, "PATCH", "/api/v1/tenants/"+strconv.FormatInt(tenant.ID, 10),
		models.UpdateTenantRequest{Status: "suspended", Plan: "pro"}, token)
	if patchResp.StatusCode != http.StatusOK { t.Errorf("PATCH: expected 200, got %d", patchResp.StatusCode) }
	patchResp.Body.Close()

	// 5. Duplicate — 409
	dupResp := e.do(t, "POST", "/api/v1/tenants", models.CreateTenantRequest{
		Username: "alice", Email: "alice2@example.com",
	}, token)
	if dupResp.StatusCode != http.StatusConflict { t.Errorf("expected 409, got %d", dupResp.StatusCode) }
	dupResp.Body.Close()

	// 6. Delete
	delResp := e.do(t, "DELETE", "/api/v1/tenants/"+strconv.FormatInt(tenant.ID, 10), nil, token)
	if delResp.StatusCode != http.StatusOK { t.Errorf("DELETE: expected 200, got %d", delResp.StatusCode) }
	delResp.Body.Close()

	// 7. After delete: total=0
	listResp2 := e.do(t, "GET", "/api/v1/tenants", nil, token)
	var lb2 struct{ Pagination struct{ Total int `json:"total"` } `json:"pagination"` }
	decodeBody(t, listResp2, &lb2)
	if lb2.Pagination.Total != 0 { t.Errorf("expected 0 after delete, got %d", lb2.Pagination.Total) }

	// 8. After delete: 404
	get2 := e.do(t, "GET", "/api/v1/tenants/"+strconv.FormatInt(tenant.ID, 10), nil, token)
	if get2.StatusCode != http.StatusNotFound { t.Errorf("expected 404, got %d", get2.StatusCode) }
	get2.Body.Close()
}

func TestAPI_GetAuditLog(t *testing.T) {
	e := setupIntegration(t)
	token := e.makeToken(t, auth.RoleRoot)
	e.do(t, "POST", "/api/v1/tenants", models.CreateTenantRequest{Username: "bob", Email: "bob@example.com"}, token).Body.Close()
	resp := e.do(t, "GET", "/api/v1/audit?limit=10", nil, token)
	if resp.StatusCode != http.StatusOK { t.Errorf("expected 200, got %d", resp.StatusCode) }
	var body struct{ Total int `json:"total"` }
	decodeBody(t, resp, &body)
	if body.Total == 0 { t.Error("expected at least 1 audit entry") }
}

func TestAPI_TokenRevocation(t *testing.T) {
	e := setupIntegration(t)
	token := e.makeToken(t, auth.RoleRoot)

	resp1 := e.do(t, "GET", "/api/v1/auth/me", nil, token)
	if resp1.StatusCode != http.StatusOK { t.Fatalf("expected 200 before logout, got %d", resp1.StatusCode) }
	resp1.Body.Close()

	e.do(t, "POST", "/api/v1/auth/logout", nil, token).Body.Close()

	resp2 := e.do(t, "GET", "/api/v1/auth/me", nil, token)
	if resp2.StatusCode != http.StatusUnauthorized { t.Errorf("expected 401 after revocation, got %d", resp2.StatusCode) }
	resp2.Body.Close()
}

func TestAPI_InvalidUsername_Cases(t *testing.T) {
	e := setupIntegration(t)
	token := e.makeToken(t, auth.RoleRoot)
	cases := []struct{ username string; wantCode int }{
		{"ab", 400}, {"root", 400}, {"admin", 400},
		{"Alice", 400}, {"alice!", 400}, {"alice", 201},
	}
	for _, tc := range cases {
		resp := e.do(t, "POST", "/api/v1/tenants", models.CreateTenantRequest{
			Username: tc.username, Email: tc.username + "@example.com",
		}, token)
		resp.Body.Close()
		if resp.StatusCode != tc.wantCode {
			t.Errorf("username %q: expected %d, got %d", tc.username, tc.wantCode, resp.StatusCode)
		}
	}
}
