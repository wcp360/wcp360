package main

import (
	"bytes"
	"encoding/json"
	"strings"
	"testing"
)

func TestApplicationCliUsesExplicitMutuallyExclusiveSelectors(t *testing.T) {
	base := "{\"site_id\":\"019a3c3f-0000-7000-8000-000000000001\",\"hostname\":\"application.test\""
	for _, profile := range []string{"php", "front_controller", "wordpress"} {
		var output bytes.Buffer
		input := base + ",\"application\":\"" + profile + "\",\"recipe\":\"nginx_frankenphp\"}"
		if run(strings.NewReader(input), &output) != 0 {
			t.Fatal("valid application CLI failed")
		}
		var fields map[string]string
		if json.Unmarshal(output.Bytes(), &fields) != nil || fields["application"] != profile ||
			fields["application_server"] != "frankenphp_classic" || fields["schema"] != "wcp360.candidate.application-plan/1" {
			t.Fatal("application CLI returned another runtime")
		}
	}
	for _, suffix := range []string{
		",\"application\":\"php\"}", ",\"recipe\":\"nginx_php_fpm\"}",
		",\"application\":\"unknown\",\"recipe\":\"nginx_php_fpm\"}",
		",\"application\":1,\"recipe\":\"nginx_php_fpm\"}",
		",\"application\":\"php\",\"recipe\":[]}",
		",\"application\":\"php\",\"recipe\":\"unknown\"}",
		",\"application\":\"php\",\"recipe\":\"nginx_php_fpm\",\"web_stack\":\"modern\"}",
		",\"application\":\"php\",\"recipe\":\"nginx_php_fpm\",\"application_server\":\"php_fpm\"}",
		",\"application\":\"php\",\"recipe\":\"nginx_php_fpm\",\"worker_acknowledged\":false}",
		",\"application\":\"php\",\"recipe\":\"nginx_php_fpm\",\"application\":\"php\"}",
		",\"application\":\"php\",\"recipe\":\"nginx_php_fpm\",\"recipe\":\"nginx_php_fpm\"}",
		",\"application\":\"php\",\"recipe\":\"nginx_php_fpm\",\"path\":\"/etc/passwd\"}",
	} {
		var output bytes.Buffer
		if run(strings.NewReader(base+suffix), &output) != 2 || output.Len() != 0 {
			t.Fatal("invalid application CLI wrote a plan")
		}
	}
}

func TestApplicationCliKeepsInputByteBounds(t *testing.T) {
	for _, recipe := range []string{"nginx_php_fpm", "nginx_apache_php_fpm"} {
		t.Run(recipe, func(t *testing.T) {
			input := "{\"site_id\":\"019a3c3f-0000-7000-8000-000000000001\",\"hostname\":\"application.test\",\"application\":\"php\",\"recipe\":\"" + recipe + "\"}"
			var output bytes.Buffer
			if run(strings.NewReader(input+strings.Repeat(" ", 4096-len(input))), &output) != 0 {
				t.Fatal("bounded application request refused")
			}
			output.Reset()
			if run(strings.NewReader(input+strings.Repeat(" ", 4097-len(input))), &output) != 2 || output.Len() != 0 {
				t.Fatal("oversized application request accepted")
			}
		})
	}
}

