// ======================================================================
// WCP 360 | V0.1.0 | internal/api/middleware/webauth.go
// ======================================================================

package middleware

import (
	"context"
	"database/sql"
	"net/http"
	"time"

	"github.com/wcp360/wcp360/internal/services"
)

const (
	SessionCookieName = "wcp_session"
	SessionCookiePath = "/"
)

func RequireWebAuth(secret string, db *sql.DB) func(http.Handler) http.Handler {
	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			cookie, err := r.Cookie(SessionCookieName)
			if err != nil {
				redirectLogin(w, r)
				return
			}
			claims, err := services.ValidateWebSession(cookie.Value, secret, db, r.Context())
			if err != nil {
				ClearSessionCookie(w)
				redirectLogin(w, r)
				return
			}
			ctx := context.WithValue(r.Context(), claimsKey, claims)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

func SetSessionCookie(w http.ResponseWriter, token string, expiresAt time.Time, isProd bool) {
	http.SetCookie(w, &http.Cookie{
		Name:     SessionCookieName,
		Value:    token,
		Path:     SessionCookiePath,
		Expires:  expiresAt,
		HttpOnly: true,
		Secure:   isProd,
		SameSite: http.SameSiteStrictMode,
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

func redirectLogin(w http.ResponseWriter, r *http.Request) {
	if r.Header.Get("HX-Request") == "true" {
		w.Header().Set("HX-Redirect", "/admin/login")
		w.WriteHeader(http.StatusUnauthorized)
		return
	}
	http.Redirect(w, r, "/admin/login", http.StatusSeeOther)
}
