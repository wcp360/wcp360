package provisioning

import (
	"bytes"
	"encoding/binary"
	"encoding/json"
	"errors"
	"fmt"
	"strings"
	"testing"
)

func admissionRequest() Request {
	return Request{Schema: Schema, Operation: "provision_php_fpm",
		JobID:    "01900000-0000-7000-8000-000000000001",
		TenantID: "01900000-0000-7000-8000-000000000002",
		SiteID:   "01900000-0000-7000-8000-000000000003",
		Hostname: "admission.test", ExpectedRevision: 2}
}

func admissionJSON(t *testing.T, r Request) []byte {
	t.Helper()
	raw, err := json.Marshal(r)
	if err != nil {
		t.Fatal(err)
	}
	return raw
}

func assertNoCandidateHandler(t *testing.T, raw []byte) {
	t.Helper()
	calls := 0
	response := dispatchFrame(bytes.NewReader(raw), func(r Request) Response {
		calls++
		return Reply(r, "ACTIVE", false)
	})
	if calls != 0 {
		t.Fatalf("invalid frame reached handler %d times", calls)
	}
	if response != Reply(Request{}, "REJECTED", false) {
		t.Fatalf("refusal leaked identity or reported success: %#v", response)
	}
}

func TestDeniedCanonicalOperationsNeverReachCandidateHandler(t *testing.T) {
	// Exact twelve rows of OWNER-DECISION-SC-M01-PRIVILEGED-IPC-BINDINGS-2026-11-03.
	// These are negative inputs, not aliases or an IPC-B operation registry.
	denied := []string{
		"filesystem.directory.create", "filesystem.ownership.apply",
		"filesystem.permissions.apply", "filesystem.quota.apply",
		"nginx_vhost.validate", "nginx_vhost.apply", "nginx_vhost.remove", "nginx.reload",
		"php_pool.validate", "php_pool.apply", "php_pool.remove", "php_fpm.reload",
	}
	for index, name := range denied {
		for _, operation := range []string{name, fmt.Sprintf("IPC-WCP-%d", 1005+index)} {
			t.Run(operation, func(t *testing.T) {
				r := admissionRequest()
				r.Operation = operation
				if err := r.Validate(); err == nil {
					t.Fatal("forbidden operation validated")
				}
				assertNoCandidateHandler(t, admissionJSON(t, r))
			})
		}
	}
}

func TestCandidateAdmissionRejectsAliasesAndWildcardFallbacks(t *testing.T) {
	for _, operation := range []string{"service.reload", "servers.services.manage", "shell.exec",
		"nginx.*", "*", " provision_php_fpm", "provision_php_fpm ",
		"PROVISION_PHP_FPM", "provision_php_fpm\x00", "inspect_php_fpm/../provision_php_fpm"} {
		t.Run(operation, func(t *testing.T) {
			r := admissionRequest()
			r.Operation = operation
			assertNoCandidateHandler(t, admissionJSON(t, r))
		})
	}
}

func TestCandidateAdmissionRejectsForeignOrMalformedEnvelopes(t *testing.T) {
	valid := admissionJSON(t, admissionRequest())
	appendField := func(field string) []byte {
		return []byte(strings.TrimSuffix(string(valid), "}") + "," + field + "}")
	}
	framed := make([]byte, 4+len(valid))
	binary.BigEndian.PutUint32(framed, uint32(len(valid)))
	copy(framed[4:], valid)
	cases := map[string][]byte{
		"empty": nil, "oversized": []byte(strings.Repeat(" ", 4097)),
		"trailing-object":           append(append([]byte{}, valid...), []byte("{}")...),
		"duplicate-operation":       appendField(`"operation":"inspect_php_fpm"`),
		"escaped-duplicate":         appendField(`"oper\u0061tion":"inspect_php_fpm"`),
		"caller-permission":         appendField(`"permission_id":"servers.services.manage"`),
		"caller-origin":             appendField(`"origin_action_id":"applications.create"`),
		"canonical-operation-field": appendField(`"ipc_operation_id":"nginx.reload"`),
		"canonical-length-frame":    framed,
	}
	for name, raw := range cases {
		t.Run(name, func(t *testing.T) { assertNoCandidateHandler(t, raw) })
	}
	variants := map[string]func(*Request){
		"foreign-schema": func(r *Request) { r.Schema = "WCP360-IPC-B/1" },
		"stale-revision": func(r *Request) { r.ExpectedRevision = 1 },
		"bad-actor":      func(r *Request) { r.TenantID = "administrator" },
		"bad-site":       func(r *Request) { r.SiteID = "../../etc" },
		"non-test-host":  func(r *Request) { r.Hostname = "example.com" },
	}
	for name, mutate := range variants {
		t.Run(name, func(t *testing.T) {
			r := admissionRequest()
			mutate(&r)
			assertNoCandidateHandler(t, admissionJSON(t, r))
		})
	}
}

type failedAdmissionReader struct{}

func (failedAdmissionReader) Read([]byte) (int, error) { return 0, errors.New("read failed") }

func TestCandidateAdmissionReadFailureNeverReachesHandler(t *testing.T) {
	calls := 0
	reply := dispatchFrame(failedAdmissionReader{}, func(r Request) Response {
		calls++
		return Reply(r, "ACTIVE", false)
	})
	if calls != 0 || reply != Reply(Request{}, "REJECTED", false) {
		t.Fatal("read failure admitted")
	}
}

func TestCandidateAdmissionAllowsOnlyExistingValidatedOperations(t *testing.T) {
	for _, operation := range []string{"provision_php_fpm", "inspect_php_fpm"} {
		t.Run(operation, func(t *testing.T) {
			r := admissionRequest()
			r.Operation = operation
			calls := 0
			reply := dispatchFrame(bytes.NewReader(admissionJSON(t, r)), func(actual Request) Response {
				calls++
				if actual != r {
					t.Fatal("validated request changed before handler")
				}
				return Reply(actual, "ACTIVE", false)
			})
			if calls != 1 || reply != Reply(r, "ACTIVE", false) {
				t.Fatal("valid request not forwarded exactly once")
			}
		})
	}
}

func TestCandidateAdmissionWithoutHandlerFailsClosed(t *testing.T) {
	reply := dispatchFrame(bytes.NewReader(admissionJSON(t, admissionRequest())), nil)
	if reply != Reply(Request{}, "REJECTED", false) {
		t.Fatal("missing handler admitted")
	}
}
