package provisioning

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
)

type record struct {
	Request     Request `json:"request"`
	Fingerprint string  `json:"fingerprint"`
	State       string  `json:"state"`
}

// Root journals are still parsed strictly: corrupted or ambiguous state must not
// turn a restart into a fresh provisioning operation.
func decodeRecord(raw []byte) (record, error) {
	var r record
	if len(raw) > 4096 {
		return r, fmt.Errorf("journal size limit")
	}
	d := json.NewDecoder(bytes.NewReader(raw))
	t, e := d.Token()
	if e != nil || t != json.Delim('{') {
		return r, fmt.Errorf("journal object required")
	}
	fields := map[string]json.RawMessage{}
	for d.More() {
		t, e = d.Token()
		if e != nil {
			return r, e
		}
		key, ok := t.(string)
		if !ok || (key != "request" && key != "fingerprint" && key != "state") || fields[key] != nil {
			return r, fmt.Errorf("journal field rejected")
		}
		var value json.RawMessage
		if e = d.Decode(&value); e != nil || bytes.Equal(value, []byte("null")) {
			return r, fmt.Errorf("journal value rejected")
		}
		fields[key] = value
	}
	t, e = d.Token()
	if e != nil || t != json.Delim('}') || len(fields) != 3 || d.Decode(new(any)) != io.EOF {
		return r, fmt.Errorf("journal envelope rejected")
	}
	if r.Request, e = Decode(bytes.NewReader(fields["request"])); e != nil {
		return r, e
	}
	if json.Unmarshal(fields["fingerprint"], &r.Fingerprint) != nil || json.Unmarshal(fields["state"], &r.State) != nil || r.Request.Operation != "provision_php_fpm" || r.Fingerprint != r.Request.Fingerprint() || (r.State != "ACTIVE" && r.State != "PREPARING") {
		return r, fmt.Errorf("journal identity/state rejected")
	}
	return r, nil
}
