package browserpolicy

import (
	"context"
	"time"

	"github.com/yyogas/wcp360/daemon/internal/contracts"
	"github.com/yyogas/wcp360/daemon/internal/sessionpolicy"
)

// BrowserSessionStore must authenticate the secret and read the current session
// and its synchronizer CSRF reference in ONE consistent server-side snapshot.
// All SessionStore requirements also apply. The CSRF value must belong to this
// exact session, never another session/account or an incoming browser value.
// Implementations must enforce surface isolation and honor cancellation.
type BrowserSessionStore interface {
	LookupBrowser(context.Context, sessionpolicy.Surface, [32]byte) (sessionpolicy.Session, string, bool, error)
}

type browserLookup struct {
	store BrowserSessionStore
	csrf  string
}

func (b *browserLookup) Lookup(ctx context.Context, surface sessionpolicy.Surface, secret [32]byte) (sessionpolicy.Session, bool, error) {
	session, csrf, found, err := b.store.LookupBrowser(ctx, surface, secret)
	b.csrf = csrf
	return session, found, err
}

// CheckSessionMutation composes cookie authentication and browser prerequisites.
// Origin and surface come from trusted configuration; clock is a trusted server
// clock, not request data. Cookie/header multiplicity must be preserved upstream.
// The clock is read again AFTER storage I/O so an expiry crossed during lookup
// cannot pass with a stale request-start timestamp. Backward clock movement fails
// closed. No token/reference is returned on failure. Success grants NO resource
// permission and performs no business mutation, LastSeen update or audit write.
// The eventual write transaction must recheck current authorization/revocation.
func CheckSessionMutation(ctx context.Context, store BrowserSessionStore, surface sessionpolicy.Surface, origin string, cookies []string, request Mutation, clock func() time.Time) (sessionpolicy.Session, contracts.ErrorCode) {
	var zero sessionpolicy.Session
	if store == nil || clock == nil || !validOrigin(origin) {
		return zero, "INTERNAL_ERROR"
	}
	start := clock()
	lookup := &browserLookup{store: store}
	session, code := AuthenticateSession(ctx, lookup, surface, cookies, start)
	if code != "" {
		return zero, code
	}
	current := clock()
	if current.IsZero() || current.Before(start) {
		return zero, "INTERNAL_ERROR"
	}
	if ctx.Err() != nil {
		return zero, "SERVICE_UNAVAILABLE"
	}
	c := Context{Surface: surface, Session: session, Origin: origin, CSRFReference: lookup.csrf}
	if code := Check(c, request, current); code != "" {
		return zero, code
	}
	return session, ""
}
