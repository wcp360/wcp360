// Package contracts provides source-derived metadata, never authorization.
// A successful lookup or binding check does not permit execution.
package contracts

import (
	_ "embed"
	"encoding/json"
	"errors"
	"sort"
)

// Distinct types prevent accidental interchange of contract namespaces.
type PermissionID string
type ActionID string
type OperationID string
type APIID string
type ErrorCode string

//go:embed registry.json
var registryJSON []byte

type Permission struct {
	ID      PermissionID `json:"id"`
	Surface string       `json:"surface"`
	Status  string       `json:"status"`
}

type API struct {
	ID             APIID        `json:"id"`
	Version        string       `json:"version"`
	Producer       string       `json:"producer"`
	Surface        string       `json:"surface"`
	Authentication string       `json:"authentication"`
	Permission     PermissionID `json:"permission"`
	Action         ActionID     `json:"action"`
	Resource       string       `json:"resource"`
	Scope          string       `json:"scope"`
	RequestSchema  string       `json:"request_schema"`
	ResponseSchema string       `json:"response_schema"`
	Errors         string       `json:"errors"`
	Timeout        string       `json:"timeout"`
	Retry          string       `json:"retry"`
	Idempotency    string       `json:"idempotency"`
	Audit          string       `json:"audit"`
	Security       string       `json:"security"`
	Compatibility  string       `json:"compatibility"`
	Test           string       `json:"test"`
}

type Operation struct {
	ID                 string      `json:"id"`
	Action             ActionID    `json:"action"`
	JobType            string      `json:"job_type"`
	Operation          OperationID `json:"operation"`
	ResourceIdentifier string      `json:"resource_identifier"`
	InputSchema        string      `json:"input_schema"`
	Primitive          string      `json:"primitive"`
	Timeout            string      `json:"timeout"`
	Idempotency        string      `json:"idempotency"`
	Concurrency        string      `json:"concurrency"`
	Reconciliation     string      `json:"reconciliation"`
	Test               string      `json:"test"`
}

type Source struct {
	Path string `json:"path"`
	Blob string `json:"blob"`
}

type document struct {
	SchemaVersion int          `json:"schema_version"`
	Sources       []Source     `json:"sources"`
	Permissions   []Permission `json:"permissions"`
	APIs          []API        `json:"apis"`
	Operations    []Operation  `json:"operations"`
	Errors        []ErrorCode  `json:"errors"`
}

// Catalog has private immutable maps. Returned records contain only values.
// It is safe for concurrent reads; a zero Catalog rejects every lookup.
type Catalog struct {
	permissions map[PermissionID]Permission
	apis        map[APIID]API
	operations  map[OperationID]Operation
	errors      map[ErrorCode]bool
	sources     []Source
}

func Load() (Catalog, error) {
	var d document
	if err := json.Unmarshal(registryJSON, &d); err != nil {
		return Catalog{}, errors.New("invalid embedded contract registry")
	}
	return build(d)
}

func build(d document) (Catalog, error) {
	bad := errors.New("invalid contract registry membership")
	if d.SchemaVersion != 1 || len(d.Permissions) != 82 || len(d.APIs) != 86 || len(d.Operations) != 47 || len(d.Errors) != 23 || len(d.Sources) != 3 {
		return Catalog{}, bad
	}
	c := Catalog{
		permissions: make(map[PermissionID]Permission),
		apis:        make(map[APIID]API),
		operations:  make(map[OperationID]Operation),
		errors:      make(map[ErrorCode]bool),
		sources:     append([]Source(nil), d.Sources...),
	}
	for _, p := range d.Permissions {
		if !validLower(string(p.ID)) || p.Status != "ACTIVE" {
			return Catalog{}, bad
		}
		if _, exists := c.permissions[p.ID]; exists {
			return Catalog{}, bad
		}
		c.permissions[p.ID] = p
	}
	used := make(map[PermissionID]bool)
	for _, a := range d.APIs {
		if a.ID == "" || a.Version != "1.0" || !validLower(string(a.Action)) {
			return Catalog{}, bad
		}
		if _, exists := c.apis[a.ID]; exists {
			return Catalog{}, bad
		}
		if _, exists := c.permissions[a.Permission]; !exists {
			return Catalog{}, bad
		}
		used[a.Permission] = true
		c.apis[a.ID] = a
	}
	if len(used) != 82 {
		return Catalog{}, bad
	}
	for _, o := range d.Operations {
		if !validLower(string(o.Operation)) || !validLower(string(o.Action)) {
			return Catalog{}, bad
		}
		if _, exists := c.operations[o.Operation]; exists {
			return Catalog{}, bad
		}
		c.operations[o.Operation] = o
	}
	for _, code := range d.Errors {
		if !validUpper(string(code)) || c.errors[code] {
			return Catalog{}, bad
		}
		c.errors[code] = true
	}
	return c, nil
}

func validLower(s string) bool {
	if len(s) < 2 || len(s) > 128 || s[0] < 'a' || s[0] > 'z' {
		return false
	}
	for _, ch := range s[1:] {
		if !(ch >= 'a' && ch <= 'z' || ch >= '0' && ch <= '9' || ch == '_' || ch == '.' || ch == '-') {
			return false
		}
	}
	return true
}

func validUpper(s string) bool {
	if len(s) < 2 || len(s) > 128 || s[0] < 'A' || s[0] > 'Z' {
		return false
	}
	for _, ch := range s[1:] {
		if !(ch >= 'A' && ch <= 'Z' || ch >= '0' && ch <= '9' || ch == '_') {
			return false
		}
	}
	return true
}

func (c Catalog) Permission(id PermissionID) (Permission, bool) {
	p, ok := c.permissions[id]
	return p, ok
}

func (c Catalog) API(id APIID) (API, bool) {
	a, ok := c.apis[id]
	return a, ok
}

func (c Catalog) Operation(id OperationID) (Operation, bool) {
	o, ok := c.operations[id]
	return o, ok
}

func (c Catalog) KnownError(code ErrorCode) bool { return c.errors[code] }

func (c Catalog) Sources() []Source { return append([]Source(nil), c.sources...) }

func (c Catalog) APIs() []API {
	result := make([]API, 0, len(c.apis))
	for _, a := range c.apis {
		result = append(result, a)
	}
	sort.Slice(result, func(i, j int) bool { return result[i].ID < result[j].ID })
	return result
}

// MatchesAPIBinding checks registry identity only, never effective permission,
// session, resource, tenant, policy, approval, entitlement or parent IPC binding.
func (c Catalog) MatchesAPIBinding(id APIID, permission PermissionID, action ActionID) bool {
	a, ok := c.apis[id]
	return ok && a.Permission == permission && a.Action == action
}

// Counts is inventory only, never implemented endpoint or granted permission counts.
type Counts struct {
	Permissions int `json:"permissions"`
	APIs        int `json:"api_contracts"`
	Operations  int `json:"ipc_operations"`
	Errors      int `json:"error_codes"`
}

func (c Catalog) Counts() Counts {
	return Counts{Permissions: len(c.permissions), APIs: len(c.apis), Operations: len(c.operations), Errors: len(c.errors)}
}
