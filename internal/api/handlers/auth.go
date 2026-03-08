// ======================================================================
// WCP 360 | V0.1.0 | internal/api/handlers/auth.go
// ======================================================================

package handlers

import (
	"errors"
	"log/slog"
	"net/http"
	"strings"

	"github.com/wcp360/wcp360/internal/api/middleware"
	"github.com/wcp360/wcp360/internal/database/queries"
	"github.com/wcp360/wcp360/internal/services"
)

type loginRequest struct {
	Username string `json:"username"`
	Password string `json:"password"`
}

func (h *Handlers) Login(w http.ResponseWriter, r *http.Request) {
	var req loginRequest
	if !decodeJSON(w, r, &req) { return }
	req.Username = strings.TrimSpace(req.Username)
	if req.Username == "" || req.Password == "" {
		writeError(w, http.StatusBadRequest, "username and password are required")
		return
	}
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

func (h *Handlers) Me(w http.ResponseWriter, r *http.Request) {
	claims := middleware.ClaimsFromContext(r.Context())
	if claims == nil {
		writeError(w, http.StatusUnauthorized, "not authenticated")
		return
	}
	writeJSON(w, http.StatusOK, map[string]string{
		"username": claims.Username,
		"role":     claims.Role,
	})
}
