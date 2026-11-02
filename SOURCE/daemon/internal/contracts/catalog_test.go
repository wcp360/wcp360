package contracts

import (
	"crypto/sha1"
	"encoding/json"
	"fmt"
	"os"
	"path/filepath"
	"strings"
	"sync"
	"testing"
)

func mustCatalog(t *testing.T) Catalog {
	t.Helper()
	c, err := Load()
	if err != nil {
		t.Fatal(err)
	}
	return c
}

func ownerSourcePath(t *testing.T, historicalPath string) string {
	t.Helper()
	// Keep the embedded historical path while admitting only reviewed relocations.
	switch historicalPath {
	case "specifications/api/WCP360_API_Contract_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md",
		"specifications/security/WCP360_Permission_Registry_v1.0_CANONICAL_IMPLEMENTATION_EXPORT.md",
		"specifications/ipc/WCP360_IPC_Contract_WCP360-IPC-B-1_CANONICAL_IMPLEMENTATION_EXPORT.md":
		return filepath.Join("../../../..", "documentation", historicalPath)
	default:
		t.Fatalf("unrecognized owner source path: %s", historicalPath)
		return ""
	}
}

func TestCanonicalSourcePins(t *testing.T) {
	for _, source := range mustCatalog(t).Sources() {
		data, err := os.ReadFile(ownerSourcePath(t, source.Path))
		if err != nil {
			t.Fatal(err)
		}
		header := []byte(fmt.Sprintf("blob %d\x00", len(data)))
		actual := fmt.Sprintf("%x", sha1.Sum(append(header, data...)))
		if actual != source.Blob {
			t.Fatalf("owner source changed: %s", source.Path)
		}
	}
}

func TestCompleteCatalogAndNamespaceSeparation(t *testing.T) {
	c := mustCatalog(t)
	if len(c.permissions) != 82 || len(c.apis) != 86 || len(c.operations) != 47 || len(c.errors) != 23 {
		t.Fatal("canonical coverage drift")
	}
	for _, id := range []PermissionID{"domains.record.update", "dns.change.apply", "dns.record.updated", "process.kill", "server.reboot", "server.reboot.requested", "server.reboot.completed", "shell.exec", "", "Servers.reboot"} {
		if _, ok := c.Permission(id); ok {
			t.Fatalf("non-permission accepted: %q", id)
		}
	}
	if !c.MatchesAPIBinding("API-WCP-0001", "domains.write", "domains.record.update") {
		t.Fatal("canonical DNS binding rejected")
	}
	for _, api := range c.APIs() {
		if !c.MatchesAPIBinding(api.ID, api.Permission, api.Action) {
			t.Fatalf("canonical binding rejected: %s", api.ID)
		}
		if c.MatchesAPIBinding(api.ID, "unknown.permission", api.Action) || c.MatchesAPIBinding(api.ID, api.Permission, "unknown.action") {
			t.Fatalf("unknown binding accepted: %s", api.ID)
		}
	}
	if c.MatchesAPIBinding("API-WCP-9999", "domains.write", "domains.record.update") {
		t.Fatal("unknown API accepted")
	}
}

func TestDistinctIPCActionAndOperation(t *testing.T) {
	c := mustCatalog(t)
	op, ok := c.Operation("server.reboot")
	if !ok || op.Action != "servers.reboot" || op.JobType != "server.reboot" {
		t.Fatal("IPC action and operation were collapsed")
	}
	if _, ok := c.Operation("servers.reboot"); ok {
		t.Fatal("permission treated as an IPC alias")
	}
	if _, ok := c.Operation("shell.exec"); ok {
		t.Fatal("generic privileged operation exposed")
	}
	if _, ok := c.API("API-WCP-0001"); !ok {
		t.Fatal("missing API")
	}
}

func TestExactErrorMembership(t *testing.T) {
	c := mustCatalog(t)
	if !c.KnownError("CONCURRENCY_CONFLICT") {
		t.Fatal("canonical conflict missing")
	}
	for _, code := range []ErrorCode{"CONCURRENT_MODIFICATION", "concurrency_conflict", "MADE_UP_ERROR", "AUTHORIZATION_DENIED\n", "", "IPC-WCP-1001"} {
		if c.KnownError(code) {
			t.Fatalf("invalid error accepted: %q", code)
		}
	}
}

func TestReturnedDataCannotMutateCatalog(t *testing.T) {
	c := mustCatalog(t)
	list := c.APIs()
	first := list[0]
	list[0].Permission = "forged.permission"
	again := c.APIs()
	if again[0] != first {
		t.Fatal("mutable API data leaked")
	}
	sources := c.Sources()
	expected := sources[0]
	sources[0].Blob = "forged"
	if c.Sources()[0] != expected {
		t.Fatal("mutable source metadata leaked")
	}
	for i := 1; i < len(again); i++ {
		if again[i-1].ID >= again[i].ID {
			t.Fatal("enumeration is not stable and sorted")
		}
	}
}

func TestZeroCatalogRejectsEverything(t *testing.T) {
	var c Catalog
	if _, ok := c.Permission("domains.write"); ok {
		t.Fatal("zero registry accepted permission")
	}
	if c.KnownError("INTERNAL_ERROR") || c.MatchesAPIBinding("", "", "") {
		t.Fatal("zero registry accepted membership")
	}
}