func TestNginxApacheApplicationCliUsesOnlyVersionTwoArtifacts(t *testing.T) {
	for _, application := range []string{"php", "front_controller", "wordpress"} {
		t.Run(application, func(t *testing.T) {
			input := "{\"site_id\":\"019a3c3f-0000-7000-8000-000000000001\",\"hostname\":\"application.test\",\"application\":\"" + application + "\",\"recipe\":\"nginx_apache_php_fpm\"}"
			var output bytes.Buffer
			if run(strings.NewReader(input), &output) != 0 {
				t.Fatal("bounded hybrid application CLI failed")
			}
			var fields map[string]string
			if json.Unmarshal(output.Bytes(), &fields) != nil || len(fields) != 9 ||
				fields["schema"] != "wcp360.candidate.application-plan/2" ||
				fields["recipe"] != "nginx_apache_php_fpm" || fields["application"] != application ||
				fields["web_server"] != "nginx_apache" || fields["application_server"] != "php_fpm" {
				t.Fatal("hybrid CLI changed its versioned topology contract")
			}
			for _, artifact := range []string{"nginx", "apache", "php_fpm", "htaccess"} {
				if fields[artifact] == "" {
					t.Fatalf("hybrid CLI omitted %s", artifact)
				}
			}
			if !strings.Contains(fields["nginx"], "proxy_pass http://127.0.0.1:8083;") ||
				!strings.Contains(fields["apache"], "Listen 127.0.0.1:8083\n") ||
				strings.Contains(fields["nginx"], "try_files") {
				t.Fatal("hybrid CLI bypassed its fixed Apache backend")
			}
		})
	}
}

func TestNginxApacheApplicationCliRejectsOverridesAndMalformedSelectors(t *testing.T) {
	base := "{\"site_id\":\"019a3c3f-0000-7000-8000-000000000001\",\"hostname\":\"application.test\""
	cases := map[string]string{
		"port": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"port\":8083}",
		"backend": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"backend\":\"http://remote.test:8083\"}",
		"path": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"path\":\"/etc/apache2\"}",
		"url": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"url\":\"http://remote.test\"}",
		"headers": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"headers\":{\"Host\":\"remote.test\"}}",
		"htaccess": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"htaccess\":\"AllowOverride All\"}",
		"artifact": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"nginx\":\"server {}\"}",
		"schema": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"schema\":\"wcp360.candidate.application-plan/1\"}",
		"web-stack": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"web_stack\":\"classic\"}",
		"runtime": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"application_server\":\"php_fpm\"}",
		"worker-ack": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"worker_acknowledged\":false}",
		"duplicate-recipe": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"recipe\":\"nginx_php_fpm\"}",
		"duplicate-hostname": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"hostname\":\"evil.test\"}",
		"duplicate-application": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\",\"application\":\"php\"}",
		"null-recipe": base + ",\"application\":\"wordpress\",\"recipe\":null}",
		"array-recipe": base + ",\"application\":\"wordpress\",\"recipe\":[]}",
		"orphan-recipe": base + ",\"recipe\":\"nginx_apache_php_fpm\"}",
		"missing-recipe": base + ",\"application\":\"wordpress\"}",
		"worker-profile": base + ",\"application\":\"worker\",\"recipe\":\"nginx_apache_php_fpm\"}",
		"recipe-path": base + ",\"application\":\"wordpress\",\"recipe\":\"../nginx_apache_php_fpm\"}",
		"recipe-case": base + ",\"application\":\"wordpress\",\"recipe\":\"NGINX_APACHE_PHP_FPM\"}",
		"recipe-injection": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\\nListen 0.0.0.0:80\"}",
		"trailing-object": base + ",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\"}{}",
		"non-object": "[]",
		"host-port": "{\"site_id\":\"019a3c3f-0000-7000-8000-000000000001\",\"hostname\":\"application.test:8083\",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\"}",
		"host-url": "{\"site_id\":\"019a3c3f-0000-7000-8000-000000000001\",\"hostname\":\"http://application.test\",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\"}",
		"host-injection": "{\"site_id\":\"019a3c3f-0000-7000-8000-000000000001\",\"hostname\":\"application.test\\nproxy_pass http://remote.test;\",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\"}",
		"identity-path": "{\"site_id\":\"../../etc/apache2\",\"hostname\":\"application.test\",\"application\":\"wordpress\",\"recipe\":\"nginx_apache_php_fpm\"}",
	}
	for name, input := range cases {
		t.Run(name, func(t *testing.T) {
			var output bytes.Buffer
			if run(strings.NewReader(input), &output) != 2 || output.Len() != 0 {
				t.Fatal("unbounded hybrid request produced an application plan")
			}
		})
	}
}
