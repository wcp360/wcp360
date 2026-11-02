package browserpolicy

import (
	"encoding/base64"

	"github.com/yyogas/wcp360/daemon/internal/contracts"
	"github.com/yyogas/wcp360/daemon/internal/sessionpolicy"
)

const cookieAttributes = "; Path=/; Secure; HttpOnly; SameSite=Strict"

// SessionCookie renders one Set-Cookie value for a server-issued opaque secret.
// The caller MUST generate all 32 bytes using a cryptographic random source,
// persist only its verifier and bind it to the selected surface before sending.
// A nonzero value alone does not prove entropy. This function neither generates
// a token nor creates/rotates a session. Its returned header contains a secret:
// never log it. No persistent-cookie lifetime or Domain override is exposed.
func SessionCookie(surface sessionpolicy.Surface, secret [32]byte) (string, contracts.ErrorCode) {
	policy, ok := sessionpolicy.For(surface)
	if !ok || secret == ([32]byte{}) {
		return "", "INTERNAL_ERROR"
	}
	return policy.CookieName + "=" + base64.RawURLEncoding.EncodeToString(secret[:]) + cookieAttributes, ""
}

// ClearSessionCookie expires only the selected surface's host-only cookie.
// It does not revoke the server session, CSRF state, long-lived channels or
// ADMIN child contexts; the logout adapter must revoke those independently.
func ClearSessionCookie(surface sessionpolicy.Surface) (string, contracts.ErrorCode) {
	policy, ok := sessionpolicy.For(surface)
	if !ok {
		return "", "INTERNAL_ERROR"
	}
	return policy.CookieName + "=" + cookieAttributes + "; Max-Age=0; Expires=Thu, 01 Jan 1970 00:00:00 GMT", ""
}