func TestConcurrentCatalogRead(t *testing.T) {
	c := mustCatalog(t)
	var wg sync.WaitGroup
	for i := 0; i < 20; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			for n := 0; n < 20; n++ {
				if !c.MatchesAPIBinding("API-WCP-0001", "domains.write", "domains.record.update") {
					t.Error("concurrent binding lookup failed")
				}
				c.Sources()[0].Blob = "local-copy"
			}
		}()
	}
	wg.Wait()
}

func TestRejectRegistryCorruption(t *testing.T) {
	for name, mutate := range map[string]func(*document){
		"schema":               func(d *document) { d.SchemaVersion++ },
		"count":                func(d *document) { d.APIs = d.APIs[1:] },
		"permission-duplicate": func(d *document) { d.Permissions[1] = d.Permissions[0] },
		"permission-inactive":  func(d *document) { d.Permissions[0].Status = "RETIRED" },
		"permission-upper":     func(d *document) { d.Permissions[0].ID = "INVALID" },
		"api-duplicate":        func(d *document) { d.APIs[1] = d.APIs[0] },
		"api-version":          func(d *document) { d.APIs[0].Version = "2.0" },
		"api-permission":       func(d *document) { d.APIs[0].Permission = "unknown.permission" },
		"operation-duplicate":  func(d *document) { d.Operations[1] = d.Operations[0] },
		"operation-invalid":    func(d *document) { d.Operations[0].Operation = "../bin/sh" },
		"error-duplicate":      func(d *document) { d.Errors[1] = d.Errors[0] },
		"error-lowercase":      func(d *document) { d.Errors[0] = "invalid_error" },
	} {
		t.Run(name, func(t *testing.T) {
			var d document
			if err := json.Unmarshal(registryJSON, &d); err != nil {
				t.Fatal(err)
			}
			mutate(&d)
			if _, err := build(d); err == nil {
				t.Fatal("corrupt registry accepted")
			}
		})
	}
}

func TestIdentifierGrammarBoundaries(t *testing.T) {
	for _, s := range []string{"a", "1a", "Ab", "ab/", "ab\x00", "aé", strings.Repeat("a", 129)} {
		if validLower(s) {
			t.Fatalf("invalid lower identifier accepted: %q", s)
		}
	}
	for _, s := range []string{"aa", "a.b-0_1", strings.Repeat("a", 128)} {
		if !validLower(s) {
			t.Fatalf("valid lower identifier rejected: %q", s)
		}
	}
	for _, s := range []string{"A", "1A", "Aa", "AB.C", "AB-", "AB\x00", strings.Repeat("A", 129)} {
		if validUpper(s) {
			t.Fatalf("invalid error grammar accepted: %q", s)
		}
	}
	if !validUpper("A_1") || !validUpper(strings.Repeat("A", 128)) {
		t.Fatal("valid upper identifier rejected")
	}
}

func TestEveryRegistryCellMatchesOwnerSource(t *testing.T) {
	c := mustCatalog(t)
	var d document
	if err := json.Unmarshal(registryJSON, &d); err != nil {
		t.Fatal(err)
	}
	for _, source := range c.Sources() {
		raw, err := os.ReadFile(ownerSourcePath(t, source.Path))
		if err != nil {
			t.Fatal(err)
		}
		text := string(raw)
		if strings.Contains(source.Path, "/api/") {
			for _, a := range d.APIs {
				cells := []string{string(a.ID), a.Version, a.Producer, a.Surface, a.Authentication, string(a.Permission), string(a.Action), a.Resource, a.Scope, a.RequestSchema, a.ResponseSchema, a.Errors, a.Timeout, a.Retry, a.Idempotency, a.Audit, a.Security, a.Compatibility, a.Test}
				row := "| `" + strings.Join(cells, "` | `") + "` |"
				if !strings.Contains(text, row+"\n") {
					t.Fatalf("API metadata differs from owner row: %s", a.ID)
				}
			}
			start := strings.Index(text, "## 5. Error Registry\n")
			if start < 0 {
				t.Fatal("error registry source section missing")
			}
			end := strings.Index(text[start:], "\nProfiles:")
			if end < 0 {
				t.Fatal("error registry source section missing")
			}
			for _, code := range d.Errors {
				if !strings.Contains(text[start:start+end], "\n"+string(code)+"\n") {
					t.Fatalf("error differs from owner source: %s", code)
				}
			}
		} else if strings.Contains(source.Path, "/ipc/") {
			for _, o := range d.Operations {
				cells := []string{o.ID, string(o.Action), o.JobType, string(o.Operation), o.ResourceIdentifier, o.InputSchema, o.Primitive, o.Timeout, o.Idempotency, o.Concurrency, o.Reconciliation, o.Test}
				if !strings.Contains(text, "| `"+strings.Join(cells, "` | `")+"` |\n") {
					t.Fatalf("IPC metadata differs from owner row: %s", o.ID)
				}
			}
		} else {
			for _, p := range d.Permissions {
				if !strings.Contains(text, "| `"+string(p.ID)+"` | "+p.Surface+" | "+p.Status+" |\n") {
					t.Fatalf("permission differs from owner row: %s", p.ID)
				}
			}
		}
	}
}
