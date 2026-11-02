package planner

import (
	"bytes"
	"encoding/json"
	"reflect"
	"regexp"
	"strings"
	"testing"
)

func TestModernWireHasItsOwnExactOrderedEnvelope(t *testing.T) {
	plan, e := RenderModern(Request{"019a3c3f-0000-7000-8000-000000000001", "modern.test"}, true)
	if e != nil {
		t.Fatal(e)
	}
	raw, e := json.Marshal(plan)
	if e != nil {
		t.Fatal(e)
	}
	decoder := json.NewDecoder(bytes.NewReader(raw))
	if token, e := decoder.Token(); e != nil || token != json.Delim('{') {
		t.Fatal("missing object")
	}
	keys := []string{}
	for decoder.More() {
		token, e := decoder.Token()
		if e != nil {
			t.Fatal(e)
		}
		key, ok := token.(string)
		if !ok {
			t.Fatal("invalid field")
		}
		keys = append(keys, key)
		var value string
		if decoder.Decode(&value) != nil || value == "" {
			t.Fatal("invalid string field")
		}
	}
	if !reflect.DeepEqual(keys, []string{"schema", "web_stack", "application_server", "caddy", "systemd_unit"}) {
		t.Fatalf("unexpected Modern fields: %v", keys)
	}
	if plan.Schema != "wcp360.candidate.plan/3" || plan.WebStack != "modern" || plan.ApplicationServer != "frankenphp_worker" {
		t.Fatal("wrong Modern markers")
	}
	if len(raw) > 65536 {
		t.Fatal("Modern transport exceeds the PHP reader bound")
	}
}

func TestModernHasOnlyOneLoopbackHttpListenerAndBoundedWorker(t *testing.T) {
	plan, e := RenderModern(Request{"019a3c3f-0000-7000-8000-000000000001", "modern.test"}, true)
	if e != nil {
		t.Fatal(e)
	}
	for _, expected := range []string{
		"admin off", "auto_https off", "http://modern.test:8082 {", "bind 127.0.0.1",
		"num_threads 2", "max_threads 3", "max_requests 500", "memory_limit 256M",
		"num 1", "env MAX_REQUESTS 500", "max_consecutive_failures 3", "respond @hidden 403",
	} {
		if !strings.Contains(plan.Caddy, expected) {
			t.Fatalf("missing worker/listener bound %q", expected)
		}
	}
	if strings.Count(plan.Caddy, "http://") != 1 || strings.Count(plan.Caddy, "    bind ") != 1 {
		t.Fatal("preview has more than its one fixed listener")
	}
	for _, denied := range []string{"nginx", "php_fpm", "proxy_pass", "reverse_proxy", "unix//", "https://", ":443", ":80 {", "tls ", "acme", "h3", "0.0.0.0", "::"} {
		if strings.Contains(plan.Caddy, denied) {
			t.Fatalf("unexpected Modern listener/proxy content %q", denied)
		}
	}
}

