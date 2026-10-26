package ipcbframe

import (
	"crypto/sha1"
	"encoding/binary"
	"encoding/json"
	"fmt"
	"os"
	"path/filepath"
	"regexp"
	"sort"
	"strings"
	"testing"
)

const canonSourcePath = "../../../specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md"

func fixture() map[string]any {
	return map[string]any{
		"protocol_version": ProtocolVersion,
		"op_version":      "1.0", // opaque research value; owner to ratify
		"request_id":      "11111111-1111-4111-8111-111111111111",
		"correlation_id":  "22222222-2222-4222-8222-222222222222",
		"action_id":       "nginx_vhost.validate",
		"ipc_operation_id": "nginx_vhost.validate",
		"resource_type":  "application_revision",
		"resource_id":    "33333333-3333-4333-8333-333333333333",
		"authorization_context_ref": "44444444-4444-4444-8444-444444444444",
		"deadline_ms":    5000, // opaque test fixture; not a normative deadline representation
		"payload": map[string]any{
			"application_id": "33333333-3333-4333-8333-333333333333",
			"vhost_revision_id": "55555555-5555-4555-8555-555555555555",
		},
	}
}

func framed(raw []byte) []byte {
	b := make([]byte, 4+len(raw))
	binary.BigEndian.PutUint32(b[:4], uint32(len(raw)))
	copy(b[4:], raw)
	return b
}

func fixtureFrame(t *testing.T, data map[string]any) []byte {
	t.Helper()
	raw, err := json.Marshal(data)
	if err != nil {
		t.Fatal(err)
	}
	return framed(raw)
}

func expectReject(t *testing.T, data []byte, phrase string) {
	t.Helper()
	_, err := DecodeRequestFrame(data)
	if err == nil {
		t.Fatal("expected error but data was accepted")
	}
	if !strings.Contains(err.Error(), phrase) {
		t.Fatalf("expected %q, got %q", phrase, err)
	}
}

func TestInertHappyPathParsesDataNotAuthorization(t *testing.T) {
	data, err := DecodeRequestFrame(fixtureFrame(t, fixture()))
	if err != nil {
		t.Fatalf("safe syntactic fixture rejected: %v", err)
	}
	if data.Operation.OperationID != "nginx_vhost.validate" ||
		data.Operation.ContractID != "IPC-WCP-1009" ||
		data.Operation.TestID != "TEST-IPC-1009" {
		t.Fatalf("unexpected source-bound reference: %+v", data)
	}
	if data.AuthorizationContextRef == "" || data.ResourceID == "" {
		t.Fatal("opaque references missing")
	}
	// This parser intentionally DOES NOT verify peer credentials or
	// authenticated authorization-context freshness/permissions.
}

func TestSyntheticContextIsNotAnAuthorizationCheck(t *testing.T) {
	req := fixture()
	req["authorization_context_ref"] = "fake-non-empty-reference"
	parsed, err := DecodeRequestFrame(fixtureFrame(t, req))
	if err != nil {
		t.Fatalf("syntactic parser should not pretend to resolve context: %v", err)
	}
	if parsed.AuthorizationContextRef != "fake-non-empty-reference" {
		t.Fatal("context reference not retained")
	}
	// A production dispatcher MUST reject invalid/forged context elsewhere.
}

func TestManifestMatchesSourceBlobAndAll47IDs(t *testing.T) {
	catalog, err := loadAllowed()
	if err != nil {
		t.Fatal(err)
	}
	raw, err := os.ReadFile(canonSourcePath)
	if err != nil {
		t.Fatal(err)
	}
	var manifest researchCatalog
	if err := json.Unmarshal(operationsJSON, &manifest); err != nil {
		t.Fatal(err)
	}
	gitInput := append([]byte(fmt.Sprintf("blob %d\x00", len(raw))), raw...)
	actualSHA := fmt.Sprintf("%x", sha1.Sum(gitInput))
	if manifest.SourceGitBlobSHA != actualSHA {
		t.Fatalf("IPC-B source blob drift: manifest %s != actual %s", manifest.SourceGitBlobSHA, actualSHA)
	}
	const heading = "## 6. Complete 47-operation registry"
	i := strings.Index(string(raw), heading)
	if i < 0 {
		t.Fatal("canonical 47-operation table missing")
	}
	section := string(raw[i+len(heading):])
	if next := strings.Index(section, "\n## "); next >= 0 {
		section = section[:next]
	}
	re := regexp.MustCompile("^\\| \x60(IPC-WCP-[0-9]{4})\x60 \\|")
	seen := make(map[string]bool)
	for _, line := range strings.Split(section, "\n") {
		if !re.MatchString(line) {
			continue
		}
		cells := strings.Split(line, "|")
		if len(cells) != 14 {
			t.Fatalf("canonical IPC row has wrong width: %q", line)
		}
		contractID := strings.Trim(strings.TrimSpace(cells[1]), "\x60")
		op := strings.Trim(strings.TrimSpace(cells[4]), "\x60")
		if !typedID.MatchString(op) {
			t.Fatalf("bad source operation ID %q", op)
		}
		got, exists := catalog[op]
		if !exists || got.ContractID != contractID {
			t.Fatalf("missing/incorrect source operation %s/%s", op, contractID)
		}
		seen[op] = true
	}
	if len(seen) != 47 || len(catalog) != 47 {
		t.Fatalf("canonical source/manifest count = %d/%d", len(seen), len(catalog))
	}
}

