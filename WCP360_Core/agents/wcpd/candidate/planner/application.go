package planner

import (
	"bytes"
	"crypto/sha256"
	"embed"
	"fmt"
	"text/template"
)

//go:embed templates/applications/*.tmpl
var applicationTemplates embed.FS

// ApplicationPlan is inert configuration text, separate from durable plan/1-3.
type ApplicationPlan struct {
	Schema            string `json:"schema"`
	Application       string `json:"application"`
	Recipe            string `json:"recipe"`
	WebServer         string `json:"web_server"`
	ApplicationServer string `json:"application_server"`
	Nginx             string `json:"nginx,omitempty"`
	Apache            string `json:"apache,omitempty"`
	Caddy             string `json:"caddy,omitempty"`
	PHPFPM            string `json:"php_fpm,omitempty"`
	SystemdUnit       string `json:"systemd_unit,omitempty"`
	Htaccess          string `json:"htaccess,omitempty"`
}

func RenderApplication(r Request, recipe, application string) (ApplicationPlan, error) {
	if !siteID.MatchString(r.SiteID) || len(r.Hostname) > 253 || !hostname.MatchString(r.Hostname) {
		return ApplicationPlan{}, fmt.Errorf("invalid candidate identity or .test hostname")
	}
	if application != "php" && application != "front_controller" && application != "wordpress" {
		return ApplicationPlan{}, fmt.Errorf("unsupported application profile")
	}
	var web, runtime string
	switch recipe {
	case "nginx_php_fpm":
		web, runtime = "nginx", "php_fpm"
	case "nginx_apache_php_fpm":
		web, runtime = "nginx_apache", "php_fpm"
	case "nginx_frankenphp":
		web, runtime = "nginx", "frankenphp_classic"
	case "apache_php_fpm":
		web, runtime = "apache", "php_fpm"
	case "apache_frankenphp":
		web, runtime = "apache", "frankenphp_classic"
	case "caddy_php_fpm":
		web, runtime = "caddy", "php_fpm"
	case "caddy_frankenphp":
		web, runtime = "caddy", "frankenphp_classic"
	default:
		return ApplicationPlan{}, fmt.Errorf("unsupported application recipe")
	}
	digest := sha256.Sum256([]byte(r.SiteID))
	frontUser := "www-data"
	if web == "caddy" && runtime == "php_fpm" {
		frontUser = "caddy"
	}
	listen := "127.0.0.1:8082"
	if web == "nginx_apache" {
		listen = "127.0.0.1:8083"
	}
	values := struct {
		ID, Host, User, Root, FrontUser, Listen string
		Front, Backend                        bool
	}{
		ID: r.SiteID, Host: r.Hostname, User: fmt.Sprintf("wcp_%x", digest[:12]),
		Root: "/srv/wcp360-candidate/applications/" + r.SiteID + "/public",
		FrontUser: frontUser, Listen: listen, Front: application != "php",
		Backend: web != "caddy",
	}
	render := func(name string) (string, error) {
		source, e := applicationTemplates.ReadFile("templates/applications/" + name)
		if e != nil {
			return "", e
		}
		t, e := template.New(name).Option("missingkey=error").Parse(string(source))
		if e != nil {
			return "", e
		}
		var out bytes.Buffer
		if e = t.Execute(&out, values); e != nil {
			return "", e
		}
		return out.String(), nil
	}
	plan := ApplicationPlan{Schema: "wcp360.candidate.application-plan/1", Application: application,
		Recipe: recipe, WebServer: web, ApplicationServer: runtime}
	targets := map[string]*string{}
	if web == "nginx_apache" {
		// A distinct preview schema preserves all existing recipe contracts.
		plan.Schema = "wcp360.candidate.application-plan/2"
		targets["nginx-apache-proxy.conf.tmpl"] = &plan.Nginx
		targets["apache-fpm.conf.tmpl"] = &plan.Apache
		targets["htaccess.tmpl"] = &plan.Htaccess
	} else if web == "nginx" {
		name := "nginx-fpm.conf.tmpl"
		if runtime == "frankenphp_classic" {
			name = "nginx-proxy.conf.tmpl"
		}
		targets[name] = &plan.Nginx
	} else if web == "apache" {
		name := "apache-fpm.conf.tmpl"
		if runtime == "frankenphp_classic" {
			name = "apache-proxy.conf.tmpl"
		} else {
			targets["htaccess.tmpl"] = &plan.Htaccess
		}
		targets[name] = &plan.Apache
	}
	if runtime == "php_fpm" {
		targets["php-fpm.conf.tmpl"] = &plan.PHPFPM
		if web == "caddy" {
			targets["Caddyfile-fpm.tmpl"] = &plan.Caddy
		}
	} else {
		targets["Caddyfile-classic.tmpl"] = &plan.Caddy
		targets["frankenphp.service.tmpl"] = &plan.SystemdUnit
	}
	for name, target := range targets {
		content, e := render(name)
		if e != nil {
			return ApplicationPlan{}, e
		}
		*target = content
	}
	total := 0
	for _, field := range []string{plan.Schema, plan.Application, plan.Recipe, plan.WebServer, plan.ApplicationServer,
		plan.Nginx, plan.Apache, plan.Caddy, plan.PHPFPM, plan.SystemdUnit, plan.Htaccess} {
		total += len(field)
	}
	if total > 65536 {
		return ApplicationPlan{}, fmt.Errorf("candidate application plan content limit exceeded")
	}
	return plan, nil
}
