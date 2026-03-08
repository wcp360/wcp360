// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/models/tenant.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/models/tenant.go
// Description: Tenant domain model, request types, and validation.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package models

import (
	"fmt"
	"regexp"
<<<<<<< HEAD
	"time"
)

var usernameRE = regexp.MustCompile(`^[a-z][a-z0-9\-]{2,31}$`)

var reservedUsernames = map[string]bool{
	"root": true, "admin": true, "www": true, "mail": true,
	"ftp": true, "ssh": true, "localhost": true, "wcp360": true,
}
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee

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
<<<<<<< HEAD
		Plan: t.Plan, Status: t.Status,
		DiskQuotaMB: t.DiskQuotaMB, BandwidthMB: t.BandwidthMB,
		MaxSites: t.MaxSites, HomeDir: t.HomeDir,
		CreatedAt: t.CreatedAt, UpdatedAt: t.UpdatedAt,
=======
		Plan: t.Plan, Status: t.Status, DiskQuotaMB: t.DiskQuotaMB,
		BandwidthMB: t.BandwidthMB, MaxSites: t.MaxSites,
		HomeDir: t.HomeDir, CreatedAt: t.CreatedAt, UpdatedAt: t.UpdatedAt,
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
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
<<<<<<< HEAD
	if !usernameRE.MatchString(r.Username) {
		return fmt.Errorf("username must match ^[a-z][a-z0-9\\-]{2,31}$")
	}
	if reservedUsernames[r.Username] {
		return fmt.Errorf("username %q is reserved", r.Username)
	}
	validPlans := map[string]bool{"starter": true, "pro": true, "business": true}
	if !validPlans[r.Plan] {
		return fmt.Errorf("plan must be starter|pro|business")
	}
	if r.DiskQuotaMB < 100 {
		r.DiskQuotaMB = 1024
	}
	if r.BandwidthMB < 1024 {
		r.BandwidthMB = 10240
	}
	if r.MaxSites < 1 {
		r.MaxSites = 1
	}
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
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
<<<<<<< HEAD
	if r.Plan != "" {
		validPlans := map[string]bool{"starter": true, "pro": true, "business": true}
		if !validPlans[r.Plan] {
			return fmt.Errorf("plan must be starter|pro|business")
		}
	}
	if r.Status != "" {
		validStatuses := map[string]bool{"active": true, "suspended": true}
		if !validStatuses[r.Status] {
			return fmt.Errorf("status must be active|suspended")
=======
	if r.Status != "" {
		valid := map[string]bool{"active": true, "suspended": true}
		if !valid[r.Status] {
			return fmt.Errorf("status must be active or suspended, got %q", r.Status)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
		}
	}
	return nil
}