func TestCatalogDoesNotContainArbitraryShellPrimitives(t *testing.T) {
	catalog, err := loadAllowed()
	if err != nil {
		t.Fatal(err)
	}
	for id := range catalog {
		if strings.Contains(id, "shell") || strings.Contains(id, "arbitrary") ||
			strings.Contains(id, "sudo") || strings.Contains(id, "systemctl") {
			t.Fatalf("generic privileged operation %q unexpectedly present", id)
		}
	}
}

func TestNoExecutionOrSocketImports(t *testing.T) {
	b, err := os.ReadFile("codec.go")
	if err != nil {
		t.Fatal(err)
	}
	for _, forbidden := range []string{
		"\"os/exec\"", "\"syscall\"", "\"net\"", "\"net/http\"",
		"\"os\"", "\"os/user\"", "\"golang.org/x/sys/unix\"",
	} {
		if strings.Contains(string(b), forbidden) {
			t.Fatalf("inert codec imports operational package %q", forbidden)
		}
	}
	// No network, socket, privileged dispatch or filesystem API is present.
}

func TestMalformedFramesFailClosed(t *testing.T) {
	raw := fixtureFrame(t, fixture())
	cases := []struct{name string; frame []byte; text string}{
		{"nil", nil, "missing frame length"},
		{"short-header", []byte{0, 0, 0}, "missing frame length"},
		{"zero-length", []byte{0, 0, 0, 0}, "invalid request frame length"},
		{"too-large", []byte{0, 1, 0, 1}, "invalid request frame length"},
		{"truncated", raw[:len(raw)-1], "truncated or trailing request frame"},
		{"trailing", append(append([]byte{}, raw...), byte(1)), "truncated or trailing request frame"},
		{"invalid-utf8", framed([]byte{0xff}), "invalid UTF-8 request"},
		{"invalid-json", framed([]byte("{")), "malformed JSON token"},
		{"json-array", framed([]byte("[]")), "request must be JSON object"},
		{"json-null", framed([]byte("null")), "request must be JSON object"},
		{"multiple-objects", framed([]byte("{} {}")), "trailing JSON"},
		{"nan", framed([]byte("{\"x\":NaN}")), "malformed JSON token"},
	}
	for _, tt := range cases {
		t.Run(tt.name, func(t *testing.T) { expectReject(t, tt.frame, tt.text) })
	}
}

func TestDuplicateJSONKeysRejectedAtEveryDepth(t *testing.T) {
	cases := [][]byte{
		[]byte("{\"protocol_version\":1,\"protocol_version\":2}"),
		[]byte("{\"payload\":{\"a\":1,\"a\":2}}"),
		[]byte("{\"payload\":{\"a\":{\"role\":1,\"role\":2}}}"),
		[]byte("{\"payload\":{\"name\":1,\"\\u006eame\":2}}"),
	}
	for _, data := range cases {
		expectReject(t, framed(data), "duplicate JSON object key")
	}
}

func TestTopLevelUnknownAndMissingFieldsRejected(t *testing.T) {
	t.Run("unknown", func(t *testing.T) {
		req := fixture()
		req["new_field"] = true
		expectReject(t, fixtureFrame(t, req), "unknown request field")
	})
	t.Run("missing", func(t *testing.T) {
		req := fixture()
		delete(req, "authorization_context_ref")
		expectReject(t, fixtureFrame(t, req), "required request field missing")
	})
}

