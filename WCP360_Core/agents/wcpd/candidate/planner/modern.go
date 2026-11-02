package planner

import (
	"bytes"
	"crypto/sha256"
	_ "embed"
	"fmt"
	"text/template"
)

//go:embed templates/Caddyfile-modern.tmpl
var modernCaddySource string

//go:embed templates/frankenphp-modern.service.tmpl
var modernUnitSource string

// ModernPlan is a separate inert topology, never a replacement for plan/2.
// Its ordered fields are the plan/3 wire contract; no proxy or pool is emitted.
type ModernPlan struct {
	Schema            string `json:"schema"`
	WebStack          string `json:"web_stack"`
	ApplicationServer string `json:"application_server"`
	Caddy             string `json:"caddy"`
	SystemdUnit       string `json:"systemd_unit"`
}

// RenderModern prepares a one-site loopback preview, without opening a listener,
// allocating a port, installing files, starting a process or enabling a service.
func RenderModern(r Request, acknowledged bool) (ModernPlan, error) {
	if !siteID.MatchString(r.SiteID) || len(r.Hostname) > 253 || !hostname.MatchString(r.Hostname) {
		return ModernPlan{}, fmt.Errorf("invalid candidate identity or .test hostname")
	}
	if !acknowledged {
		return ModernPlan{}, fmt.Errorf("worker compatibility acknowledgement required")
	}
	digest := sha256.Sum256([]byte(r.SiteID))
	values := struct{ ID, Host, User string }{r.SiteID, r.Hostname, fmt.Sprintf("wcp_%x", digest[:12])}
	render := func(source string) (string, error) {
		t, e := template.New("modern").Option("missingkey=error").Parse(source)
		if e != nil {
			return "", e
		}
		var out bytes.Buffer
		e = t.Execute(&out, values)
		return out.String(), e
	}
	caddy, e := render(modernCaddySource)
	if e != nil {
		return ModernPlan{}, e
	}
	unit, e := render(modernUnitSource)
	if e != nil {
		return ModernPlan{}, e
	}
	plan := ModernPlan{
		Schema: "wcp360.candidate.plan/3", WebStack: "modern",
		ApplicationServer: "frankenphp_worker", Caddy: caddy, SystemdUnit: unit,
	}
	if len(plan.Schema)+len(plan.WebStack)+len(plan.ApplicationServer)+len(plan.Caddy)+len(plan.SystemdUnit) > 65536 {
		return ModernPlan{}, fmt.Errorf("candidate plan content limit exceeded")
	}
	return plan, nil
}