func TestModernServiceIsNonRootBoundedAndSeparateFromAdvanced(t *testing.T) {
	first, e := RenderModern(Request{"019a3c3f-0000-7000-8000-000000000001", "one.test"}, true)
	if e != nil {
		t.Fatal(e)
	}
	second, e := RenderModern(Request{"019a3c3f-0000-7000-8000-000000000002", "two.test"}, true)
	if e != nil {
		t.Fatal(e)
	}
	users := regexp.MustCompile(`(?m)^User=(wcp_[a-f0-9]{24})$`)
	a, b := users.FindStringSubmatch(first.SystemdUnit), users.FindStringSubmatch(second.SystemdUnit)
	if len(a) != 2 || len(b) != 2 || a[1] == b[1] {
		t.Fatal("Modern sites share a Unix identity")
	}
	if !strings.Contains(first.SystemdUnit, "\nGroup="+a[1]+"\n") {
		t.Fatal("service group differs from its site identity")
	}
	for _, expected := range []string{
		"/var/lib/wcp360-candidate/modern/019a3c3f-0000-7000-8000-000000000001/Caddyfile",
		"RuntimeDirectory=wcp360-modern/019a3c3f-0000-7000-8000-000000000001",
		"StateDirectory=wcp360-modern/019a3c3f-0000-7000-8000-000000000001",
		"XDG_CONFIG_HOME=/var/lib/wcp360-modern/019a3c3f-0000-7000-8000-000000000001/config",
		"XDG_DATA_HOME=/var/lib/wcp360-modern/019a3c3f-0000-7000-8000-000000000001/data",
		"NoNewPrivileges=true", "ProtectSystem=strict", "MemoryMax=768M", "TasksMax=64",
		"RuntimeDirectoryMode=0750", "StateDirectoryMode=0700", "UMask=0077", "CapabilityBoundingSet=\n",
	} {
		if !strings.Contains(first.SystemdUnit, expected) {
			t.Fatalf("missing Modern service bound %q", expected)
		}
	}
	for _, denied := range []string{"User=root", "Group=root", "Group=www-data", "CAP_", "AmbientCapabilities", "wcp360-frankenphp", "/candidate/frankenphp/"} {
		if strings.Contains(first.SystemdUnit, denied) {
			t.Fatalf("unexpected Modern service capability/path %q", denied)
		}
	}
}

func TestModernRefusesAcknowledgementAndInjectedOrUnboundedTargets(t *testing.T) {
	valid := Request{"019a3c3f-0000-7000-8000-000000000001", "modern.test"}
	if _, e := RenderModern(valid, false); e == nil {
		t.Fatal("Modern accepted a missing compatibility acknowledgement")
	}
	for _, input := range []Request{
		{"../../etc/passwd", "modern.test"},
		{"019a3c3f-0000-4000-8000-000000000001", "modern.test"},
		{"019a3c3f-0000-7000-8000-000000000001\nUser=root", "modern.test"},
		{valid.SiteID, "modern.test\nadmin on"},
		{valid.SiteID, "modern.test;include /etc/passwd"},
		{valid.SiteID, "modern.com"},
		{valid.SiteID, "MODERN.test"},
		{valid.SiteID, "-modern.test"},
		{valid.SiteID, "127.0.0.1"},
		{valid.SiteID, strings.Repeat("a", 64) + ".test"},
		{valid.SiteID, strings.Repeat("a.", 125) + "a.test"},
	} {
		if _, e := RenderModern(input, true); e == nil {
			t.Fatalf("Modern accepted invalid input %#v", input)
		}
	}
}

func TestModernDoesNotChangeLegacyPlansOrResolveAsAnExistingRuntime(t *testing.T) {
	input := Request{"019a3c3f-0000-7000-8000-000000000001", "modern.test"}
	fpmBefore, e := Render(input)
	if e != nil {
		t.Fatal(e)
	}
	advancedBefore, e := RenderRuntime(input, "frankenphp_worker", true)
	if e != nil {
		t.Fatal(e)
	}
	if _, e := RenderModern(input, true); e != nil {
		t.Fatal(e)
	}
	fpmAfter, e := Render(input)
	if e != nil {
		t.Fatal(e)
	}
	advancedAfter, e := RenderRuntime(input, "frankenphp_worker", true)
	if e != nil {
		t.Fatal(e)
	}
	if fpmBefore != fpmAfter || advancedBefore != advancedAfter {
		t.Fatal("Modern rendering mutated an existing runtime")
	}
	if fpmAfter.Schema != "wcp360.candidate.plan/1" || advancedAfter.Schema != "wcp360.candidate.plan/2" || advancedAfter.Nginx == "" {
		t.Fatal("legacy plan semantics changed")
	}
	for _, mode := range []string{"modern", "frankenphp_direct"} {
		if _, e := RenderRuntime(input, mode, true); e == nil {
			t.Fatalf("legacy runtime implicitly accepted %q", mode)
		}
	}
}
