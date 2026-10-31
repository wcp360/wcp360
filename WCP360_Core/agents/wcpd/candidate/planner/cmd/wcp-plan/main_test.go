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
