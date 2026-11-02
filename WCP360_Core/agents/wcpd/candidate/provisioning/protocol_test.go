package provisioning

import (
	"encoding/json"
	"strings"
	"testing"
)

func validRequest() Request {
	return Request{Schema, "provision_php_fpm", "019a3c3f-0000-7000-8000-000000000011", "019a3c3f-0000-7000-8000-000000000021", "019a3c3f-0000-7000-8000-000000000031", "demo.test", 2}
}
func TestStrictEnvelope(t *testing.T) {
	r := validRequest()
	raw, _ := json.Marshal(r)
	got, e := Decode(strings.NewReader(string(raw)))
	if e != nil || got != r {
		t.Fatal("valid request rejected", e)
	}
}
func TestRejectUnboundedUnknownDuplicateAndWrongTypes(t *testing.T) {
	raw, _ := json.Marshal(validRequest())
	base := strings.TrimSuffix(string(raw), "}")
	for _, s := range []string{"null", "[]", "{}", string(raw) + "{}", strings.Repeat(" ", 4097), base + `,"command":"id"}`, base + `,"site_id":"../../etc"}`, strings.Replace(string(raw), `"expected_revision":2`, `"expected_revision":null`, 1), strings.Replace(string(raw), `"operation":"provision_php_fpm"`, `"operation":"shell.exec"`, 1), strings.Replace(string(raw), "demo.test", "demo.test; include /etc/passwd;", 1)} {
		if _, e := Decode(strings.NewReader(s)); e == nil {
			t.Fatalf("accepted invalid request %s", s)
		}
	}
}
func TestInspectionUsesSameImmutableIdentity(t *testing.T) {
	r := validRequest()
	fingerprint := r.Fingerprint()
	r.Operation = "inspect_php_fpm"
	if r.Fingerprint() != fingerprint {
		t.Fatal("inspection changed identity")
	}
	r.TenantID = "019a3c3f-0000-7000-8000-000000000022"
	if r.Fingerprint() == fingerprint {
		t.Fatal("tenant not bound")
	}
}
