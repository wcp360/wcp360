package planner

import (
	"crypto/sha256"
	"encoding/json"
	"fmt"
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

func TestNginxApacheApplicationUsesVersionTwoWithoutBypassingApache(t *testing.T) {
	r := Request{"019a3c3f-0000-7000-8000-000000000001", "application.test"}
	for _, application := range []string{"php", "front_controller", "wordpress"} {
		t.Run(application, func(t *testing.T) {
			plan, err := RenderApplication(r, "nginx_apache_php_fpm", application)
			if err != nil {
				t.Fatal(err)
			}
			data, err := json.Marshal(plan)
			if err != nil || len(data) > 65536 {
				t.Fatal("invalid or oversized hybrid application transport")
			}
			var fields map[string]string
			if json.Unmarshal(data, &fields) != nil || len(fields) != 9 {
				t.Fatal("hybrid application has unexpected or missing artifacts")
			}
			for _, key := range []string{"schema", "application", "recipe", "web_server", "application_server", "nginx", "apache", "php_fpm", "htaccess"} {
				if fields[key] == "" {
					t.Fatalf("missing hybrid field: %s", key)
				}
			}
			if plan.Schema != "wcp360.candidate.application-plan/2" || plan.Application != application ||
				plan.Recipe != "nginx_apache_php_fpm" || plan.WebServer != "nginx_apache" || plan.ApplicationServer != "php_fpm" {
				t.Fatal("hybrid recipe did not select its separate versioned contract")
			}
			if strings.Count(plan.Nginx, "location ") != 1 || strings.Count(plan.Nginx, "proxy_pass ") != 1 {
				t.Fatal("a second edge route can bypass Apache content authorization")
			}
			for _, required := range []string{
				"listen 127.0.0.1:8082;", "location / {", "proxy_pass http://127.0.0.1:8083;",
				"proxy_http_version 1.1;", "proxy_set_header Host application.test;",
				"proxy_set_header X-Real-IP $remote_addr;", "proxy_set_header X-Forwarded-For $remote_addr;",
				"proxy_set_header X-Forwarded-Proto $scheme;", "proxy_set_header X-Forwarded-Host application.test;",
				"proxy_set_header X-Forwarded-Port 8082;", "proxy_set_header Forwarded \"\";",
				"proxy_set_header Proxy \"\";", "proxy_set_header Connection \"\";",
				"proxy_redirect off;", "proxy_intercept_errors off;", "proxy_cache off;", "proxy_store off;",
				"proxy_connect_timeout 3s;", "proxy_read_timeout 60s;", "proxy_send_timeout 60s;",
			} {
				if !strings.Contains(plan.Nginx, required) {
					t.Fatalf("missing bounded edge directive: %s", required)
				}
			}
			for _, forbidden := range []string{
				"root ", "alias ", "try_files ", "fastcgi_", "location ~", "location =", "return ", "error_page ",
				"$http_", "$proxy_add_x_forwarded_for", "proxy_hide_header ", "proxy_ignore_headers ",
				"proxy_cache_path ", "proxy_cache_valid ", "proxy_store on", "/srv/", "include ",
			} {
				if strings.Contains(plan.Nginx, forbidden) {
					t.Fatalf("edge content or inherited-header shortcut: %s", forbidden)
				}
			}
			legacy, err := RenderApplication(r, "apache_php_fpm", application)
			if err != nil {
				t.Fatal(err)
			}
			if plan.Apache != strings.ReplaceAll(legacy.Apache, "127.0.0.1:8082", "127.0.0.1:8083") ||
				plan.PHPFPM != legacy.PHPFPM || plan.Htaccess != legacy.Htaccess {
				t.Fatal("hybrid recipe stopped reusing the reviewed Apache/FPM/htaccess policy")
			}
			if !strings.Contains(plan.Apache, "Listen 127.0.0.1:8083\n") ||
				!strings.Contains(plan.Apache, "<VirtualHost 127.0.0.1:8083>") ||
				strings.Contains(plan.Apache, "127.0.0.1:8082") ||
				!strings.Contains(plan.PHPFPM, "listen.owner = www-data\n") ||
				!strings.Contains(plan.PHPFPM, "listen.group = www-data\n") ||
				!strings.Contains(plan.PHPFPM, "listen.mode = 0600\n") {
				t.Fatal("hybrid backend escaped its private listener or Apache socket identity")
			}
			if !strings.Contains(plan.Apache, "AllowOverride None\n") ||
				!strings.Contains(plan.Apache, "AllowOverrideList RewriteEngine RewriteBase RewriteCond RewriteRule\n") ||
				!strings.Contains(plan.Apache, "wp-content/uploads") ||
				!strings.Contains(plan.Apache, `<LocationMatch "(^|/)\.">`) {
				t.Fatal("static and dynamic content no longer share the Apache policy")
			}
			if application == "php" {
				if strings.Contains(plan.Htaccess, "RewriteRule") || !strings.Contains(plan.Htaccess, "Traditional PHP") {
					t.Fatal("traditional PHP acquired a front-controller fallback")
				}
			} else if !strings.Contains(plan.Htaccess, "RewriteRule ^ /index.php [END]") ||
				!strings.Contains(plan.Htaccess, "E=HTTP_AUTHORIZATION:") {
				t.Fatal("front-controller or WordPress route lost its fixed rewrite recipe")
			}
			before, err := RenderRuntime(r, "php_fpm", false)
			if err != nil {
				t.Fatal(err)
			}
			_, _ = RenderApplication(r, "nginx_apache_php_fpm", application)
			after, err := RenderRuntime(r, "php_fpm", false)
			if err != nil || !reflect.DeepEqual(before, after) || after.Schema != "wcp360.candidate.plan/1" {
				t.Fatal("hybrid application preview reinterpreted the durable default")
			}
		})
	}
}

func TestNginxApacheApplicationRejectsUnboundedInputs(t *testing.T) {
	r := Request{"019a3c3f-0000-7000-8000-000000000001", "application.test"}
	for _, recipe := range []string{
		"nginx_apache", "NGINX_APACHE_PHP_FPM", "../nginx_apache_php_fpm",
		"nginx_apache_php_fpm\nListen 0.0.0.0:80", "nginx_apache_php_fpm;id",
	} {
		if _, err := RenderApplication(r, recipe, "wordpress"); err == nil {
			t.Fatal("unbounded hybrid selector admitted")
		}
	}
	for _, application := range []string{"", "worker", "wordpress\nAllowOverride All", "../wordpress"} {
		if _, err := RenderApplication(r, "nginx_apache_php_fpm", application); err == nil {
			t.Fatal("unbounded hybrid application admitted")
		}
	}
	for _, target := range []Request{
		{"../../etc/apache2", r.Hostname}, {r.SiteID + "/public", r.Hostname},
		{r.SiteID, "127.0.0.1"}, {r.SiteID, "application.test:8083"},
		{r.SiteID, "http://application.test"}, {r.SiteID, "application.test\nproxy_pass http://remote.test;"},
		{r.SiteID, "application.test/path"}, {r.SiteID, strings.Repeat("a", 64) + ".test"},
	} {
		if _, err := RenderApplication(target, "nginx_apache_php_fpm", "wordpress"); err == nil {
			t.Fatal("unbounded hybrid listener, path or identity admitted")
		}
	}
}

func TestApplicationVersionOneReferenceBytes(t *testing.T) {
	// Captured statically from the pre-hybrid templates for this synthetic target.
	// NUL-separated envelope fields and raw artifacts retain every output byte.
	expected := map[string]string{
		"nginx_php_fpm/php":                  "6280ac11833c0dd6b056ea23a6e8c99ccd03baf5762de7d648e2fe139364c492",
		"nginx_php_fpm/front_controller":     "cfb728fe8be4ad257e8307064d136b737124f441d9d2d8ad95f9f0905750654e",
		"nginx_php_fpm/wordpress":            "1ffac478c9c48ea050b48576d2e1edd5efc76882746873c01df883c8d3a2307a",
		"nginx_frankenphp/php":               "9d87e705c378fc0c18eaad212ffc0a4c7fc24bc8207180c0012f7ec82d5c3f5a",
		"nginx_frankenphp/front_controller":  "a3fa92e092fbc9be2b2c43b2d21f58bd1dae966ebbc42186c57232dd139e96b2",
		"nginx_frankenphp/wordpress":         "95aed88928e36efa6930e1a562ff272b2f85bf18acfd9b1fd07421327a71fa8f",
		"apache_php_fpm/php":                 "5621138b6c131ce2ef45c932542339b67cbf0a7824d06760ac3666c40acd20b5",
		"apache_php_fpm/front_controller":    "a1148ecfd8a6b4fe76d53fb38cf14393a89362798471f9ff700fdab8ed5342d9",
		"apache_php_fpm/wordpress":           "42c59f9b866a0893e346cdd0d01d2c2c634a2e8548d2044267823a29b8f025f5",
		"apache_frankenphp/php":              "820ca1283fa160adb43b62401089bfb22b620fb5aa36778c42af32faf5bc0d77",
		"apache_frankenphp/front_controller": "da01d291a51c805a1c69bf9770518d4719d855b2c4aad551ec4efd74e0394936",
		"apache_frankenphp/wordpress":        "46f9ebf334d4ae501152c99036f15197568239d5f1fc1f724588f2b5265ce5eb",
		"caddy_php_fpm/php":                  "5297cbc42b719595b9ec4d4176b82f58a1037ed2720c9d80fa1284b7826ebfcb",
		"caddy_php_fpm/front_controller":     "69a59b556e25f2f804a96208a9e2c02964aa499c7a2646fcb01ac5426e0d4513",
		"caddy_php_fpm/wordpress":            "1b2288d0c8a959251a589990354ca1b4b3bfa627c44bc2301910d26932e2f788",
		"caddy_frankenphp/php":               "de39928e430186ad07dc84ecb7f297a3cbc2005642cd028d67589f46ab39006b",
		"caddy_frankenphp/front_controller":  "4d1cca10a9281641fe2d3a4d005fa98f7ab00f12ef146e1ae7dfbd934fff5114",
		"caddy_frankenphp/wordpress":         "8805461c993d2bafe950e8b182ad8cc940d7f30af4297314655d65ce4390dc05",
	}
	r := Request{"019a3c3f-0000-7000-8000-000000000001", "application.test"}
	for key, want := range expected {
		t.Run(key, func(t *testing.T) {
			recipe, application, ok := strings.Cut(key, "/")
			if !ok {
				t.Fatal("invalid reference key")
			}
			plan, err := RenderApplication(r, recipe, application)
			if err != nil {
				t.Fatal(err)
			}
			content := strings.Join([]string{
				plan.Schema, plan.Application, plan.Recipe, plan.WebServer, plan.ApplicationServer,
				plan.Nginx, plan.Apache, plan.Caddy, plan.PHPFPM, plan.SystemdUnit, plan.Htaccess,
			}, "\x00")
			digest := sha256.Sum256([]byte(content))
			if got := fmt.Sprintf("%x", digest); got != want {
				t.Fatalf("legacy version-one bytes changed: got %s, want %s", got, want)
			}
		})
	}
}
