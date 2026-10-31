// Package planner emits inert development plans. It has no filesystem, network or subprocess capability.
package planner

import (
	"bytes"
	"crypto/sha256"
	_ "embed"
	"fmt"
	"regexp"
	"text/template"
)

//go:embed templates/nginx.conf.tmpl
var nginxSource string

//go:embed templates/php-fpm.conf.tmpl
var poolSource string

//go:embed templates/nginx-frankenphp.conf.tmpl
var workerNginxSource string

//go:embed templates/Caddyfile.tmpl
var caddySource string

//go:embed templates/frankenphp.service.tmpl
var workerUnitSource string

var siteID = regexp.MustCompile(`^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$`)
var hostname = regexp.MustCompile(`^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+test$`)

type Request struct {
	SiteID   string `json:"site_id"`
	Hostname string `json:"hostname"`
}
type Plan struct {
	Schema            string `json:"schema"`
	Nginx             string `json:"nginx"`
	PHPFPM            string `json:"php_fpm,omitempty"`
	ApplicationServer string `json:"application_server,omitempty"`
	Caddy             string `json:"caddy,omitempty"`
	SystemdUnit       string `json:"systemd_unit,omitempty"`
}

func Render(r Request) (Plan, error) { return RenderRuntime(r, "php_fpm", false) }

func RenderRuntime(r Request, mode string, acknowledged bool) (Plan, error) {
	if !siteID.MatchString(r.SiteID) || len(r.Hostname) > 253 || !hostname.MatchString(r.Hostname) {
		return Plan{}, fmt.Errorf("invalid candidate identity or .test hostname")
	}
	if mode != "php_fpm" && mode != "frankenphp_worker" {
		return Plan{}, fmt.Errorf("unsupported application server")
	}
	if mode == "frankenphp_worker" && !acknowledged {
		return Plan{}, fmt.Errorf("worker compatibility acknowledgement required")
	}
	digest := sha256.Sum256([]byte(r.SiteID))
	values := struct{ ID, Host, User string }{r.SiteID, r.Hostname, fmt.Sprintf("wcp_%x", digest[:12])}
	render := func(source string) (string, error) {
		t, e := template.New("plan").Option("missingkey=error").Parse(source)
		if e != nil {
			return "", e
		}
		var out bytes.Buffer
		e = t.Execute(&out, values)
		return out.String(), e
	}
	if mode == "php_fpm" {
		nginx, e := render(nginxSource)
		if e != nil {
			return Plan{}, e
		}
		pool, e := render(poolSource)
		if e != nil {
			return Plan{}, e
		}
		return Plan{Schema: "wcp360.candidate.plan/1", Nginx: nginx, PHPFPM: pool}, nil
	}
	nginx, e := render(workerNginxSource)
	if e != nil {
		return Plan{}, e
	}
	caddy, e := render(caddySource)
	if e != nil {
		return Plan{}, e
	}
	unit, e := render(workerUnitSource)
	if e != nil {
		return Plan{}, e
	}
	return Plan{Schema: "wcp360.candidate.plan/2", Nginx: nginx, ApplicationServer: mode, Caddy: caddy, SystemdUnit: unit}, nil
}
