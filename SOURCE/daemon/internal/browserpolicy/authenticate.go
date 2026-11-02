package browserpolicy

import (
	"context"
	"encoding/base64"
	"time"

	"github.com/yyogas/wcp360/daemon/internal/contracts"
	"github.com/yyogas/wcp360/daemon/internal/sessionpolicy"
)

// SessionStore is the trusted server adapter boundary, not a browser claim.
// Lookup must verify the secret against its persisted verifier in the selected
// surface-specific store and return current revocation/MFA/expiry facts. It must
// honor cancellation, never log the secret, and never persist the raw secret.
// Unknown credentials return found=false. No cache may delay revocation.
// The concrete verifier algorithm and PostgreSQL adapter remain unimplemented.
type SessionStore interface {
	Lookup(context.Context, sessionpolicy.Surface, [32]byte) (sessionpolicy.Session, bool, error)
}

// AuthenticateSession composes strict cookie decoding, authoritative lookup and
// session policy. Values contains ALL occurrences of the expected cookie name,
// selected using trusted surface configuration. The HTTP adapter must reject
// malformed Cookie headers and preserve duplicates, never select the first.
// No fallback to the other surface, URL, headers or browser storage is allowed.
// Success establishes session validity only, never action/resource permission.
// now must be a trusted current server time. This function does not update
// LastSeen or refresh a cookie; writes need a separate atomic persistence path.
func AuthenticateSession(ctx context.Context, store SessionStore, surface sessionpolicy.Surface, values []string, now time.Time) (sessionpolicy.Session, contracts.ErrorCode) {
	var zero sessionpolicy.Session
	if ctx == nil || store == nil || now.IsZero() {
		return zero, "INTERNAL_ERROR"
	}
	if _, ok := sessionpolicy.For(surface); !ok {
		return zero, "INTERNAL_ERROR"
	}
	if ctx.Err() != nil {
		return zero, "SERVICE_UNAVAILABLE"
	}
	if len(values) != 1 || len(values[0]) != 43 {
		return zero, "AUTHENTICATION_REQUIRED"
	}
	decoded, err := base64.RawURLEncoding.Strict().DecodeString(values[0])
	if err != nil || len(decoded) != 32 || base64.RawURLEncoding.EncodeToString(decoded) != values[0] {
		return zero, "AUTHENTICATION_REQUIRED"
	}
	var secret [32]byte
	copy(secret[:], decoded)
	if secret == ([32]byte{}) {
		return zero, "AUTHENTICATION_REQUIRED"
	}
	session, found, err := store.Lookup(ctx, surface, secret)
	if err != nil || ctx.Err() != nil {
		return zero, "SERVICE_UNAVAILABLE"
	}
	if !found {
		return zero, "AUTHENTICATION_REQUIRED"
	}
	if code := sessionpolicy.Validate(session, surface, now); code != "" {
		return zero, code
	}
	return session, ""
}
