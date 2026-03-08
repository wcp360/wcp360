// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine | V0.0.5
// File: internal/models/tenant_test.go
// ======================================================================

package models

import "testing"

func TestCreateTenantRequest_Validate(t *testing.T) {
	tests := []struct {
		name    string
		req     CreateTenantRequest
		wantErr bool
	}{
		{"valid", CreateTenantRequest{Username: "alice", Email: "a@b.com"}, false},
		{"too short", CreateTenantRequest{Username: "ab", Email: "a@b.com"}, true},
		{"reserved root", CreateTenantRequest{Username: "root", Email: "a@b.com"}, true},
		{"reserved admin", CreateTenantRequest{Username: "admin", Email: "a@b.com"}, true},
		{"uppercase", CreateTenantRequest{Username: "Alice", Email: "a@b.com"}, true},
		{"special chars", CreateTenantRequest{Username: "ali_ce", Email: "a@b.com"}, true},
		{"no email", CreateTenantRequest{Username: "alice"}, true},
		{"with hyphen", CreateTenantRequest{Username: "my-site", Email: "a@b.com"}, false},
	}
	for _, tt := range tests {
		t.Run(tt.name, func(t *testing.T) {
			err := tt.req.Validate()
			if (err != nil) != tt.wantErr {
				t.Errorf("Validate() error = %v, wantErr %v", err, tt.wantErr)
			}
		})
	}
}

func TestUpdateTenantRequest_Validate(t *testing.T) {
	if err := (&UpdateTenantRequest{Status: "active"}).Validate(); err != nil {
		t.Errorf("active status should be valid: %v", err)
	}
	if err := (&UpdateTenantRequest{Status: "suspended"}).Validate(); err != nil {
		t.Errorf("suspended status should be valid: %v", err)
	}
	if err := (&UpdateTenantRequest{Status: "deleted"}).Validate(); err == nil {
		t.Error("deleted status should be invalid via API")
	}
}
