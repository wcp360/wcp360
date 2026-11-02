package browserpolicy

import (
	"testing"
	"time"

	"github.com/yyogas/wcp360/daemon/internal/sessionpolicy"
)

var now = time.Date(2026, 10, 29, 12, 0, 0, 0, time.UTC)

func fixture(surface sessionpolicy.Surface) (Context, Mutation) {
	origin := "https://user.example.test"
	if surface == sessionpolicy.Admin {
		origin = "https://admin.example.test"
	}
	c := Context{Surface: surface, Origin: origin, CSRFReference: "server-bound-token"}
	c.Session = sessionpolicy.Session{Reference: [16]byte{1}, Surface: surface, IssuedAt: now.Add(-time.Minute), LastSeen: now, ExpiresAt: now.Add(time.Hour), MFAAt: now}
	return c, Mutation{Method: "POST", Origins: []string{origin}, CSRFTokens: []string{c.CSRFReference}}
}

func TestSupportedMutationsRequireBothBrowserProofs(t *testing.T) {
	for _, surface := range []sessionpolicy.Surface{sessionpolicy.User, sessionpolicy.Admin} {
		for _, method := range []string{"POST", "PUT", "PATCH", "DELETE"} {
			c, r := fixture(surface)
			r.Method = method
			if code := Check(c, r, now); code != "" {
				t.Fatalf("valid prerequisites rejected: %s", code)
			}
			r.Origins = nil
			if Check(c, r, now) != "AUTHORIZATION_DENIED" {
				t.Fatal("missing Origin accepted with valid token")
			}
			c, r = fixture(surface)
			r.CSRFTokens = nil
			if Check(c, r, now) != "AUTHORIZATION_DENIED" {
				t.Fatal("missing token accepted with valid Origin")
			}
		}
	}
}

func TestOriginBypassesAndDuplicatesDenied(t *testing.T) {
	for _, origins := range [][]string{
		nil, {""}, {"null"}, {"https://user.example.test", "https://user.example.test"},
		{"https://evil.test"}, {"http://user.example.test"},
		{"https://user.example.test.evil.test"}, {"https://user.example.test@evil.test"},
		{"https://user.example.test/"}, {"https://user.example.test:443"},
		{" https://user.example.test"}, {"https://user.example.test\r\n"},
		{"https://admin.example.test"}, {"https://user.example.test, https://evil.test"},
	} {
		c, r := fixture(sessionpolicy.User)
		r.Origins = origins
		if Check(c, r, now) != "AUTHORIZATION_DENIED" {
			t.Fatal("unapproved/ambiguous Origin accepted")
		}
	}
}

func TestCSRFBypassesDenied(t *testing.T) {
	for _, tokens := range [][]string{nil, {""}, {"wrong"}, {"server-bound-token "}, {"server-bound-token", "server-bound-token"}, {"wrong", "server-bound-token"}} {
		c, r := fixture(sessionpolicy.User)
		r.CSRFTokens = tokens
		if Check(c, r, now) != "AUTHORIZATION_DENIED" {
			t.Fatal("invalid/ambiguous CSRF accepted")
		}
	}
	c, r := fixture(sessionpolicy.User)
	c.CSRFReference = ""
	if Check(c, r, now) != "AUTHORIZATION_DENIED" {
		t.Fatal("uninitialized server CSRF accepted")
	}
	c, r = fixture(sessionpolicy.User)
	c.CSRFReference = "rotated-token"
	if Check(c, r, now) != "AUTHORIZATION_DENIED" {
		t.Fatal("old CSRF survived server rotation")
	}
}

func TestSessionChecksCannotBeBypassedByBrowserProofs(t *testing.T) {
	c, r := fixture(sessionpolicy.User)
	c.Surface = sessionpolicy.Admin
	if Check(c, r, now) != "AUTHENTICATION_REQUIRED" {
		t.Fatal("USER session accepted on ADMIN")
	}
	c, r = fixture(sessionpolicy.User)
	c.Session.ExpiresAt = now
	if Check(c, r, now) != "AUTHENTICATION_REQUIRED" {
		t.Fatal("expired session accepted")
	}
	c, r = fixture(sessionpolicy.Admin)
	c.Session.MFAAt = time.Time{}
	if Check(c, r, now) != "MFA_REQUIRED" {
		t.Fatal("ADMIN without MFA accepted")
	}
}

func TestNoSafeOrUnknownMethodCanMutate(t *testing.T) {
	for _, method := range []string{"GET", "HEAD", "OPTIONS", "TRACE", "CONNECT", "", "post", "POST "} {
		c, r := fixture(sessionpolicy.User)
		r.Method = method
		if Check(c, r, now) != "VALIDATION_FAILED" {
			t.Fatal("unsupported mutation method accepted")
		}
	}
}

func TestInvalidTrustedOriginFailsClosed(t *testing.T) {
	for _, origin := range []string{"", "null", "*", "https://*.example.test", "http://example.test", "https:///", "https://user@example.test", "https://example.test/path", "https://example.test?", "https://example.test?q=x", "https://example.test#fragment", "https://exa mple.test", "https:example.test"} {
		c, r := fixture(sessionpolicy.User)
		c.Origin = origin
		r.Origins = []string{origin}
		if Check(c, r, now) != "INTERNAL_ERROR" {
			t.Fatalf("invalid origin configuration accepted: %q", origin)
		}
	}
}
