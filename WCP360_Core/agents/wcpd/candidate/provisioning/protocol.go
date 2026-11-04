// Package provisioning implements an isolated-container experiment, not canonical IPC-B.
package provisioning

import (
	"crypto/sha256"
	"encoding/json"
	"fmt"
	planner "github.com/yyogas/wcp360/candidate/planner"
	"io"
	"regexp"
	"strings"
)

const Schema = "wcp360.candidate.provision/1"
const AgentSocket = "/run/wcp360-provision-candidate/public/agent.sock"
const BrokerSocket = "/run/wcp360-provision-candidate/broker.sock"
const StateRoot = "/var/lib/wcp360-provision-candidate"

type Request struct {
	Schema           string `json:"schema"`
	Operation        string `json:"operation"`
	JobID            string `json:"job_id"`
	TenantID         string `json:"tenant_id"`
	SiteID           string `json:"site_id"`
	Hostname         string `json:"hostname"`
	ExpectedRevision int    `json:"expected_revision"`
}
type Response struct {
	Schema   string `json:"schema"`
	JobID    string `json:"job_id"`
	SiteID   string `json:"site_id"`
	Status   string `json:"status"`
	Replayed bool   `json:"replayed"`
}

var uuid = regexp.MustCompile(`^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$`)

func Decode(input io.Reader) (Request, error) {
	var r Request
	raw, e := io.ReadAll(io.LimitReader(input, 4097))
	if e != nil || len(raw) > 4096 {
		return r, fmt.Errorf("input limit")
	}
	d := json.NewDecoder(strings.NewReader(string(raw)))
	t, e := d.Token()
	if e != nil || t != json.Delim('{') {
		return r, fmt.Errorf("object required")
	}
	allowed := map[string]bool{"schema": true, "operation": true, "job_id": true, "tenant_id": true, "site_id": true, "hostname": true, "expected_revision": true}
	seen := map[string]bool{}
	for d.More() {
		t, e = d.Token()
		if e != nil {
			return r, e
		}
		key, ok := t.(string)
		if !ok || !allowed[key] || seen[key] {
			return r, fmt.Errorf("unknown/duplicate key")
		}
		seen[key] = true
		var value json.RawMessage
		if e = d.Decode(&value); e != nil || string(value) == "null" {
			return r, fmt.Errorf("invalid field")
		}
	}
	t, e = d.Token()
	if e != nil || t != json.Delim('}') || len(seen) != len(allowed) || d.Decode(new(any)) != io.EOF {
		return r, fmt.Errorf("invalid envelope")
	}
	if e = json.Unmarshal(raw, &r); e != nil {
		return r, e
	}
	return r, r.Validate()
}
func (r Request) Validate() error {
	if r.Schema != Schema || (r.Operation != "provision_php_fpm" && r.Operation != "inspect_php_fpm") || !uuid.MatchString(r.JobID) || !uuid.MatchString(r.TenantID) || r.ExpectedRevision != 2 {
		return fmt.Errorf("invalid operation identity or revision")
	}
	_, e := planner.Render(planner.Request{SiteID: r.SiteID, Hostname: r.Hostname})
	return e
}
func (r Request) Fingerprint() string {
	r.Operation = "provision_php_fpm"
	b, _ := json.Marshal(r)
	return fmt.Sprintf("%x", sha256.Sum256(b))
}
func UnixUser(id string) string { h := sha256.Sum256([]byte(id)); return fmt.Sprintf("wcp_%x", h[:12]) }
func Reply(r Request, status string, replayed bool) Response {
	return Response{Schema, r.JobID, r.SiteID, status, replayed}
}

// dispatchFrame is the shared decode-before-handler boundary used by Serve.
// Invalid candidate frames never reach an effect handler or echo attacker IDs.
// This is not an IPC-B dispatcher and grants no canonical operation authority.
func dispatchFrame(input io.Reader, handler func(Request) Response) Response {
	r, err := Decode(input)
	if err != nil || handler == nil {
		return Reply(Request{}, "REJECTED", false)
	}
	return handler(r)
}
