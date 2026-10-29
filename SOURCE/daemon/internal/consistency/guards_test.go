package consistency

import (
	"crypto/sha1"
	"fmt"
	"os"
	"testing"
	"time"

	"github.com/yyogas/wcp360/daemon/internal/contracts"
)

var now = time.Date(2026, 10, 29, 12, 0, 0, 0, time.UTC)

func TestRevisionConflictAndOverflow(t *testing.T) {
	for _, tc := range []struct {
		current  int64
		expected int64
		next     int64
		code     contracts.ErrorCode
	}{
		{1, 1, 2, ""},
		{maxCounter - 1, maxCounter - 1, maxCounter, ""},
		{2, 1, 0, "CONCURRENCY_CONFLICT"},
		{1, 2, 0, "CONCURRENCY_CONFLICT"},
		{0, 1, 0, "INTERNAL_ERROR"},
		{-1, 1, 0, "INTERNAL_ERROR"},
		{1, 0, 0, "VALIDATION_FAILED"},
		{1, -1, 0, "VALIDATION_FAILED"},
		{maxCounter, maxCounter, 0, "INTERNAL_ERROR"},
	} {
		next, code := NextRevision(tc.current, tc.expected)
		if next != tc.next || code != tc.code {
			t.Fatalf("revision %d/%d: got %d %s", tc.current, tc.expected, next, code)
		}
	}
	// Two writers with the same snapshot: after the first commits revision 2,
	// the second's expected revision 1 must fail. This is not a live DB race.
	first, _ := NextRevision(1, 1)
	if _, code := NextRevision(first, 1); code != "CONCURRENCY_CONFLICT" {
		t.Fatal("lost-update snapshot accepted")
	}
}

func key(scope Scope) Key {
	k := Key{Scope: scope, Namespace: "applications.create", OpaqueKey: "opaque:key"}
	if scope == Account {
		k.AccountID = ID{1}
	}
	if scope == Tenant {
		k.TenantID = ID{1}
	}
	return k
}

func TestIdempotencyFingerprintConflict(t *testing.T) {
	for _, scope := range []Scope{Global, Account, Tenant} {
		k := key(scope)
		same, code := SameRequest(k, k, []byte{1, 2}, []byte{1, 2})
		if !same || code != "" {
			t.Fatalf("same request rejected: %s", scope)
		}
		for _, different := range [][]byte{{1, 3}, {1, 2, 3}} {
			if same, code := SameRequest(k, k, []byte{1, 2}, different); same || code != "IDEMPOTENCY_CONFLICT" {
				t.Fatal("different semantic request did not conflict")
			}
		}
	}
}

func TestScopeAndOpaqueKeyIsolation(t *testing.T) {
	for _, pair := range [][2]Key{
		{key(Global), key(Account)},
		{key(Account), key(Tenant)},
		{key(Tenant), {Scope: Tenant, TenantID: ID{2}, Namespace: "applications.create", OpaqueKey: "opaque:key"}},
		{key(Account), {Scope: Account, AccountID: ID{2}, Namespace: "applications.create", OpaqueKey: "opaque:key"}},
		{key(Global), {Scope: Global, Namespace: "applications.delete", OpaqueKey: "opaque:key"}},
		{key(Global), {Scope: Global, Namespace: "applications.create", OpaqueKey: "Opaque:key"}},
		{key(Global), {Scope: Global, Namespace: "applications.create", OpaqueKey: "opaque:key "}},
		{{Scope: Global, Namespace: "ab", OpaqueKey: "c"}, {Scope: Global, Namespace: "a", OpaqueKey: "bc"}},
	} {
		if same, code := SameRequest(pair[0], pair[1], []byte{1}, []byte{2}); same || code != "" {
			t.Fatal("independent scope/key collided")
		}
	}
}

func TestInvalidIdempotencyScopeRejected(t *testing.T) {
	for _, invalid := range []Key{
		{},
		{Scope: "UNKNOWN", Namespace: "a", OpaqueKey: "k"},
		{Scope: Global, AccountID: ID{1}, Namespace: "a", OpaqueKey: "k"},
		{Scope: Global, TenantID: ID{1}, Namespace: "a", OpaqueKey: "k"},
		{Scope: Account, Namespace: "a", OpaqueKey: "k"},
		{Scope: Tenant, Namespace: "a", OpaqueKey: "k"},
		{Scope: Tenant, TenantID: ID{1}, AccountID: ID{2}, Namespace: "a", OpaqueKey: "k"},
		{Scope: Account, TenantID: ID{1}, AccountID: ID{2}, Namespace: "a", OpaqueKey: "k"},
		{Scope: Global, Namespace: "a"},
		{Scope: Global, OpaqueKey: "k"},
	} {
		if same, code := SameRequest(invalid, key(Global), []byte{1}, []byte{1}); same || code != "VALIDATION_FAILED" {
			t.Fatal("invalid scope accepted")
		}
	}
	for _, fingerprints := range [][2][]byte{{nil, {1}}, {{1}, nil}, {nil, nil}} {
		if same, code := SameRequest(key(Global), key(Global), fingerprints[0], fingerprints[1]); same || code != "VALIDATION_FAILED" {
			t.Fatal("empty fingerprint accepted")
		}
	}
}

