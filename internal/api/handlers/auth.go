// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/api/handlers/auth.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/handlers/auth.go
// Description: JSON API auth handlers — login, logout, me.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package handlers

import (
	"errors"
	"log/slog"
	"net/http"
	"strings"

	"github.com/wcp360/wcp360/internal/api/middleware"
<<<<<<< HEAD
=======
	"github.com/wcp360/wcp360/internal/auth"
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/services"
)

type loginRequest struct {
	Username string `json:"username"`
	Password string `json:"password"`
}

<<<<<<< HEAD
func (h *Handlers) Login(w http.ResponseWriter, r *http.Request) {
	var req loginRequest
	if !decodeJSON(w, r, &req) { return }
=======
// Login handles POST /api/v1/auth/login
func (h *Handlers) Login(w http.ResponseWriter, r *http.Request) {
	var req loginRequest
	if !decodeJSON(w, r, &req) {
		return
	}
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	req.Username = strings.TrimSpace(req.Username)
	if req.Username == "" || req.Password == "" {
		writeError(w, http.StatusBadRequest, "username and password are required")
		return
	}
<<<<<<< HEAD
	result, err := services.LoginAdmin(r.Context(), h.db.DB, h.cfg, req.Username, req.Password)
	if err != nil {
		if errors.Is(err, services.ErrInvalidCredentials) {
			slog.Info("api: login failed", "username", req.Username, "ip", r.RemoteAddr)
			writeError(w, http.StatusUnauthorized, "invalid credentials")
			return
		}
		writeError(w, http.StatusInternalServerError, "internal server error")
		return
	}
	queries.LogAction(r.Context(), h.db.DB, result.Admin.Username,
		queries.ActionAdminLogin, "", "", r.RemoteAddr)
	slog.Info("api: admin login", "username", result.Admin.Username)
	writeJSON(w, http.StatusOK, map[string]any{
		"token":      result.Token,
		"expires_at": result.ExpiresAt,
		"username":   result.Admin.Username,
		"role":       result.Admin.Role,
	})
}

func (h *Handlers) Logout(w http.ResponseWriter, r *http.Request) {
	claims := middleware.ClaimsFromContext(r.Context())
	if claims != nil {
		queries.InvalidateSession(r.Context(), h.db.DB, claims.ID)
		queries.LogAction(r.Context(), h.db.DB, claims.Username, queries.ActionAdminLogout, "", "", r.RemoteAddr)
	}
	writeJSON(w, http.StatusOK, map[string]string{"message": "logged out"})
}

=======

	result, err := services.LoginAdmin(r.Context(), h.db.DB, h.cfg, req.Username, req.Password)
	if err != nil {
		if errors.Is(err, services.ErrInvalidCredentials) {
			slog.Info("login failed", "username", req.Username, "ip", r.RemoteAddr)
			writeError(w, http.StatusUnauthorized, "invalid credentials")
			return
		}
		slog.Error("login error", "err", err)
		writeError(w, http.StatusInternalServerError, "internal server error")
		return
	}

	queries.LogAction(r.Context(), h.db.DB, result.Admin.Username,
		queries.ActionAdminLogin, "", "", r.RemoteAddr)
	slog.Info("admin login", "username", result.Admin.Username, "ip", r.RemoteAddr)

	writeJSON(w, http.StatusOK, map[string]any{
		"token":      result.Token,
		"expires_at": result.ExpiresAt,
		"admin":      result.Admin.ToResponse(),
	})
}

// Logout handles POST /api/v1/auth/logout
func (h *Handlers) Logout(w http.ResponseWriter, r *http.Request) {
	claims := middleware.ClaimsFromContext(r.Context())
	if claims == nil {
		writeError(w, http.StatusUnauthorized, "not authenticated")
		return
	}
	if err := queries.InvalidateSession(r.Context(), h.db.DB, claims.ID); err != nil {
		slog.Warn("logout: invalidate session", "jti", claims.ID, "err", err)
	}
	queries.LogAction(r.Context(), h.db.DB, claims.Username,
		queries.ActionAdminLogout, "", "", r.RemoteAddr)
	slog.Info("admin logout", "username", claims.Username)
	writeJSON(w, http.StatusOK, map[string]string{"message": "logged out successfully"})
}

// Me handles GET /api/v1/auth/me
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
func (h *Handlers) Me(w http.ResponseWriter, r *http.Request) {
	claims := middleware.ClaimsFromContext(r.Context())
	if claims == nil {
		writeError(w, http.StatusUnauthorized, "not authenticated")
		return
	}
<<<<<<< HEAD
	writeJSON(w, http.StatusOK, map[string]string{
		"username": claims.Username,
		"role":     claims.Role,
	})
}
=======
	writeJSON(w, http.StatusOK, map[string]any{
		"username": claims.Username,
		"role":     string(claims.Role),
		"exp":      claims.ExpiresAt,
	})
}

func actorFromContext(r *http.Request) string {
	if claims := middleware.ClaimsFromContext(r.Context()); claims != nil {
		return claims.Username
	}
	return "unknown"
}

// ensure auth.Role is accessible (used by routes.go)
var _ = auth.RoleRoot
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
