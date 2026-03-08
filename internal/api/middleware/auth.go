// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/middleware/auth.go
// Description: Bearer token (JWT) auth middleware for the JSON API.
// ======================================================================

package middleware

import (
	"context"
	"database/sql"
	"encoding/json"
	"net/http"
	"strings"

	"github.com/wcp360/wcp360/internal/auth"
	"github.com/wcp360/wcp360/internal/database/queries"
)

type contextKeyType struct{}

var claimsKey = contextKeyType{}

func RequireAuth(jwtSecret string, db *sql.DB) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			header := r.Header.Get("Authorization")
			if !strings.HasPrefix(header, "Bearer ") {
				writeUnauthorized(w, "missing or invalid Authorization header")
				return
			}
			tokenStr := strings.TrimPrefix(header, "Bearer ")
			claims, err := auth.ValidateToken(tokenStr, jwtSecret)
			if err != nil {
				writeUnauthorized(w, "invalid or expired token")
				return
			}
			if db != nil {
				invalidated, err := queries.IsTokenInvalidated(r.Context(), db, claims.ID)
				if err == nil && invalidated {
					writeUnauthorized(w, "token has been revoked")
					return
				}
			}
			ctx := context.WithValue(r.Context(), claimsKey, claims)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

func RequireRole(jwtSecret string, db *sql.DB, role auth.Role) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return RequireAuth(jwtSecret, db)(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			claims := ClaimsFromContext(r.Context())
			if claims == nil || claims.Role != role {
				w.Header().Set("Content-Type", "application/json")
				w.WriteHeader(http.StatusForbidden)
				json.NewEncoder(w).Encode(map[string]string{"error": "insufficient permissions"})
				return
			}
			next.ServeHTTP(w, r)
		}))
	}
}

func ClaimsFromContext(ctx context.Context) *auth.Claims {
	v := ctx.Value(claimsKey)
	if v == nil {
		return nil
	}
	c, _ := v.(*auth.Claims)
	return c
}

func writeUnauthorized(w http.ResponseWriter, msg string) {
	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("WWW-Authenticate", `Bearer realm="wcp360"`)
	w.WriteHeader(http.StatusUnauthorized)
	json.NewEncoder(w).Encode(map[string]string{"error": msg})
}
