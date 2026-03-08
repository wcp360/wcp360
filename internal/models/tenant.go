// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/models/tenant.go
// Description: Tenant domain model, request types, and validation.
// ======================================================================

package models

import (
	"fmt"
	"regexp"
	"strings"
	"time"
)

var (
	usernameRe = regexp.MustCompile(`^[a-z][a-z0-9\-]{2,31}$`)
	reserved   = map[string]bool{
		"root": true, "admin": true, "wcp360": true, "www": true,
		"caddy": true, "mail": true, "ftp": true, "ssh": true,
		"nobody": true, "daemon": true, "bin": true, "sys": true,
	}
)

type Tenant struct {
	ID          int64
	Username    string
	Email       string
	Plan        string
	Status      string
	DiskQuotaMB int
	BandwidthMB int
	MaxSites    int
	HomeDir     string
	CreatedAt   time.Time
	UpdatedAt   time.Time
}

type TenantResponse struct {
	ID          int64     `json:"id"`
	Username    string    `json:"username"`
	Email       string    `json:"email"`
	Plan        string    `json:"plan"`
	Status      string    `json:"status"`
	DiskQuotaMB int       `json:"disk_quota_mb"`
	BandwidthMB int       `json:"bandwidth_mb"`
	MaxSites    int       `json:"max_sites"`
	HomeDir     string    `json:"home_dir"`
	CreatedAt   time.Time `json:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"`
}

func (t *Tenant) ToResponse() TenantResponse {
	return TenantResponse{
		ID: t.ID, Username: t.Username, Email: t.Email,
		Plan: t.Plan, Status: t.Status, DiskQuotaMB: t.DiskQuotaMB,
		BandwidthMB: t.BandwidthMB, MaxSites: t.MaxSites,
		HomeDir: t.HomeDir, CreatedAt: t.CreatedAt, UpdatedAt: t.UpdatedAt,
	}
}

type CreateTenantRequest struct {
	Username    string `json:"username"`
	Email       string `json:"email"`
	Plan        string `json:"plan"`
	DiskQuotaMB int    `json:"disk_quota_mb"`
	BandwidthMB int    `json:"bandwidth_mb"`
	MaxSites    int    `json:"max_sites"`
}

func (r *CreateTenantRequest) Validate() error {
	if !usernameRe.MatchString(r.Username) {
		return fmt.Errorf("username must match ^[a-z][a-z0-9-]{2,31}$")
	}
	if reserved[strings.ToLower(r.Username)] {
		return fmt.Errorf("username %q is reserved", r.Username)
	}
	if r.Email == "" {
		return fmt.Errorf("email is required")
	}
	if r.Plan == "" { r.Plan = "starter" }
	if r.DiskQuotaMB == 0 { r.DiskQuotaMB = 1024 }
	if r.BandwidthMB == 0 { r.BandwidthMB = 10240 }
	if r.MaxSites == 0 { r.MaxSites = 1 }
	return nil
}

type UpdateTenantRequest struct {
	Email       string `json:"email"`
	Plan        string `json:"plan"`
	Status      string `json:"status"`
	DiskQuotaMB int    `json:"disk_quota_mb"`
	BandwidthMB int    `json:"bandwidth_mb"`
	MaxSites    int    `json:"max_sites"`
}

func (r *UpdateTenantRequest) Validate() error {
	if r.Status != "" {
		valid := map[string]bool{"active": true, "suspended": true}
		if !valid[r.Status] {
			return fmt.Errorf("status must be active or suspended, got %q", r.Status)
		}
	}
	return nil
}
