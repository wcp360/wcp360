// ======================================================================
// WCP 360 | V0.1.0 | internal/models/admin.go
// ======================================================================

package models

import "time"

type Admin struct {
	ID           int64
	Username     string
	Email        string
	PasswordHash string
	Role         string
	CreatedAt    time.Time
	UpdatedAt    time.Time
}
