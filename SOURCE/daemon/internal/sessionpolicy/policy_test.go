package sessionpolicy

import (
	"crypto/sha1"
	"fmt"
	"os"
	"testing"
	"time"
)

var testNow = time.Date(2026, 10, 29, 12, 0, 0, 0, time.UTC)

func live(surface Surface) Session {
	return Session{Surface: surface, IssuedAt: testNow.Add(-time.Minute), LastSeen: testNow.Add(-time.Second), MFAAt: testNow.Add(-time.Minute)}
}

func TestSurfaceIsolation(t *testing.T) {
	for _, surface := range []Surface{User, Admin} {
		if got := Validate(live(surface), surface, testNow); got != "" {
			t.Fatalf("live session rejected: %s", got)
		}
		other := User
		if surface == User {
			other = Admin
		}
		if Validate(live(surface), other, testNow) != AuthenticationRequired {
			t.Fatal("cross-surface session accepted")
		}
	}
	if _, ok := For("SYSTEM"); ok {
		t.Fatal("unknown surface policy supplied")
	}
	if Validate(Session{}, User, testNow) != AuthenticationRequired || Validate(live(User), "", testNow) != AuthenticationRequired {
		t.Fatal("missing surface/session accepted")
	}
}

func TestCanonicalPolicies(t *testing.T) {
	u, _ := For(User)
	a, _ := For(Admin)
	if u.CookieName != "__Host-wcp_user_session" || a.CookieName != "__Host-wcp_admin_session" || u.CookieName == a.CookieName {
		t.Fatal("cookie namespaces diverged")
	}
	if u.Idle != 30*time.Minute || u.Absolute != 12*time.Hour || a.Idle != 15*time.Minute || a.Absolute != 8*time.Hour || !a.MandatoryMFA || u.MandatoryMFA {
		t.Fatal("canonical policy drift")
	}
}

func TestExpiryEdges(t *testing.T) {
	for _, surface := range []Surface{User, Admin} {
		p, _ := For(surface)
		for _, kind := range []string{"idle", "absolute"} {
			for _, delta := range []time.Duration{-time.Nanosecond, 0, time.Nanosecond} {
				s := live(surface)
				s.IssuedAt = testNow.Add(-p.Absolute + time.Minute)
				if kind == "idle" {
					s.LastSeen = testNow.Add(-p.Idle - delta)
				} else {
					s.IssuedAt = testNow.Add(-p.Absolute - delta)
				}
				got := Validate(s, surface, testNow)
				if (delta < 0 && got != "") || (delta >= 0 && got != AuthenticationRequired) {
					t.Fatalf("%s %s delta=%s got=%s", surface, kind, delta, got)
				}
			}
		}
	}
}

func TestRevocationAndClockAnomalies(t *testing.T) {
	for name, mutate := range map[string]func(*Session){
		"revoked":          func(s *Session) { s.Revoked = true },
		"issued-in-future": func(s *Session) { s.IssuedAt = testNow.Add(time.Second) },
		"seen-in-future":   func(s *Session) { s.LastSeen = testNow.Add(time.Second) },
		"seen-before-birth": func(s *Session) {
			s.LastSeen = s.IssuedAt.Add(-time.Second)
		},
	} {
		t.Run(name, func(t *testing.T) {
			s := live(User)
			mutate(&s)
			if Validate(s, User, testNow) != AuthenticationRequired {
				t.Fatal("invalid session accepted")
			}
		})
	}
	if Validate(live(User), User, time.Time{}) != AuthenticationRequired {
		t.Fatal("missing clock accepted")
	}
}

func TestMFARules(t *testing.T) {
	for _, surface := range []Surface{User, Admin} {
		s := live(surface)
		s.MFAAt = time.Time{}
		got := Validate(s, surface, testNow)
		if surface == Admin && got != MFARequired || surface == User && got != "" {
			t.Fatal("login MFA policy mismatch")
		}
		s.MFARequired = true
		if Validate(s, surface, testNow) != MFARequired {
			t.Fatal("account-required MFA ignored")
		}
		s.MFAAt = testNow.Add(time.Second)
		if RequireFreshMFA(s, surface, testNow) != MFARequired {
			t.Fatal("future MFA accepted")
		}
		p, _ := For(surface)
		s.MFAAt = testNow.Add(-p.FreshMFA)
		if RequireFreshMFA(s, surface, testNow) != "" {
			t.Fatal("exact canonical <= MFA boundary rejected")
		}
		s.MFAAt = s.MFAAt.Add(-time.Nanosecond)
		if RequireFreshMFA(s, surface, testNow) != MFARequired {
			t.Fatal("stale MFA accepted")
		}
	}
}

func TestPrivilegedChildBounds(t *testing.T) {
	parent := live(Admin)
	parent.IssuedAt = testNow.Add(-time.Hour)
	child := Privileged{IssuedAt: testNow.Add(-time.Minute), LastSeen: testNow.Add(-time.Second)}
	if ValidatePrivileged(parent, child, testNow) != "" {
		t.Fatal("valid child rejected")
	}
	for name, mutate := range map[string]func(*Privileged){
		"zero":     func(c *Privileged) { *c = Privileged{} },
		"revoked":  func(c *Privileged) { c.Revoked = true },
		"absolute": func(c *Privileged) { c.IssuedAt = testNow.Add(-15 * time.Minute) },
		"idle": func(c *Privileged) {
			c.IssuedAt = testNow.Add(-10 * time.Minute)
			c.LastSeen = testNow.Add(-5 * time.Minute)
		},
		"older-parent": func(c *Privileged) { c.IssuedAt = parent.IssuedAt.Add(-time.Second) },
		"future":       func(c *Privileged) { c.LastSeen = testNow.Add(time.Second) },
		"seen-before":  func(c *Privileged) { c.LastSeen = c.IssuedAt.Add(-time.Second) },
	} {
		t.Run(name, func(t *testing.T) {
			copy := child
			mutate(&copy)
			if ValidatePrivileged(parent, copy, testNow) != Reauthentication {
				t.Fatal("invalid child accepted")
			}
		})
	}
	if ValidatePrivileged(live(User), child, testNow) != AuthenticationRequired {
		t.Fatal("USER gained privileged child context")
	}
	parent.Revoked = true
	if ValidatePrivileged(parent, child, testNow) != AuthenticationRequired {
		t.Fatal("revoked parent retained privilege")
	}
}

func TestSecurityBaselineSourcePin(t *testing.T) {
	data, err := os.ReadFile("../../../../specifications/security/WCP360_Security_Baseline_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md")
	if err != nil {
		t.Fatal(err)
	}
	header := []byte(fmt.Sprintf("blob %d\x00", len(data)))
	if fmt.Sprintf("%x", sha1.Sum(append(header, data...))) != "6d74f2f363524511424f839ca7262143c2af95ab" {
		t.Fatal("Security baseline changed; review session policies before updating the pin")
	}
}