func TestWrongProtocolAndOperationRejected(t *testing.T) {
	req := fixture()
	req["protocol_version"] = "WCP360-CONTROL/1"
	expectReject(t, fixtureFrame(t, req), "unsupported IPC-B protocol version")
	req = fixture()
	req["ipc_operation_id"] = "shell.exec"
	expectReject(t, fixtureFrame(t, req), "unknown IPC-B operation")
	req = fixture()
	req["ipc_operation_id"] = "made.up"
	expectReject(t, fixtureFrame(t, req), "unknown IPC-B operation")
}

func TestOpaqueFieldsCannotBeMissingOrNull(t *testing.T) {
	for _, field := range []string{"op_version", "deadline_ms"} {
		req := fixture()
		req[field] = nil
		expectReject(t, fixtureFrame(t, req), "required opaque version/deadline field missing")
	}
}

func TestBrokenRequiredTypesRejected(t *testing.T) {
	for _, tt := range []struct{field string; value any; errorText string}{
		{"request_id", 123, "missing/invalid string"},
		{"correlation_id", "", "missing/invalid string"},
		{"authorization_context_ref", nil, "missing/invalid string"},
		{"resource_type", "bad/executable/path", "invalid typed IPC-B identifier"},
		{"action_id", "FOO", "invalid typed IPC-B identifier"},
		{"resource_id", "\x00", "missing/invalid string"},
		{"payload", []any{1,2}, "payload must be object"},
		{"payload", nil, "payload must be object"},
		{"job_id", 9, "optional job_id must be nonempty string"},
	} {
		t.Run(tt.field, func(t *testing.T) {
			req := fixture()
			req[tt.field] = tt.value
			expectReject(t, fixtureFrame(t, req), tt.errorText)
		})
	}
}

func TestForbiddenCallerFieldsRejectedRecursively(t *testing.T) {
	for _, key := range []string{
		"shell", "command", "executable", "argv", "env",
		"sudo", "systemctl", "service_name", "raw_sql", "signal",
		"private_key", "password", "cookie", "token", "role",
		"permission_id", "target_path",
	} {
		t.Run(key, func(t *testing.T) {
			req := fixture()
			req["payload"] = map[string]any{"nested": map[string]any{
				key: "caller-controlled",
			}}
			expectReject(t, fixtureFrame(t, req), "forbidden caller-controlled field")
		})
	}
}

func TestInvalidCatalogIsRejected(t *testing.T) {
	raw := operationsJSON
	defer func(){ operationsJSON = raw }()
	operationsJSON = []byte(`{"status":"DERIVED_RESEARCH_ONLY_NONCANONICAL","source_contract_version":"WCP360-IPC-B/1","operations":[]}`)
	if _, err := loadAllowed(); err == nil {
		t.Fatal("empty catalog erroneously accepted")
	}
}

func TestMissingFramesDontPanic(t *testing.T) {
	samples := [][]byte{
		{}, {0}, {0,0}, {0,0,0}, {0,0,0,1},
		{0,0,0,1,'{'}, {0,0,0,1,0xff},
		framed([]byte(`{"malformed":{},"malformed":null}`)),
	}
	for _, sample := range samples {
		_, _ = DecodeRequestFrame(sample)
	}
}

func TestResearchLimitOnNestedJSON(t *testing.T) {
	nested := "1"
	for i := 0; i < 20; i++ {
		nested = "[" + nested + "]"
	}
	expectReject(t, framed([]byte(`{"payload":`+nested+"}")), "research parser nesting/token limit exceeded")
}

func TestSortedOperationSetStable(t *testing.T) {
	cat, err := loadAllowed()
	if err != nil {
		t.Fatal(err)
	}
	ids := make([]string,0,len(cat))
	for id:= range cat { ids=append(ids,id) }
	sort.Strings(ids)
	if len(ids)!=47 || ids[0]=="" || ids[len(ids)-1]=="" {
		t.Fatal("research IDs unstable")
	}
}

func FuzzDecodeRequestFrameNeverPanics(f *testing.F) {
	req, _ := json.Marshal(fixture())
	f.Add(framed(req))
	f.Add([]byte{})
	f.Add(framed([]byte(`{"payload":{"a":1,"a":2}}`)))
	f.Fuzz(func(t *testing.T, data []byte) {
		_, _ = DecodeRequestFrame(data)
	})
}
