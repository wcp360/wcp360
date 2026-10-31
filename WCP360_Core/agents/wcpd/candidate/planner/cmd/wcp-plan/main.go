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
	fields := map[string]string{}
	for d.More() {
		token, e = d.Token()
		if e != nil {
			return 2
		}
		key, ok := token.(string)
		if !ok || (key != "site_id" && key != "hostname") {
			return 2
		}
		if _, exists := fields[key]; exists {
			return 2
		}
		var value string
		if d.Decode(&value) != nil {
			return 2
		}
		fields[key] = value
	}
	token, e = d.Token()
	if e != nil || token != json.Delim('}') || d.Decode(new(any)) != io.EOF || len(fields) != 2 {
		return 2
	}
	plan, e := planner.Render(planner.Request{SiteID: fields["site_id"], Hostname: fields["hostname"]})
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
