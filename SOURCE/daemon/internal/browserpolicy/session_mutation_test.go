package browserpolicy

import (
	"context"
	"errors"
	"testing"
	"time"

	"github.com/yyogas/wcp360/daemon/internal/sessionpolicy"
)

type browserLookupFunc func(context.Context, sessionpolicy.Surface, [32]byte) (sessionpolicy.Session, string, bool, error)

func (f browserLookupFunc) LookupBrowser(ctx context.Context, surface sessionpolicy.Surface, secret [32]byte) (sessionpolicy.Session, string, bool, error) {
	return f(ctx, surface, secret)
}

func TestSessionMutationComposition(t *testing.T) {
	for _, surface := range []sessionpolicy.Surface{sessionpolicy.User, sessionpolicy.Admin} {
		for _, scenario := range []string{"valid", "rotated-csrf", "missing-csrf", "wrong-origin", "duplicate-cookie", "expired-during-lookup", "backward-clock", "storage-error", "revoked", "wrong-surface", "safe-method", "cancelled"} {
			t.Run(string(surface)+"/"+scenario, func(t *testing.T) {
				c, request := fixture(surface)
				secret := [32]byte{7, 8, 9}
				header, _ := SessionCookie(surface, secret)
				cookies := []string{parseCookie(t, header).Value}
				ctx, cancel := context.WithCancel(context.Background())
				defer cancel()
				current := now
				calls := 0
				want := ""
				switch scenario {
				case "wrong-origin":
					request.Origins = []string{"https://untrusted.example.test"}
					want = "AUTHORIZATION_DENIED"
				case "duplicate-cookie":
					cookies = append(cookies, cookies[0])
					want = "AUTHENTICATION_REQUIRED"
				case "safe-method":
					request.Method = "GET"
					want = "VALIDATION_FAILED"
				}
				store := browserLookupFunc(func(gotCtx context.Context, gotSurface sessionpolicy.Surface, gotSecret [32]byte) (sessionpolicy.Session, string, bool, error) {
					calls++
					if gotCtx != ctx || gotSurface != surface || gotSecret != secret {
						t.Fatal("lookup input mismatch")
					}
					switch scenario {
					case "rotated-csrf", "missing-csrf":
						c.CSRFReference = ""
						if scenario == "rotated-csrf" {
							c.CSRFReference = "new-server-reference"
						}
						want = "AUTHORIZATION_DENIED"
					case "expired-during-lookup":
						current = c.Session.ExpiresAt
						want = "AUTHENTICATION_REQUIRED"
					case "backward-clock":
						current = now.Add(-time.Second)
						want = "INTERNAL_ERROR"
					case "storage-error":
						want = "SERVICE_UNAVAILABLE"
						return c.Session, c.CSRFReference, true, errors.New("private database detail")
					case "revoked":
						c.Session.Revoked = true
						want = "AUTHENTICATION_REQUIRED"
					case "wrong-surface":
						c.Session.Surface = "other"
						want = "AUTHENTICATION_REQUIRED"
					case "cancelled":
						cancel()
						want = "SERVICE_UNAVAILABLE"
					}
					return c.Session, c.CSRFReference, true, nil
				})
				got, code := CheckSessionMutation(ctx, store, surface, c.Origin, cookies, request, func() time.Time { return current })
				if string(code) != want {
					t.Fatalf("unexpected outcome: %s", code)
				}
				if want == "" && got != c.Session {
					t.Fatal("valid server session not returned")
				}
				if want != "" && got != (sessionpolicy.Session{}) {
					t.Fatal("failure disclosed a session")
				}
				wantCalls := 1
				if scenario == "duplicate-cookie" {
					wantCalls = 0
				}
				if calls != wantCalls {
					t.Fatal("unexpected lookup count")
				}
			})
		}
	}
}

func TestSessionMutationRejectsInvalidConfiguration(t *testing.T) {
	store := browserLookupFunc(func(context.Context, sessionpolicy.Surface, [32]byte) (sessionpolicy.Session, string, bool, error) {
		t.Fatal("invalid configuration reached storage")
		return sessionpolicy.Session{}, "", false, nil
	})
	clock := func() time.Time { return now }
	if _, code := CheckSessionMutation(context.Background(), nil, sessionpolicy.User, "https://user.example.test", nil, Mutation{}, clock); code != "INTERNAL_ERROR" {
		t.Fatal("missing store accepted")
	}
	if _, code := CheckSessionMutation(context.Background(), store, sessionpolicy.User, "https://user.example.test", nil, Mutation{}, nil); code != "INTERNAL_ERROR" {
		t.Fatal("missing clock accepted")
	}
	if _, code := CheckSessionMutation(context.Background(), store, sessionpolicy.User, "*", nil, Mutation{}, clock); code != "INTERNAL_ERROR" {
		t.Fatal("invalid configured origin accepted")
	}
}
