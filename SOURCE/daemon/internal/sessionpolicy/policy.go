// Package sessionpolicy checks canonical session lifetime and surface rules.
// It consumes server-loaded facts, not request claims, and grants no resource
// authorization. Persistence, token verification, CSRF and revocation lookup
// must be supplied by the eventual authenticated transport.
package sessionpolicy

import (
	"time"

	"github.com/yyogas/wcp360/daemon/internal/contracts"
)

type Surface string

const (
	User  Surface = "WCP-USER"
	Admin Surface = "WCP-ADMIN"
)

type Failure = contracts.ErrorCode

const (
	AuthenticationRequired Failure = "AUTHENTICATION_REQUIRED"
	MFARequired            Failure = "MFA_REQUIRED"
	Reauthentication       Failure = "REAUTHENTICATION_REQUIRED"
)

type Policy struct {
	CookieName       string
	Idle             time.Duration
	Absolute         time.Duration
	FreshMFA         time.Duration
	MandatoryMFA     bool
	PrivilegedIdle   time.Duration
	PrivilegedExpiry time.Duration
}

func For(surface Surface) (Policy, bool) {
	switch surface {
	case User:
		return Policy{CookieName: "__Host-wcp_user_session", Idle: 30 * time.Minute, Absolute: 12 * time.Hour, FreshMFA: 10 * time.Minute}, true
	case Admin:
		return Policy{CookieName: "__Host-wcp_admin_session", Idle: 15 * time.Minute, Absolute: 8 * time.Hour, FreshMFA: 5 * time.Minute, MandatoryMFA: true, PrivilegedIdle: 5 * time.Minute, PrivilegedExpiry: 15 * time.Minute}, true
	default:
		return Policy{}, false
	}
}

// Session must be loaded from the appropriate surface-specific server store.
// Reference identifies the persisted session row, never a raw secret/token.
// ExpiresAt is the authoritative stored expiry and may shorten policy limits.
// These are timestamps, not durations supplied by a browser. Validate never
// updates LastSeen: persistence must not revive an expired session.
type Session struct {
	Reference   [16]byte
	Surface     Surface
	IssuedAt    time.Time
	ExpiresAt   time.Time
	LastSeen    time.Time
	MFAAt       time.Time
	MFARequired bool
	Revoked     bool
}

func Validate(s Session, expected Surface, now time.Time) Failure {
	p, ok := For(expected)
	if !ok || s.Reference == ([16]byte{}) || s.Surface != expected || s.Revoked || now.IsZero() || s.IssuedAt.IsZero() || s.LastSeen.IsZero() {
		return AuthenticationRequired
	}
	if s.IssuedAt.After(now) || s.LastSeen.After(now) || s.LastSeen.Before(s.IssuedAt) {
		return AuthenticationRequired
	}
	if !s.ExpiresAt.After(s.IssuedAt) || !now.Before(s.ExpiresAt) {
		return AuthenticationRequired
	}
	if now.Sub(s.IssuedAt) >= p.Absolute || now.Sub(s.LastSeen) >= p.Idle {
		return AuthenticationRequired
	}
	if !s.MFAAt.IsZero() && s.MFAAt.After(now) {
		return MFARequired
	}
	if (p.MandatoryMFA || s.MFARequired) && s.MFAAt.IsZero() {
		return MFARequired
	}
	return ""
}

// RequireFreshMFA is an additional high-risk gate. Equality at the published
// <= freshness boundary is accepted; session expiration itself is exclusive.
func RequireFreshMFA(s Session, expected Surface, now time.Time) Failure {
	if failure := Validate(s, expected, now); failure != "" {
		return failure
	}
	p, _ := For(expected)
	if s.MFAAt.IsZero() || now.Sub(s.MFAAt) > p.FreshMFA {
		return MFARequired
	}
	return ""
}

// Privileged is a child of a still-valid ADMIN session. This check does not
// create privilege, extend expiry or replace action-specific authorization.
type Privileged struct {
	ParentReference [16]byte
	IssuedAt        time.Time
	LastSeen        time.Time
	Revoked         bool
}

func ValidatePrivileged(parent Session, child Privileged, now time.Time) Failure {
	if failure := RequireFreshMFA(parent, Admin, now); failure != "" {
		return failure
	}
	p, _ := For(Admin)
	if child.ParentReference != parent.Reference || child.Revoked || child.IssuedAt.IsZero() || child.LastSeen.IsZero() || child.IssuedAt.Before(parent.IssuedAt) || child.IssuedAt.After(now) || child.LastSeen.Before(child.IssuedAt) || child.LastSeen.After(now) {
		return Reauthentication
	}
	if now.Sub(child.IssuedAt) >= p.PrivilegedExpiry || now.Sub(child.LastSeen) >= p.PrivilegedIdle {
		return Reauthentication
	}
	return ""
}
