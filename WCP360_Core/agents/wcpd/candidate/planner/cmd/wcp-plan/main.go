package main

import (
	"encoding/json"
	"fmt"
	planner "github.com/yyogas/wcp360/candidate/planner"
	"io"
	"os"
	"strings"
)

func run(input io.Reader, output io.Writer) int {
	raw, e := io.ReadAll(io.LimitReader(input, 4097))
	if e != nil || len(raw) > 4096 {
		return 2
	}
	d := json.NewDecoder(strings.NewReader(string(raw)))
	token, e := d.Token()
	if e != nil || token != json.Delim('{') {
		return 2
	}
	fields := map[string]json.RawMessage{}
	for d.More() {
		token, e = d.Token()
		if e != nil {
			return 2
		}
		key, ok := token.(string)
		if !ok || (key != "site_id" && key != "hostname" && key != "application_server" && key != "worker_acknowledged" && key != "web_stack") {
			return 2
		}
		if _, exists := fields[key]; exists {
			return 2
		}
		var value json.RawMessage
		if d.Decode(&value) != nil || string(value) == "null" {
			return 2
		}
		fields[key] = value
	}
	token, e = d.Token()
	if e != nil || token != json.Delim('}') || d.Decode(new(any)) != io.EOF {
		return 2
	}
	var r planner.Request
	if len(fields["site_id"]) == 0 || len(fields["hostname"]) == 0 {
		return 2
	}
	if json.Unmarshal(fields["site_id"], &r.SiteID) != nil || json.Unmarshal(fields["hostname"], &r.Hostname) != nil {
		return 2
	}
	mode := "php_fpm"
	if value, ok := fields["application_server"]; ok {
		if json.Unmarshal(value, &mode) != nil {
			return 2
		}
	}
	acknowledged := false
	if value, ok := fields["worker_acknowledged"]; ok {
		if json.Unmarshal(value, &acknowledged) != nil {
			return 2
		}
	}
	var plan any
	if value, ok := fields["web_stack"]; ok {
		if _, conflicting := fields["application_server"]; conflicting {
			return 2
		}
		var stack string
		if json.Unmarshal(value, &stack) != nil || stack != "modern" {
			return 2
		}
		plan, e = planner.RenderModern(r, acknowledged)
	} else {
		plan, e = planner.RenderRuntime(r, mode, acknowledged)
	}
	if e != nil {
		return 2
	}
	if json.NewEncoder(output).Encode(plan) != nil {
		return 1
	}
	return 0
}
func main() {
	if len(os.Args) != 2 || os.Args[1] != "plan" {
		fmt.Fprintln(os.Stderr, "usage: wcp-plan plan")
		os.Exit(2)
	}
	code := run(os.Stdin, os.Stdout)
	if code != 0 {
		fmt.Fprintln(os.Stderr, "invalid candidate plan request")
	}
	os.Exit(code)
}
