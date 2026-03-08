// ======================================================================
// WCP 360 | V0.1.0 | internal/services/auth_service.go
// ======================================================================

package services

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"time"

	"github.com/wcp360/wcp360/internal/auth"
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/models"
)

var ErrInvalidCredentials = errors.New("invalid credentials")

type AuthResult struct {
	Admin     *models.Admin
	Token     string
	JTI       string
	ExpiresAt time.Time
}

func LoginAdmin(ctx context.Context, db *sql.DB, cfg *config.Config, username, password string) (*AuthResult, error) {
	admin, err := queries.GetAdminByUsername(ctx, db, username)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) { return nil, ErrInvalidCredentials }
		return nil, fmt.Errorf("auth_service.LoginAdmin: %w", err)
	}
	if err := auth.CheckPasswordTimingSafe(password, admin.PasswordHash); err != nil {
		return nil, ErrInvalidCredentials
	}
	secret := cfg.JWTSecret
	if secret == "" { secret = "default-dev-secret-change-in-production!!" }
	tokenStr, jti, expiresAt, err := auth.GenerateToken(admin.Username, admin.Role, secret)
	if err != nil { return nil, fmt.Errorf("auth_service: generate token: %w", err) }
	if err := queries.RegisterSession(ctx, db, jti, admin.Username, admin.Role, expiresAt); err != nil {
		return nil, fmt.Errorf("auth_service: register session: %w", err)
	}
	return &AuthResult{Admin: admin, Token: tokenStr, JTI: jti, ExpiresAt: expiresAt}, nil
}

func ValidateWebSession(tokenStr, secret string, db *sql.DB, ctx context.Context) (*auth.Claims, error) {
	if secret == "" { secret = "default-dev-secret-change-in-production!!" }
	claims, err := auth.ParseToken(tokenStr, secret)
	if err != nil { return nil, fmt.Errorf("services.ValidateWebSession: %w", err) }
	invalidated, err := queries.IsTokenInvalidated(ctx, db, claims.ID)
	if err != nil || invalidated { return nil, fmt.Errorf("token invalidated") }
	return claims, nil
}
