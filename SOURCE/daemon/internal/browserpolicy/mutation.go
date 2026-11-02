// Package browserpolicy implements pure browser mutation prerequisites.
// It opens no listener and does not replace action/resource authorization.
package browserpolicy

import (
	"crypto/subtle"
	"net/url"
	"strings"
	"time"

	"github.com/yyogas/wcp360/daemon/internal/contracts"
	"github.com/yyogas/wcp360/daemon/internal/sessionpolicy"
)

// Context comes from trusted surface configuration and server-side session
// lookup; Origin must never come from an untrusted Host/forwarded header.
// CSRFReference is the synchronizer token bound to that same session,
// never a second client-supplied header or cookie.
type Context struct {
	Surface       sessionpolicy.Surface
	Session       sessionpolicy.Session
	Origin        string
	CSRFReference string
}

// Mutation holds raw single-header values as supplied by the HTTP adapter.
// Do not join duplicates or select the first value: duplicates must be denied.
type Mutation struct {
	Method     string
	Origins    []string
	CSRFTokens []string
}

// Check returns a canonical error only, never echoes tokens or Origin values.
// Success means browser prerequisites passed, NOT permission to mutate. The
// caller must then enforce permission, resource, tenant, entitlement and audit.
func Check(c Context, request Mutation, now time.Time) contracts.ErrorCode {
	if failure := sessionpolicy.Validate(c.Session, c.Surface, now); failure != "" {
		return failure
	}
	switch request.Method {
	case "POST", "PUT", "PATCH", "DELETE":
	default:
		return "VALIDATION_FAILED"
	}
	if !validOrigin(c.Origin) {
		return "INTERNAL_ERROR"
	}
	if len(request.Origins) != 1 || request.Origins[0] != c.Origin {
		return "AUTHORIZATION_DENIED"
	}
	if c.CSRFReference == "" || len(request.CSRFTokens) != 1 || request.CSRFTokens[0] == "" {
		return "AUTHORIZATION_DENIED"
	}
	if subtle.ConstantTimeCompare([]byte(c.CSRFReference), []byte(request.CSRFTokens[0])) != 1 {
		return "AUTHORIZATION_DENIED"
	}
	return ""
}

// Only an explicit HTTPS origin is accepted as trusted configuration. No
// canonicalization is applied to incoming values; matching is byte-for-byte.
func validOrigin(origin string) bool {
	u, err := url.Parse(origin)
	return err == nil && u.Scheme == "https" && u.Host != "" && u.Hostname() != "" && !strings.ContainsAny(u.Host, "*%\\ \t\r\n") && u.User == nil && u.Opaque == "" && u.Path == "" && u.RawQuery == "" && !u.ForceQuery && u.Fragment == "" && u.RawFragment == "" && u.String() == origin
}
