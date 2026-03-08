// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/api/middleware/webauth.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/api/middleware/webauth.go
// Description: Cookie-based auth middleware for the web UI (/admin/*).
//              HTTP-only wcp_session cookie, SameSite=Strict, HTMX-aware.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package middleware

import (
	"context"
	"database/sql"
	"net/http"
	"time"

<<<<<<< HEAD
	"github.com/wcp360/wcp360/internal/services"
=======
	"github.com/wcp360/wcp360/internal/auth"
	"github.com/wcp360/wcp360/internal/database/queries"
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
)

const (
	SessionCookieName = "wcp_session"
	SessionCookiePath = "/"
)

<<<<<<< HEAD
func RequireWebAuth(secret string, db *sql.DB) func(http.Handler) http.Handler {
=======
func RequireWebAuth(jwtSecret string, db *sql.DB) func(http.Handler) http.Handler {
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			cookie, err := r.Cookie(SessionCookieName)
			if err != nil {
<<<<<<< HEAD
				redirectLogin(w, r)
				return
			}
			claims, err := services.ValidateWebSession(cookie.Value, secret, db, r.Context())
			if err != nil {
				ClearSessionCookie(w)
				redirectLogin(w, r)
				return
			}
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
			ctx := context.WithValue(r.Context(), claimsKey, claims)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

<<<<<<< HEAD
func SetSessionCookie(w http.ResponseWriter, token string, expiresAt time.Time, isProd bool) {
=======
func SetSessionCookie(w http.ResponseWriter, token string, expiresAt time.Time) {
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	http.SetCookie(w, &http.Cookie{
		Name:     SessionCookieName,
		Value:    token,
		Path:     SessionCookiePath,
		Expires:  expiresAt,
		HttpOnly: true,
<<<<<<< HEAD
		Secure:   isProd,
		SameSite: http.SameSiteStrictMode,
=======
		SameSite: http.SameSiteStrictMode,
		// Secure: true, // TODO: enable in production
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
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

<<<<<<< HEAD
func redirectLogin(w http.ResponseWriter, r *http.Request) {
=======
func redirectOrUnauthorized(w http.ResponseWriter, r *http.Request) {
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	if r.Header.Get("HX-Request") == "true" {
		w.Header().Set("HX-Redirect", "/admin/login")
		w.WriteHeader(http.StatusUnauthorized)
		return
	}
	http.Redirect(w, r, "/admin/login", http.StatusSeeOther)
}