func lease() Lease {
	return Lease{ID: ID{3}, Target: Target{Kind: Job, JobID: ID{1}}, Worker: "worker-1", Fence: 1, Acquired: now.Add(-time.Minute), Expires: now.Add(time.Minute)}
}

func TestLeaseWriteAndExactExpiry(t *testing.T) {
	l := lease()
	for _, instant := range []time.Time{l.Acquired, now, l.Expires.Add(-time.Nanosecond)} {
		if code := CheckLeaseWrite(l, l, instant); code != "" {
			t.Fatalf("valid lease rejected: %s", code)
		}
	}
	for _, instant := range []time.Time{l.Acquired.Add(-time.Nanosecond), l.Expires, l.Expires.Add(time.Nanosecond)} {
		if CheckLeaseWrite(l, l, instant) != "CONCURRENCY_CONFLICT" {
			t.Fatal("expired/future lease accepted")
		}
	}
	claimed := l
	claimed.Expires = l.Expires.Add(time.Hour)
	if CheckLeaseWrite(l, claimed, l.Expires) != "CONCURRENCY_CONFLICT" {
		t.Fatal("claimed expiry extended authority")
	}
}

func TestEveryLeaseIdentityIsBound(t *testing.T) {
	for _, mutate := range []func(*Lease){
		func(l *Lease) { l.ID = ID{9} },
		func(l *Lease) { l.Target.JobID = ID{9} },
		func(l *Lease) { l.Target = Target{Kind: Resource, ResourceID: ID{1}} },
		func(l *Lease) { l.Worker = "worker-2" },
		func(l *Lease) { l.Fence++ },
	} {
		current, claimed := lease(), lease()
		mutate(&claimed)
		if CheckLeaseWrite(current, claimed, now) != "CONCURRENCY_CONFLICT" {
			t.Fatal("lease identity mismatch accepted")
		}
	}
}

func TestInvalidLeaseSnapshots(t *testing.T) {
	for _, mutate := range []func(*Lease){
		func(l *Lease) { l.ID = ID{} },
		func(l *Lease) { l.Worker = "" },
		func(l *Lease) { l.Fence = 0 },
		func(l *Lease) { l.Acquired = time.Time{} },
		func(l *Lease) { l.Expires = l.Acquired },
		func(l *Lease) { l.Target.Kind = "UNKNOWN" },
		func(l *Lease) { l.Target.JobID = ID{} },
		func(l *Lease) { l.Target.ResourceID = ID{2} },
		func(l *Lease) { l.Target = Target{Kind: Resource, JobID: ID{1}, ResourceID: ID{2}} },
	} {
		bad := lease()
		mutate(&bad)
		if CheckLeaseWrite(bad, lease(), now) != "VALIDATION_FAILED" || CheckLeaseWrite(lease(), bad, now) != "VALIDATION_FAILED" {
			t.Fatal("invalid lease accepted")
		}
		if _, code := NextFence(bad, now); code != "VALIDATION_FAILED" {
			t.Fatal("invalid lease reclaimed")
		}
	}
	if CheckLeaseWrite(lease(), lease(), time.Time{}) != "VALIDATION_FAILED" {
		t.Fatal("missing clock accepted")
	}
}

func TestReclaimRejectsStaleWorkerAndOverflow(t *testing.T) {
	old := lease()
	if _, code := NextFence(old, old.Expires.Add(-time.Nanosecond)); code != "CONCURRENCY_CONFLICT" {
		t.Fatal("live lease reclaimed")
	}
	next, code := NextFence(old, old.Expires)
	if code != "" || next != old.Fence+1 {
		t.Fatal("exact expiry did not advance fencing token")
	}
	current := old
	current.Fence = next
	current.Worker = "worker-2"
	current.Acquired = old.Expires
	current.Expires = current.Acquired.Add(time.Minute)
	if CheckLeaseWrite(current, old, current.Acquired) != "CONCURRENCY_CONFLICT" {
		t.Fatal("stale worker overwrote reclaimed lease")
	}
	if CheckLeaseWrite(current, current, current.Acquired) != "" {
		t.Fatal("new worker rejected")
	}
	current.Fence = maxCounter
	if next, code := NextFence(current, current.Expires); next != 0 || code != "INTERNAL_ERROR" {
		t.Fatal("fencing token wrapped")
	}
	if _, code := NextFence(old, time.Time{}); code != "VALIDATION_FAILED" {
		t.Fatal("missing clock reclaimed lease")
	}
}

func TestDatabaseContractSourcePin(t *testing.T) {
	data, err := os.ReadFile("../../../../specifications/database/WCP360_DB_Model_v1.0_Canonical_Implementation_Contract.md")
	if err != nil {
		t.Fatal(err)
	}
	header := []byte(fmt.Sprintf("blob %d\x00", len(data)))
	if fmt.Sprintf("%x", sha1.Sum(append(header, data...))) != "2660e057bad1d00592c57f354b1f3745ccccbe7d" {
		t.Fatal("Database contract changed; review consistency guards before updating source pin")
	}
}
