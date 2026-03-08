// ======================================================================
// WCP 360 | V0.1.0 | internal/models/tenant_test.go
// ======================================================================

package models

import "testing"

func TestCreateTenantRequest_Validate(t *testing.T) {
	cases := []struct {
		name    string
		req     CreateTenantRequest
		wantErr bool
	}{
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
			}
		})
	}
}

func TestUpdateTenantRequest_Validate(t *testing.T) {
	r := UpdateTenantRequest{Status: "deleted"}
	if err := r.Validate(); err == nil {
		t.Error("expected error for invalid status")
	}
	r2 := UpdateTenantRequest{Status: "suspended", Plan: "pro"}
	if err := r2.Validate(); err != nil {
		t.Errorf("valid update request rejected: %v", err)
	}
}
