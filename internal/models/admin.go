// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/models/admin.go
// Description: Admin domain model and response type.
// ======================================================================

package models

import "time"

type Admin struct {
	ID           int64
	Username     string
	PasswordHash string
	Email        string
	Role         string
	IsActive     bool
	LastLoginAt  *time.Time
	CreatedAt    time.Time
	UpdatedAt    time.Time
}

type AdminResponse struct {
	ID          int64      `json:"id"`
	Username    string     `json:"username"`
	Email       string     `json:"email"`
	Role        string     `json:"role"`
	LastLoginAt *time.Time `json:"last_login_at,omitempty"`
	CreatedAt   time.Time  `json:"created_at"`
}

func (a *Admin) ToResponse() AdminResponse {
	return AdminResponse{
		ID:          a.ID,
		Username:    a.Username,
		Email:       a.Email,
		Role:        a.Role,
		LastLoginAt: a.LastLoginAt,
		CreatedAt:   a.CreatedAt,
	}
}
