package provisioning

import (
	"encoding/json"
	"strings"
	"testing"
)

func TestJournalCannotConvertAmbiguousStateIntoActivation(t *testing.T) {
	r := record{validRequest(), validRequest().Fingerprint(), "ACTIVE"}
	raw, _ := json.Marshal(r)
	got, e := decodeRecord(raw)
	if e != nil || got != r {
		t.Fatal("active journal rejected", e)
	}
	r.State = "PREPARING"
	partial, _ := json.Marshal(r)
	if _, e := decodeRecord(partial); e != nil {
		t.Fatal("partial journal must remain recognizable", e)
	}
	base := strings.TrimSuffix(string(raw), "}")
	for _, s := range []string{base + `,"state":"ACTIVE"}`, base + `,"command":"id"}`, string(raw) + "{}", strings.Replace(string(raw), `"ACTIVE"`, `"QUEUED"`, 1), strings.Replace(string(raw), r.Fingerprint, strings.Repeat("0", 64), 1), strings.Replace(string(raw), `"request":{`, `"request":{"schema":null,`, 1), "null", strings.Repeat(" ", 4097)} {
		if _, e := decodeRecord([]byte(s)); e == nil {
			t.Fatal("corrupted journal accepted", s)
		}
	}
}
