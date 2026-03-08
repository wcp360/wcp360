// ======================================================================
// WCP 360 | V0.1.0 | internal/api/middleware/auth.go
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

type contextKey string

const claimsKey contextKey = "claims"

func ClaimsFromContext(ctx context.Context) *auth.Claims {
	v, _ := ctx.Value(claimsKey).(*auth.Claims)
	return v
}

func RequireAuth(secret string, db *sql.DB) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			header := r.Header.Get("Authorization")
			if !strings.HasPrefix(header, "Bearer ") {
				writeUnauth(w, "missing or invalid Authorization header")
				return
			}
			tokenStr := strings.TrimPrefix(header, "Bearer ")
			if secret == "" { secret = "default-dev-secret-change-in-production!!" }
			claims, err := auth.ParseToken(tokenStr, secret)
			if err != nil { writeUnauth(w, "invalid token"); return }
			invalidated, err := queries.IsTokenInvalidated(r.Context(), db, claims.ID)
			if err != nil || invalidated { writeUnauth(w, "token revoked"); return }
			ctx := context.WithValue(r.Context(), claimsKey, claims)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

func RequireRole(secret string, db *sql.DB, role string) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return RequireAuth(secret, db)(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			claims := ClaimsFromContext(r.Context())
			if claims == nil || claims.Role != role {
				w.Header().Set("Content-Type", "application/json")
				w.WriteHeader(http.StatusForbidden)
				json.NewEncoder(w).Encode(map[string]string{"error": "forbidden"})
				return
			}
			next.ServeHTTP(w, r)
		}))
	}
}

func writeUnauth(w http.ResponseWriter, msg string) {
	w.Header().Set("Content-Type", "application/json")
	w.Header().Set("WWW-Authenticate", "Bearer")
	w.WriteHeader(http.StatusUnauthorized)
	json.NewEncoder(w).Encode(map[string]string{"error": msg})
}
