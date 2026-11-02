// Package consistency implements pure DB-contract predicates. These checks
// do not lock, persist, authorize, dispatch or replace transactional SQL CAS.
package consistency

import (
	"bytes"
	"time"

	"github.com/yyogas/wcp360/daemon/internal/contracts"
)

const maxCounter int64 = 1<<63 - 1

// NextRevision implements TX-REV-01 arithmetic. The persistence adapter must
// still atomically UPDATE WHERE revision=expected and check affected rows.
func NextRevision(current, expected int64) (int64, contracts.ErrorCode) {
	if current < 1 {
		return 0, "INTERNAL_ERROR"
	}
	if expected < 1 {
		return 0, "VALIDATION_FAILED"
	}
	if current != expected {
		return 0, "CONCURRENCY_CONFLICT"
	}
	if current == maxCounter {
		return 0, "INTERNAL_ERROR"
	}
	return current + 1, ""
}

// ID is an already-decoded UUID. Syntax/version validation and referenced-row
// existence belong to the boundary/persistence adapter, not these predicates.
type ID [16]byte

type Scope string

const (
	Global  Scope = "GLOBAL"
	Account Scope = "ACCOUNT"
	Tenant  Scope = "TENANT"
)

// Key preserves the canonical composite uniqueness. No string concatenation,
// case-folding, whitespace trimming or scope fallback is performed.
type Key struct {
	Scope     Scope
	AccountID ID
	TenantID  ID
	Namespace string
	OpaqueKey string
}

func (k Key) valid() bool {
	if k.Namespace == "" || k.OpaqueKey == "" {
		return false
	}
	zero := ID{}
	switch k.Scope {
	case Global:
		return k.AccountID == zero && k.TenantID == zero
	case Account:
		return k.AccountID != zero && k.TenantID == zero
	case Tenant:
		return k.AccountID == zero && k.TenantID != zero
	default:
		return false
	}
}

// SameRequest compares already-computed semantic fingerprints (TX-IDEMP-01).
// false with no error means different records must be acquired independently.
// true means same logical request only, NOT permission to execute/replay it.
// RUNNING/UNKNOWN_OUTCOME/retention/reconciliation need durable state checks.
// No fingerprint algorithm or request canonicalization is invented here.
func SameRequest(stored, incoming Key, storedFingerprint, incomingFingerprint []byte) (bool, contracts.ErrorCode) {
	if !stored.valid() || !incoming.valid() || len(storedFingerprint) == 0 || len(incomingFingerprint) == 0 {
		return false, "VALIDATION_FAILED"
	}
	if stored != incoming {
		return false, ""
	}
	if !bytes.Equal(storedFingerprint, incomingFingerprint) {
		return false, "IDEMPOTENCY_CONFLICT"
	}
	return true, ""
}

type TargetKind string

const (
	Job      TargetKind = "JOB"
	Resource TargetKind = "RESOURCE"
)

type Target struct {
	Kind       TargetKind
	JobID      ID
	ResourceID ID
}

func (t Target) valid() bool {
	switch t.Kind {
	case Job:
		return t.JobID != (ID{}) && t.ResourceID == (ID{})
	case Resource:
		return t.JobID == (ID{}) && t.ResourceID != (ID{})
	default:
		return false
	}
}

// Lease is a snapshot read from authoritative persistence. It is not a bearer
// credential. Reclaim/write must compare the row atomically in the database.
type Lease struct {
	ID       ID
	Target   Target
	Worker   string
	Fence    int64
	Acquired time.Time
	Expires  time.Time
}

func (l Lease) valid() bool {
	return l.ID != (ID{}) && l.Target.valid() && l.Worker != "" && l.Fence >= 1 && !l.Acquired.IsZero() && l.Expires.After(l.Acquired)
}

// CheckLeaseWrite refuses stale worker/target/lease/token identities and expired
// leases, including exact expiry. The current row must be rechecked in the
// SAME transaction as the state/result write to avoid a check/use race.
func CheckLeaseWrite(current, claimed Lease, now time.Time) contracts.ErrorCode {
	if !current.valid() || !claimed.valid() || now.IsZero() {
		return "VALIDATION_FAILED"
	}
	if current.ID != claimed.ID || current.Target != claimed.Target || current.Worker != claimed.Worker || current.Fence != claimed.Fence {
		return "CONCURRENCY_CONFLICT"
	}
	// Use authoritative timestamps only: a claimed expiry cannot extend a lease.
	if now.Before(current.Acquired) || !now.Before(current.Expires) {
		return "CONCURRENCY_CONFLICT"
	}
	return ""
}

// NextFence produces the strictly increasing token for an expired lease.
// The caller must atomically replace owner/timestamps/token under row locking;
// returning a token here neither acquires a lease nor authorizes execution.
func NextFence(current Lease, now time.Time) (int64, contracts.ErrorCode) {
	if !current.valid() || now.IsZero() {
		return 0, "VALIDATION_FAILED"
	}
	if now.Before(current.Expires) {
		return 0, "CONCURRENCY_CONFLICT"
	}
	if current.Fence == maxCounter {
		return 0, "INTERNAL_ERROR"
	}
	return current.Fence + 1, ""
}
