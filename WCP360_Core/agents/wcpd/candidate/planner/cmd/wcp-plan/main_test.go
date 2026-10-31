package main

import (
	"bytes"
	"strings"
	"testing"
)

func TestValidSingleBoundedObject(t *testing.T) {
	var out bytes.Buffer
	code := run(strings.NewReader(`{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"example.test"}`), &out)
	if code != 0 || !strings.Contains(out.String(), `"schema":"wcp360.candidate.plan/1"`) {
		t.Fatalf("invalid result: %d %s", code, out.String())
	}
}
func TestRejectMalformedDuplicateUnknownAndOversizedInput(t *testing.T) {
	for _, input := range []string{
		`{}`, `[]`, `null`,
		`{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"example.test","command":"id"}`,
		`{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"bad.test","hostname":"example.test"}`,
		`{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"example.test"}{}`,
		`{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":12}`,
		strings.Repeat(" ", 4097),
	} {
		var out bytes.Buffer
		if run(strings.NewReader(input), &out) != 2 || out.Len() != 0 {
			t.Fatalf("accepted invalid input %q", input)
		}
	}
}

func TestWorkerStrictOptionalFields(t *testing.T) {
	base := `{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"example.test"`
	var out bytes.Buffer
	if run(strings.NewReader(base+`,"application_server":"frankenphp_worker","worker_acknowledged":true}`), &out) != 0 || !strings.Contains(out.String(), `"schema":"wcp360.candidate.plan/2"`) {
		t.Fatal("worker request failed")
	}
	for _, suffix := range []string{
		`,"application_server":"frankenphp_worker"}`,
		`,"application_server":"frankenphp_worker","worker_acknowledged":false}`,
		`,"application_server":null}`,
		`,"worker_acknowledged":null}`,
		`,"worker_acknowledged":"true"}`,
		`,"worker_acknowledged":1}`,
		`,"application_server":"php_fpm","application_server":"frankenphp_worker"}`,
		`,"worker_acknowledged":true,"worker_acknowledged":false}`,
		`,"application_server":"/bin/sh"}`,
	} {
		out.Reset()
		if run(strings.NewReader(base+suffix), &out) != 2 || out.Len() != 0 {
			t.Fatalf("invalid worker request accepted: %s", suffix)
		}
	}
}
