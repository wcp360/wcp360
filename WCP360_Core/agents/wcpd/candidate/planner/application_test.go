package planner

import (
	"encoding/json"
	"reflect"
	"strings"
	"testing"
)

func TestApplicationRecipesCoverAllProfilesWithoutChangingLegacyPlans(t *testing.T) {
	r := Request{"019a3c3f-0000-7000-8000-000000000001", "application.test"}
	recipes := map[string][]string{
		"nginx_php_fpm": {"nginx", "php_fpm"},
		"nginx_frankenphp": {"nginx", "caddy", "systemd_unit"},
		"apache_php_fpm": {"apache", "php_fpm", "htaccess"},
		"apache_frankenphp": {"apache", "caddy", "systemd_unit"},
		"caddy_php_fpm": {"caddy", "php_fpm"},
		"caddy_frankenphp": {"caddy", "systemd_unit"},
	}
	for recipe, artifacts := range recipes {
		for _, application := range []string{"php", "front_controller", "wordpress"} {
			plan, e := RenderApplication(r, recipe, application)
			if e != nil {
				t.Fatal(e)
			}
			data, e := json.Marshal(plan)
			if e != nil || len(data) > 65536 {
				t.Fatal("invalid or oversized application transport")
			}
			var fields map[string]string
			if json.Unmarshal(data, &fields) != nil {
				t.Fatal("invalid application envelope")
			}
			if len(fields) != 5+len(artifacts) || fields["schema"] != "wcp360.candidate.application-plan/1" ||
				fields["application"] != application || fields["recipe"] != recipe {
				t.Fatal("wrong application contract")
			}
			for _, artifact := range artifacts {
				if fields[artifact] == "" {
					t.Fatalf("missing %s in %s", artifact, recipe)
				}
			}
			if strings.Contains(recipe, "frankenphp") {
				if plan.ApplicationServer != "frankenphp_classic" || strings.Contains(plan.Caddy, "worker {") ||
					!strings.Contains(plan.Caddy, "php @php") || !strings.Contains(plan.Caddy, "admin off") ||
					!strings.Contains(plan.SystemdUnit, "NoNewPrivileges=true") || strings.Contains(plan.SystemdUnit, "User=root") {
					t.Fatal("classic application became an unsafe or persistent worker")
				}
			} else if plan.ApplicationServer != "php_fpm" || !strings.Contains(plan.PHPFPM, "opcache.enable") ||
				!strings.Contains(plan.PHPFPM, "pm.max_requests = 500") || !strings.Contains(plan.PHPFPM, "clear_env = yes") {
				t.Fatal("missing bounded FPM baseline")
			}
			if strings.Contains(plan.Nginx, "listen 0.0.0.0") || strings.Contains(plan.Caddy, "bind 0.0.0.0") ||
				strings.Contains(plan.Apache, "Listen 0.0.0.0") {
				t.Fatal("application preview opened a public listener")
			}
		}
	}
	before, _ := RenderRuntime(r, "frankenphp_worker", true)
	_, _ = RenderApplication(r, "nginx_frankenphp", "wordpress")
	after, _ := RenderRuntime(r, "frankenphp_worker", true)
	if !reflect.DeepEqual(before, after) || after.Schema != "wcp360.candidate.plan/2" {
		t.Fatal("application recipe reinterpreted a durable Worker")
	}
}

func TestApplicationProfilesKeepRoutingAndPrivateBoundariesDistinct(t *testing.T) {
	r := Request{"019a3c3f-0000-7000-8000-000000000001", "application.test"}
	plain, _ := RenderApplication(r, "nginx_php_fpm", "php")
	wp, _ := RenderApplication(r, "nginx_php_fpm", "wordpress")
	if !strings.Contains(plain.Nginx, "try_files $uri $uri/ =404;") ||
		!strings.Contains(wp.Nginx, "/index.php?$args") {
		t.Fatal("traditional PHP and front-controller routing collapsed")
	}
	proxy, _ := RenderApplication(r, "nginx_frankenphp", "wordpress")
	if strings.Contains(proxy.Nginx, "location ~ \\.php") ||
		strings.Contains(proxy.Nginx, "location ~* \\.php") {
		t.Fatal("ordinary application PHP entry points are blocked")
	}
	for _, recipe := range []string{"nginx_php_fpm", "nginx_frankenphp", "apache_php_fpm", "caddy_php_fpm", "caddy_frankenphp"} {
		p, _ := RenderApplication(r, recipe, "wordpress")
		text := p.Nginx + p.Apache + p.Caddy
		if !strings.Contains(text, "wp-config") || !strings.Contains(text, "wp-content/uploads") {
			t.Fatalf("private/upload protections missing: %s", recipe)
		}
		if strings.Contains(text, "fastcgi_cache ") || strings.Contains(text, "proxy_cache ") || strings.Contains(text, "CacheEnable") {
			t.Fatal("dynamic page cache enabled before an application cache policy exists")
		}
	}
	apache, _ := RenderApplication(r, "apache_php_fpm", "wordpress")
	if !strings.Contains(apache.Apache, "AllowOverride None") ||
		!strings.Contains(apache.Apache, "AllowOverrideList RewriteEngine") ||
		!strings.Contains(apache.Htaccess, "HTTP_AUTHORIZATION") ||
		!strings.Contains(apache.Apache, `(?i)\.(php[0-9]+|phtml|phar)$`) ||
		!strings.Contains(apache.Apache, `<LocationMatch "(^|/)\.">`) {
		t.Fatal("restricted Apache routing is missing")
	}
	apacheProxy, _ := RenderApplication(r, "apache_frankenphp", "wordpress")
	if apacheProxy.Htaccess != "" || strings.Contains(apacheProxy.Apache, "SetHandler") {
		t.Fatal("HTTP proxy incorrectly claims native .htaccess handling")
	}
}

func TestApplicationRecipesRefuseUnknownAndInjectedSelectors(t *testing.T) {
	r := Request{"019a3c3f-0000-7000-8000-000000000001", "application.test"}
	for _, recipe := range []string{"", "nginx", "nginx;id", "../nginx_php_fpm", "nginx_php_fpm\nUser=root", "NGINX_PHP_FPM"} {
		if _, e := RenderApplication(r, recipe, "php"); e == nil {
			t.Fatal("invalid recipe admitted")
		}
	}
	for _, application := range []string{"", "worker", "wordpress\nadmin on", "../php", "PHP"} {
		if _, e := RenderApplication(r, "nginx_php_fpm", application); e == nil {
			t.Fatal("invalid application admitted")
		}
	}
	for _, bad := range []Request{
		{"../../etc/passwd", "application.test"}, {r.SiteID, "application.com"},
		{r.SiteID, "application.test\nserver {}"}, {r.SiteID, strings.Repeat("a", 64) + ".test"},
	} {
		if _, e := RenderApplication(bad, "nginx_php_fpm", "php"); e == nil {
			t.Fatal("invalid application target admitted")
		}
	}
}
