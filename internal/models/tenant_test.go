// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/models/tenant_test.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine | V0.0.5
// File: internal/models/tenant_test.go
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package models

import "testing"

func TestCreateTenantRequest_Validate(t *testing.T) {
<<<<<<< HEAD
	cases := []struct {
=======
	tests := []struct {
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
		name    string
		req     CreateTenantRequest
		wantErr bool
	}{
<<<<<<< HEAD
		{"valid", CreateTenantRequest{Username: "alice", Plan: "starter", DiskQuotaMB: 1024, BandwidthMB: 10240, MaxSites: 1}, false},
		{"short username", CreateTenantRequest{Username: "ab", Plan: "starter"}, true},
		{"uppercase", CreateTenantRequest{Username: "Alice", Plan: "starter"}, true},
		{"reserved", CreateTenantRequest{Username: "admin", Plan: "starter"}, true},
		{"bad plan", CreateTenantRequest{Username: "alice", Plan: "free"}, true},
	}
	for _, tc := range cases {
		t.Run(tc.name, func(t *testing.T) {
			err := tc.req.Validate()
			if (err != nil) != tc.wantErr {
				t.Errorf("Validate() error = %v, wantErr %v", err, tc.wantErr)
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
			}
		})
	}
}

func TestUpdateTenantRequest_Validate(t *testing.T) {
<<<<<<< HEAD
	r := UpdateTenantRequest{Status: "deleted"}
	if err := r.Validate(); err == nil {
		t.Error("expected error for invalid status")
	}
	r2 := UpdateTenantRequest{Status: "suspended", Plan: "pro"}
	if err := r2.Validate(); err != nil {
		t.Errorf("valid update request rejected: %v", err)
=======
	if err := (&UpdateTenantRequest{Status: "active"}).Validate(); err != nil {
		t.Errorf("active status should be valid: %v", err)
	}
	if err := (&UpdateTenantRequest{Status: "suspended"}).Validate(); err != nil {
		t.Errorf("suspended status should be valid: %v", err)
	}
	if err := (&UpdateTenantRequest{Status: "deleted"}).Validate(); err == nil {
		t.Error("deleted status should be invalid via API")
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	}
}
