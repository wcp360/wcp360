package planner

import (
	"encoding/json"
	"regexp"
	"strings"
	"testing"
)

func TestValidBoundedPlan(t *testing.T) {
	p, e := Render(Request{"019a3c3f-0000-7000-8000-000000000001", "example.test"})
	if e != nil {
		t.Fatal(e)
	}
	if p.Schema != "wcp360.candidate.plan/1" || !strings.Contains(p.Nginx, "server_name example.test;") || !strings.Contains(p.PHPFPM, "clear_env = yes") {
		t.Fatal("invalid plan")
	}
	if strings.Contains(p.Nginx, "systemctl") || strings.Contains(p.PHPFPM, "user = root") {
		t.Fatal("privilege escape")
	}
}
func TestRejectInjectionAndUnboundedTargets(t *testing.T) {
	for _, r := range []Request{
		{"../../etc/passwd", "example.test"},
		{"019a3c3f-0000-7000-8000-000000000001", "example.test;include /etc/passwd;"},
		{"019a3c3f-0000-7000-8000-000000000001", "example.com"},
		{"019a3c3f-0000-7000-8000-000000000001", "EXAMPLE.test"},
		{"019a3c3f-0000-7000-8000-000000000001", "-example.test"},
		{"019a3c3f-0000-7000-8000-000000000001", strings.Repeat("a", 64) + ".test"},
	} {
		if _, e := Render(r); e == nil {
			t.Fatalf("accepted invalid input: %#v", r)
		}
	}
}

func TestSameTimestampIDsUseDifferentUnixIdentities(t *testing.T) {
	first, e := Render(Request{"019a3c3f-0000-7000-8000-000000000001", "one.test"})
	if e != nil {
		t.Fatal(e)
	}
	second, e := Render(Request{"019a3c3f-0000-7000-8000-000000000002", "two.test"})
	if e != nil {
		t.Fatal(e)
	}
	pattern := regexp.MustCompile(`(?m)^user = (wcp_[a-f0-9]{24})$`)
	a := pattern.FindStringSubmatch(first.PHPFPM)
	b := pattern.FindStringSubmatch(second.PHPFPM)
	if len(a) != 2 || len(b) != 2 || a[1] == b[1] {
		t.Fatal("different site IDs must not share a timestamp-derived Unix identity")
	}
}

// Worker profiles must never reuse a PHP-FPM pool or share an application process.
func TestWorkerPlanHasDedicatedIdentityAndBoundedListeners(t *testing.T) {
	first, e := RenderRuntime(Request{"019a3c3f-0000-7000-8000-000000000001", "one.test"}, "frankenphp_worker", true)
	if e != nil {
		t.Fatal(e)
	}
	second, e := RenderRuntime(Request{"019a3c3f-0000-7000-8000-000000000002", "two.test"}, "frankenphp_worker", true)
	if e != nil {
		t.Fatal(e)
	}
	if first.Schema != "wcp360.candidate.plan/2" || first.PHPFPM != "" || first.ApplicationServer != "frankenphp_worker" {
		t.Fatal("wrong worker schema")
	}
	for _, text := range []string{"admin off", "auto_https off", "num 1", "max_requests 500", "max_consecutive_failures 3", "http.sock|0660"} {
		if !strings.Contains(first.Caddy, text) {
			t.Fatalf("missing bound %s", text)
		}
	}
	if !strings.Contains(first.Nginx, "proxy_pass http://unix:/run/wcp360-frankenphp/019a3c3f-0000-7000-8000-000000000001/http.sock:;") {
		t.Fatal("wrong private upstream")
	}
	users := regexp.MustCompile(`(?m)^User=(wcp_[a-f0-9]{24})$`)
	a, b := users.FindStringSubmatch(first.SystemdUnit), users.FindStringSubmatch(second.SystemdUnit)
	if len(a) != 2 || len(b) != 2 || a[1] == b[1] {
		t.Fatal("sites share Unix identity")
	}
	if strings.Contains(first.SystemdUnit, "User=root") || strings.Contains(first.SystemdUnit, "Group=www-data") {
		t.Fatal("unsafe worker identity")
	}
	for _, text := range []string{"NoNewPrivileges=true", "ProtectSystem=strict", "MemoryMax=768M", "TasksMax=64"} {
		if !strings.Contains(first.SystemdUnit, text) {
			t.Fatal("unbounded service")
		}
	}
}
func TestWorkerRejectsMissingAcknowledgementAndUnknownMode(t *testing.T) {
	r := Request{"019a3c3f-0000-7000-8000-000000000001", "one.test"}
	for _, mode := range []string{"frankenphp_worker", "cgi", "frankenphp_worker\nadmin on", ""} {
		if _, e := RenderRuntime(r, mode, false); e == nil {
			t.Fatalf("accepted %q", mode)
		}
	}
}
func TestLegacyFpmWireShapeIsPreserved(t *testing.T) {
	p, e := Render(Request{"019a3c3f-0000-7000-8000-000000000001", "example.test"})
	if e != nil {
		t.Fatal(e)
	}
	raw, e := json.Marshal(p)
	if e != nil {
		t.Fatal(e)
	}
	var fields map[string]any
	if json.Unmarshal(raw, &fields) != nil || len(fields) != 3 || fields["schema"] != "wcp360.candidate.plan/1" {
		t.Fatal("legacy shape changed")
	}
}
