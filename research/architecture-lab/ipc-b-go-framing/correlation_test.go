package ipcbframe

import (
	"strings"
	"testing"
)

// These are in-memory consistency checks, NOT authenticated IPC, a privileged
// execution, or a product QA TEST-IPC-#### assertion.
func fixtureParsedPair(t *testing.T) (Parsed, ParsedResponse) {
	t.Helper()
	request, err := DecodeRequestFrame(fixtureFrame(t, fixture()))
	if err != nil {
		t.Fatalf("request fixture rejected: %v", err)
	}
	response, err := DecodeResponseFrame(responseFrame(t, responseFixture()))
	if err != nil {
		t.Fatalf("response fixture rejected: %v", err)
	}
	return request, response
}

func TestRequestResponseCorrelationMatches(t *testing.T) {
	request, response := fixtureParsedPair(t)
	if err := MatchRequestResponseIdentifiers(request, response); err != nil {
		t.Fatalf("same identities must match: %v", err)
	}
	// Parsed SUCCEEDED is still unverified input; no effect is claimed.
	if response.Outcome != "SUCCEEDED" {
		t.Fatal("unexpected synthetic response fixture")
	}
}

func TestRequestResponseCorrelationRejectsCrossedIdentities(t *testing.T) {
	for _, tc := range []struct {
		name string
		mutate func(*ParsedResponse)
		reason string
	}{
		{
			name: "different request id",
			mutate: func(p *ParsedResponse) { p.RequestID = "99999999-9999-4999-8999-999999999999" },
			reason: "request_id mismatch",
		},
		{
			name: "different correlation id",
			mutate: func(p *ParsedResponse) { p.CorrelationID = "99999999-9999-4999-8999-999999999999" },
			reason: "correlation_id mismatch",
		},
		{
			name: "different allowed IPC operation",
			mutate: func(p *ParsedResponse) { p.Operation.OperationID = "linux_user.create" },
			reason: "ipc_operation_id mismatch",
		},
	} {
		t.Run(tc.name, func(t *testing.T) {
			request, response := fixtureParsedPair(t)
			tc.mutate(&response)
			err := MatchRequestResponseIdentifiers(request, response)
			if err == nil || !strings.Contains(err.Error(), tc.reason) {
				t.Fatalf("expected %q, got %v", tc.reason, err)
			}
			// Diagnostics must not echo the caller's untrusted identifiers.
			if strings.Contains(err.Error(), "99999999") {
				t.Fatal("untrusted identity leaked in error")
			}
		})
	}
}

func TestRequestResponseCorrelationRejectsParsedEmptyFields(t *testing.T) {
	for _, tc := range []struct {
		name string
		mutate func(*Parsed, *ParsedResponse)
	}{
		{"empty request ID", func(req *Parsed, _ *ParsedResponse) { req.RequestID = "" }},
		{"empty request correlation", func(req *Parsed, _ *ParsedResponse) { req.CorrelationID = "" }},
		{"empty request operation", func(req *Parsed, _ *ParsedResponse) { req.Operation.OperationID = "" }},
		{"empty response ID", func(_ *Parsed, resp *ParsedResponse) { resp.RequestID = "" }},
		{"empty response correlation", func(_ *Parsed, resp *ParsedResponse) { resp.CorrelationID = "" }},
		{"empty response operation", func(_ *Parsed, resp *ParsedResponse) { resp.Operation.OperationID = "" }},
	} {
		t.Run(tc.name, func(t *testing.T) {
			req, resp := fixtureParsedPair(t)
			tc.mutate(&req, &resp)
			err := MatchRequestResponseIdentifiers(req, resp)
			if err == nil || !strings.Contains(err.Error(), "missing request/response correlation identity") {
				t.Fatalf("expected missing-identity rejection, got %v", err)
			}
		})
	}
}

func TestRequestResponseCorrelationDoesNotTrustOutcome(t *testing.T) {
	req, resp := fixtureParsedPair(t)
	resp.Outcome = "CLAIMED_EFFECT_COMPLETED"
	// The helper only compares fixed correlation fields, deliberately NOT
	// response truth or outcome policy: the caller must still authenticate
	// the response, validate state and reconcile any uncertain effect.
	if err := MatchRequestResponseIdentifiers(req, resp); err != nil {
		t.Fatalf("correlation should be independent of unverified outcome: %v", err)
	}
}

func TestResponseFromDifferentKnownOperationIsRejected(t *testing.T) {
	req, _ := fixtureParsedPair(t)
	other := responseFixture()
	other["ipc_operation_id"] = "server.reboot"
	resp, err := DecodeResponseFrame(responseFrame(t, other))
	if err != nil {
		t.Fatalf("other known operation must be parsed as untrusted data: %v", err)
	}
	if err := MatchRequestResponseIdentifiers(req, resp); err == nil ||
		!strings.Contains(err.Error(), "ipc_operation_id mismatch") {
		t.Fatalf("known operation of another request must be rejected: %v", err)
	}
}
