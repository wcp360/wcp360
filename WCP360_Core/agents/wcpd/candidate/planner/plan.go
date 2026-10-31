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

var siteID = regexp.MustCompile(`^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$`)
var hostname = regexp.MustCompile(`^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+test$`)

type Request struct {
	SiteID   string `json:"site_id"`
	Hostname string `json:"hostname"`
}
type Plan struct {
	Schema string `json:"schema"`
	Nginx  string `json:"nginx"`
	PHPFPM string `json:"php_fpm"`
}

func Render(r Request) (Plan, error) {
	if !siteID.MatchString(r.SiteID) || len(r.Hostname) > 253 || !hostname.MatchString(r.Hostname) {
		return Plan{}, fmt.Errorf("invalid candidate identity or .test hostname")
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
	nginx, e := render(nginxSource)
	if e != nil {
		return Plan{}, e
	}
	pool, e := render(poolSource)
	if e != nil {
		return Plan{}, e
	}
	return Plan{"wcp360.candidate.plan/1", nginx, pool}, nil
}
