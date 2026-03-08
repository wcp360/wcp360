// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/services/auth_service.go
// Description: Shared authentication service used by both JSON API and
//              web UI. LoginAdmin + ValidateWebSession.
// ======================================================================

package services

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"log/slog"
	"time"

	"github.com/wcp360/wcp360/internal/auth"
	"github.com/wcp360/wcp360/internal/config"
	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/models"
)

var ErrInvalidCredentials = errors.New("invalid credentials")

type LoginResult struct {
	Token     string
	JTI       string
	ExpiresAt time.Time
	Admin     *models.Admin
}

func LoginAdmin(ctx context.Context, db *sql.DB, cfg *config.Config, username, password string) (*LoginResult, error) {
	admin, err := queries.GetAdminByUsername(ctx, db, username)
	if err != nil {
		if errors.Is(err, queries.ErrNotFound) {
			_ = auth.CheckPasswordTimingSafe(password)
			return nil, ErrInvalidCredentials
		}
		return nil, fmt.Errorf("auth_service.LoginAdmin: %w", err)
	}
	if err := auth.CheckPassword(password, admin.PasswordHash); err != nil {
		return nil, ErrInvalidCredentials
	}
	tokenStr, jti, expiresAt, err := auth.GenerateToken(admin.Username, auth.Role(admin.Role), cfg.JWTSecret)
	if err != nil {
		return nil, fmt.Errorf("auth_service.LoginAdmin: generate token: %w", err)
	}
	if err := queries.RegisterSession(ctx, db, jti, admin.Username, admin.Role, expiresAt); err != nil {
		slog.Warn("auth_service: session registration failed", "username", admin.Username, "err", err)
	}
	if err := queries.UpdateLastLogin(ctx, db, admin.ID); err != nil {
		slog.Warn("auth_service: update last_login_at failed", "username", admin.Username, "err", err)
	}
	return &LoginResult{Token: tokenStr, JTI: jti, ExpiresAt: expiresAt, Admin: admin}, nil
}

func ValidateWebSession(tokenStr, jwtSecret string, db *sql.DB, ctx context.Context) (*auth.Claims, error) {
	claims, err := auth.ValidateToken(tokenStr, jwtSecret)
	if err != nil {
		return nil, err
	}
	if db != nil {
		invalidated, err := queries.IsTokenInvalidated(ctx, db, claims.ID)
		if err == nil && invalidated {
			return nil, fmt.Errorf("session has been revoked")
		}
	}
	return claims, nil
}
