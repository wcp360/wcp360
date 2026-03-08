// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/middleware/webauth.go
// Description: Cookie-based auth middleware for the web UI (/admin/*).
//              HTTP-only wcp_session cookie, SameSite=Strict, HTMX-aware.
// ======================================================================

package middleware

import (
	"context"
	"database/sql"
	"net/http"
	"time"

	"github.com/wcp360/wcp360/internal/auth"
	"github.com/wcp360/wcp360/internal/database/queries"
)

const (
	SessionCookieName = "wcp_session"
	SessionCookiePath = "/"
)

func RequireWebAuth(jwtSecret string, db *sql.DB) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			cookie, err := r.Cookie(SessionCookieName)
			if err != nil {
				redirectOrUnauthorized(w, r)
				return
			}
			claims, err := auth.ValidateToken(cookie.Value, jwtSecret)
			if err != nil {
				ClearSessionCookie(w)
				redirectOrUnauthorized(w, r)
				return
			}
			if db != nil {
				invalidated, checkErr := queries.IsTokenInvalidated(r.Context(), db, claims.ID)
				if checkErr == nil && invalidated {
					ClearSessionCookie(w)
					redirectOrUnauthorized(w, r)
					return
				}
			}
			ctx := context.WithValue(r.Context(), claimsKey, claims)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

func SetSessionCookie(w http.ResponseWriter, token string, expiresAt time.Time) {
	http.SetCookie(w, &http.Cookie{
		Name:     SessionCookieName,
		Value:    token,
		Path:     SessionCookiePath,
		Expires:  expiresAt,
		HttpOnly: true,
		SameSite: http.SameSiteStrictMode,
		// Secure: true, // TODO: enable in production
	})
}

func ClearSessionCookie(w http.ResponseWriter) {
	http.SetCookie(w, &http.Cookie{
		Name:     SessionCookieName,
		Value:    "",
		Path:     SessionCookiePath,
		MaxAge:   -1,
		HttpOnly: true,
		SameSite: http.SameSiteStrictMode,
	})
}

func redirectOrUnauthorized(w http.ResponseWriter, r *http.Request) {
	if r.Header.Get("HX-Request") == "true" {
		w.Header().Set("HX-Redirect", "/admin/login")
		w.WriteHeader(http.StatusUnauthorized)
		return
	}
	http.Redirect(w, r, "/admin/login", http.StatusSeeOther)
}
