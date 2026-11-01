package provisioning

import (
	"bytes"
	"encoding/json"
	"strings"
	"testing"
)

// These targets exercise pure parsers only. They never start an agent/broker,
// execute commands, create accounts, open sockets or change managed files.
func FuzzProvisionEnvelope(f *testing.F) {
	seed, _ := json.Marshal(validRequest())
	f.Add(seed)
	inspection := validRequest()
	inspection.Operation = "inspect_php_fpm"
	seed, _ = json.Marshal(inspection)
	f.Add(seed)
	for _, raw := range []string{"null", "{}", "[]", `{"operation":"shell.exec"}`, `{"schema":null,"schema":"duplicate"}`, strings.Repeat(" ", 4097)} {
		f.Add([]byte(raw))
	}
	f.Fuzz(func(t *testing.T, raw []byte) {
		r, err := Decode(bytes.NewReader(raw))
		if err != nil {
			return
		}
		if len(raw) > 4096 || r.Validate() != nil {
			t.Fatal("parser accepted an oversized or invalid operation")
		}
		encoded, err := json.Marshal(r)
		if err != nil {
			t.Fatal(err)
		}
		again, err := Decode(bytes.NewReader(encoded))
		if err != nil || again != r {
			t.Fatal("accepted identity did not survive canonical encoding", err)
		}
		fingerprint := r.Fingerprint()
		r.Operation = "inspect_php_fpm"
		if r.Fingerprint() != fingerprint {
			t.Fatal("inspection changed the bound operation identity")
		}
	})
}

func FuzzActivationJournal(f *testing.F) {
	r := validRequest()
	for _, state := range []string{"ACTIVE", "PREPARING"} {
		seed, _ := json.Marshal(record{r, r.Fingerprint(), state})
		f.Add(seed)
	}
	for _, raw := range []string{"null", "{}", `{"state":"ACTIVE","state":"PREPARING"}`, `{"request":null}`, strings.Repeat(" ", 4097)} {
		f.Add([]byte(raw))
	}
	f.Fuzz(func(t *testing.T, raw []byte) {
		r, err := decodeRecord(raw)
		if err != nil {
			return
		}
		if len(raw) > 4096 || r.Request.Validate() != nil || r.Request.Operation != "provision_php_fpm" || r.Fingerprint != r.Request.Fingerprint() || (r.State != "ACTIVE" && r.State != "PREPARING") {
			t.Fatal("parser accepted an unbound or invalid journal")
		}
		encoded, err := json.Marshal(r)
		if err != nil {
			t.Fatal(err)
		}
		again, err := decodeRecord(encoded)
		if err != nil || again != r {
			t.Fatal("journal identity or state changed on canonical encoding", err)
		}
	})
}
