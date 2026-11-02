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
			fields["application_server"] != "frankenphp_classic" {
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
	input := "{\"site_id\":\"019a3c3f-0000-7000-8000-000000000001\",\"hostname\":\"application.test\",\"application\":\"php\",\"recipe\":\"nginx_php_fpm\"}"
	var output bytes.Buffer
	if run(strings.NewReader(input+strings.Repeat(" ", 4096-len(input))), &output) != 0 {
		t.Fatal("bounded application request refused")
	}
	output.Reset()
	if run(strings.NewReader(input+strings.Repeat(" ", 4097-len(input))), &output) != 2 || output.Len() != 0 {
		t.Fatal("oversized application request accepted")
	}
}
