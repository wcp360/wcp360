package main

import (
	"bytes"
	"encoding/json"
	planner "github.com/yyogas/wcp360/candidate/planner"
	"io"
	"strings"
	"testing"
)

func TestModernCliProducesOnlyItsVersionedEnvelope(t *testing.T) {
	var output bytes.Buffer
	input := `{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"modern.test","web_stack":"modern","worker_acknowledged":true}`
	if run(strings.NewReader(input), &output) != 0 {
		t.Fatal("Modern CLI rejected its explicit request")
	}
	var document map[string]string
	if json.Unmarshal(output.Bytes(), &document) != nil || len(document) != 5 {
		t.Fatal("Modern CLI returned an invalid envelope")
	}
	if document["schema"] != "wcp360.candidate.plan/3" || document["web_stack"] != "modern" || document["application_server"] != "frankenphp_worker" {
		t.Fatal("Modern CLI returned the wrong topology")
	}
	if document["caddy"] == "" || document["systemd_unit"] == "" {
		t.Fatal("Modern artifacts are missing")
	}
	if _, exists := document["nginx"]; exists {
		t.Fatal("Modern CLI emitted an edge proxy")
	}
	if _, exists := document["php_fpm"]; exists {
		t.Fatal("Modern CLI emitted a PHP-FPM pool")
	}
}

func TestModernCliRefusesConflictingUnknownDuplicateAndMalformedSelectors(t *testing.T) {
	base := `{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"modern.test"`
	for _, suffix := range []string{
		`,"web_stack":"modern"}`,
		`,"web_stack":"modern","worker_acknowledged":false}`,
		`,"web_stack":"modern","worker_acknowledged":"true"}`,
		`,"web_stack":"modern","worker_acknowledged":1}`,
		`,"web_stack":"modern","worker_acknowledged":null}`,
		`,"web_stack":"modern","worker_acknowledged":true,"worker_acknowledged":true}`,
		`,"web_stack":"modern","worker_acknowledged":true,"application_server":"php_fpm"}`,
		`,"web_stack":"modern","worker_acknowledged":true,"application_server":"frankenphp_worker"}`,
		`,"web_stack":"modern","worker_acknowledged":true,"application_server":null}`,
		`,"web_stack":"modern","web_stack":"modern","worker_acknowledged":true}`,
		`,"web_stack":"modern","worker_acknowledged":true,"command":"id"}`,
		`,"web_stack":"modern","worker_acknowledged":true,"port":443}`,
		`,"web_stack":"classic","worker_acknowledged":true}`,
		`,"web_stack":"advanced","worker_acknowledged":true}`,
		`,"web_stack":"legacy","worker_acknowledged":true}`,
		`,"web_stack":"frankenphp_direct","worker_acknowledged":true}`,
		`,"web_stack":"MODERN","worker_acknowledged":true}`,
		`,"web_stack":"modern\nadmin on","worker_acknowledged":true}`,
		`,"web_stack":"","worker_acknowledged":true}`,
		`,"web_stack":null,"worker_acknowledged":true}`,
		`,"web_stack":true,"worker_acknowledged":true}`,
		`,"web_stack":1,"worker_acknowledged":true}`,
		`,"web_stack":[],"worker_acknowledged":true}`,
		`,"web_stack":{},"worker_acknowledged":true}`,
		`,"web_stack":"modern","worker_acknowledged":true}{}`,
		`,"web_stack":"modern","worker_acknowledged":true`,
	} {
		var output bytes.Buffer
		if run(strings.NewReader(base+suffix), &output) != 2 || output.Len() != 0 {
			t.Fatalf("Modern accepted invalid selector %s", suffix)
		}
	}
}

func TestModernCliKeepsThe4096ByteInputLimit(t *testing.T) {
	base := `{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"modern.test","web_stack":"modern","worker_acknowledged":true}`
	var output bytes.Buffer
	if run(strings.NewReader(base+strings.Repeat(" ", 4096-len(base))), &output) != 0 {
		t.Fatal("bounded Modern request rejected at its input limit")
	}
	output.Reset()
	if run(strings.NewReader(base+strings.Repeat(" ", 4097-len(base))), &output) != 2 || output.Len() != 0 {
		t.Fatal("oversized Modern request admitted")
	}
}

func TestModernCliLeavesBothLegacyWireOutputsByteIdentical(t *testing.T) {
	input := planner.Request{SiteID: "019a3c3f-0000-7000-8000-000000000001", Hostname: "modern.test"}
	for _, mode := range []string{"php_fpm", "frankenphp_worker"} {
		plan, e := planner.RenderRuntime(input, mode, mode == "frankenphp_worker")
		if e != nil {
			t.Fatal(e)
		}
		var expected bytes.Buffer
		if json.NewEncoder(&expected).Encode(plan) != nil {
			t.Fatal("cannot encode expected legacy plan")
		}
		request := `{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"modern.test"`
		if mode == "frankenphp_worker" {
			request += `,"application_server":"frankenphp_worker","worker_acknowledged":true`
		}
		request += "}"
		var output bytes.Buffer
		if run(strings.NewReader(request), &output) != 0 || !bytes.Equal(expected.Bytes(), output.Bytes()) {
			t.Fatalf("legacy CLI wire changed for %s", mode)
		}
	}
}

type modernFailingWriter struct{}

func (modernFailingWriter) Write([]byte) (int, error) { return 0, io.ErrClosedPipe }

func TestModernCliReportsOutputFailure(t *testing.T) {
	input := `{"site_id":"019a3c3f-0000-7000-8000-000000000001","hostname":"modern.test","web_stack":"modern","worker_acknowledged":true}`
	if run(strings.NewReader(input), modernFailingWriter{}) != 1 {
		t.Fatal("Modern output error was not reported")
	}
}
