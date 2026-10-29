package browserpolicy

import (
	"encoding/base64"
	"net/http"
	"strings"
	"testing"
	"time"

	"github.com/yyogas/wcp360/daemon/internal/sessionpolicy"
)

func parseCookie(t *testing.T, header string) *http.Cookie {
	t.Helper()
	response := &http.Response{Header: http.Header{"Set-Cookie": []string{header}}}
	cookies := response.Cookies()
	if len(cookies) != 1 {
		t.Fatal("expected exactly one parseable cookie")
	}
	c := cookies[0]
	if err := c.Valid(); err != nil {
		t.Fatal("invalid cookie")
	}
	if !c.Secure || !c.HttpOnly || c.SameSite != http.SameSiteStrictMode || c.Path != "/" || c.Domain != "" || len(c.Unparsed) != 0 {
		t.Fatal("cookie security attributes differ from baseline")
	}
	return c
}

func TestSessionCookieIsolationAndRoundTrip(t *testing.T) {
	var secret [32]byte
	for i := range secret {
		secret[i] = byte(i)
	}
	for _, surface := range []sessionpolicy.Surface{sessionpolicy.User, sessionpolicy.Admin} {
		header, code := SessionCookie(surface, secret)
		if code != "" {
			t.Fatal("valid secret rejected")
		}
		c := parseCookie(t, header)
		policy, _ := sessionpolicy.For(surface)
		if c.Name != policy.CookieName || !strings.HasPrefix(c.Name, "__Host-") {
			t.Fatal("wrong surface cookie")
		}
		decoded, err := base64.RawURLEncoding.DecodeString(c.Value)
		if err != nil || string(decoded) != string(secret[:]) {
			t.Fatal("opaque secret did not round trip")
		}
		if c.MaxAge != 0 || !c.Expires.IsZero() || strings.Contains(header, "Max-Age") || strings.Contains(header, "Domain") {
			t.Fatal("persistent or domain-scoped session cookie")
		}
	}
}

func TestCookieEncodingCannotInjectHeaders(t *testing.T) {
	// Exercise every possible byte value, including CR/LF, semicolons and quotes.
	for value := 0; value < 256; value++ {
		var secret [32]byte
		for i := range secret {
			secret[i] = byte(value)
		}
		secret[0] = 1
		header, code := SessionCookie(sessionpolicy.User, secret)
		if code != "" || strings.ContainsAny(header, "\r\n") {
			t.Fatal("unsafe header serialization")
		}
		c := parseCookie(t, header)
		if strings.ContainsAny(c.Value, ";,\"\\ \t") {
			t.Fatal("unsafe cookie value")
		}
	}
}

func TestCookieDeletionUsesSameScope(t *testing.T) {
	for _, surface := range []sessionpolicy.Surface{sessionpolicy.User, sessionpolicy.Admin} {
		header, code := ClearSessionCookie(surface)
		if code != "" {
			t.Fatal("known surface rejected")
		}
		c := parseCookie(t, header)
		policy, _ := sessionpolicy.For(surface)
		if c.Name != policy.CookieName || c.Value != "" || c.MaxAge != -1 || !c.Expires.Equal(time.Unix(0, 0)) {
			t.Fatal("cookie deletion failed to preserve scope or expiration")
		}
	}
}

func TestCookieErrorsNeverReturnPartialHeaders(t *testing.T) {
	for _, surface := range []sessionpolicy.Surface{"", "user", "WCP-ROOT"} {
		if header, code := SessionCookie(surface, [32]byte{1}); header != "" || code != "INTERNAL_ERROR" {
			t.Fatal("unknown surface accepted")
		}
		if header, code := ClearSessionCookie(surface); header != "" || code != "INTERNAL_ERROR" {
			t.Fatal("unknown deletion surface accepted")
		}
	}
	if header, code := SessionCookie(sessionpolicy.User, [32]byte{}); header != "" || code != "INTERNAL_ERROR" {
		t.Fatal("uninitialized secret accepted")
	}
}
