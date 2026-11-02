package browserpolicy

import (
	"context"
	"encoding/base64"
	"errors"
	"strings"
	"testing"

	"github.com/yyogas/wcp360/daemon/internal/sessionpolicy"
)

type lookupFunc func(context.Context, sessionpolicy.Surface, [32]byte) (sessionpolicy.Session, bool, error)

func (f lookupFunc) Lookup(ctx context.Context, surface sessionpolicy.Surface, secret [32]byte) (sessionpolicy.Session, bool, error) {
	return f(ctx, surface, secret)
}

func TestAuthenticateSessionReadsCurrentServerState(t *testing.T) {
	for _, surface := range []sessionpolicy.Surface{sessionpolicy.User, sessionpolicy.Admin} {
		c, _ := fixture(surface)
		secret := [32]byte{1, 2, 3}
		header, _ := SessionCookie(surface, secret)
		value := parseCookie(t, header).Value
		calls := 0
		store := lookupFunc(func(ctx context.Context, got sessionpolicy.Surface, token [32]byte) (sessionpolicy.Session, bool, error) {
			calls++
			if got != surface || token != secret || ctx != context.Background() {
				t.Fatal("lookup scope, secret or context changed")
			}
			return c.Session, true, nil
		})
		got, code := AuthenticateSession(context.Background(), store, surface, []string{value}, now)
		if code != "" || got != c.Session || calls != 1 {
			t.Fatal("valid session failed round trip")
		}
		c.Session.Revoked = true
		got, code = AuthenticateSession(context.Background(), store, surface, []string{value}, now)
		if code != "AUTHENTICATION_REQUIRED" || got != (sessionpolicy.Session{}) || calls != 2 {
			t.Fatal("revoked session reused or failure leaked session data")
		}
	}
}

func TestAuthenticateRejectsMalformedCookiesBeforeLookup(t *testing.T) {
	token := base64.RawURLEncoding.EncodeToString(make([]byte, 32))
	store := lookupFunc(func(context.Context, sessionpolicy.Surface, [32]byte) (sessionpolicy.Session, bool, error) {
		t.Fatal("malformed credential reached store")
		return sessionpolicy.Session{}, false, nil
	})
	for _, values := range [][]string{
		nil, {""}, {token, token}, {token}, {token + "="}, {strings.Repeat("A", 42)},
		{strings.Repeat("A", 44)}, {strings.Repeat("A", 42) + "B"},
		{strings.Repeat("A", 42) + "+"}, {strings.Repeat("A", 42) + "/"},
		{"\n" + strings.Repeat("A", 42)}, {strings.Repeat("A", 42) + " "},
	} {
		got, code := AuthenticateSession(context.Background(), store, sessionpolicy.User, values, now)
		if code != "AUTHENTICATION_REQUIRED" || got != (sessionpolicy.Session{}) {
			t.Fatal("invalid or duplicate credential accepted")
		}
	}
}

func TestAuthenticateNeverExposesInvalidServerSession(t *testing.T) {
	value := base64.RawURLEncoding.EncodeToString([]byte(strings.Repeat("x", 32)))
	for _, scenario := range []string{"not-found", "backend-error", "wrong-surface", "expired", "missing-mfa", "cancelled-during-lookup"} {
		t.Run(scenario, func(t *testing.T) {
			c, _ := fixture(sessionpolicy.Admin)
			ctx, cancel := context.WithCancel(context.Background())
			defer cancel()
			want := "AUTHENTICATION_REQUIRED"
			store := lookupFunc(func(context.Context, sessionpolicy.Surface, [32]byte) (sessionpolicy.Session, bool, error) {
				switch scenario {
				case "not-found":
					return c.Session, false, nil
				case "backend-error":
					want = "SERVICE_UNAVAILABLE"
					return c.Session, true, errors.New("private storage detail")
				case "wrong-surface":
					c.Session.Surface = sessionpolicy.User
				case "expired":
					c.Session.ExpiresAt = now
				case "missing-mfa":
					c.Session.MFAAt = (sessionpolicy.Session{}).MFAAt
					want = "MFA_REQUIRED"
				case "cancelled-during-lookup":
					cancel()
					want = "SERVICE_UNAVAILABLE"
				}
				return c.Session, true, nil
			})
			got, code := AuthenticateSession(ctx, store, sessionpolicy.Admin, []string{value}, now)
			if string(code) != want || got != (sessionpolicy.Session{}) {
				t.Fatal("invalid server state or backend error escaped")
			}
		})
	}
}

func TestAuthenticateCancellationAndConfiguration(t *testing.T) {
	store := lookupFunc(func(context.Context, sessionpolicy.Surface, [32]byte) (sessionpolicy.Session, bool, error) {
		t.Fatal("invalid context or configuration reached store")
		return sessionpolicy.Session{}, false, nil
	})
	ctx, cancel := context.WithCancel(context.Background())
	cancel()
	if _, code := AuthenticateSession(ctx, store, sessionpolicy.User, nil, now); code != "SERVICE_UNAVAILABLE" {
		t.Fatal("cancelled request accepted")
	}
	if _, code := AuthenticateSession(nil, store, sessionpolicy.User, nil, now); code != "INTERNAL_ERROR" {
		t.Fatal("nil context accepted")
	}
	if _, code := AuthenticateSession(context.Background(), nil, sessionpolicy.User, nil, now); code != "INTERNAL_ERROR" {
		t.Fatal("missing store accepted")
	}
	if _, code := AuthenticateSession(context.Background(), store, "unknown", nil, now); code != "INTERNAL_ERROR" {
		t.Fatal("unknown surface accepted")
	}
}
