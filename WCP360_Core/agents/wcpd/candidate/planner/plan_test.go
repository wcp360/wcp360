package planner

import (
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
