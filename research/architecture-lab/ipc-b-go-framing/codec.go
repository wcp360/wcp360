// Package ipcbframe contains an inert research-only decoder for the already
// specified WCP360-IPC-B/1 physical frame and operation-ID inventory.
//
// This is NOT WCP360 production code. It cannot access sockets, peer creds,
// secrets, PostgreSQL, systemd, filesystems, root or any privileged primitive.
// The parser's "valid" result conveys no authorization or dispatch right.
// Production integration requires final owner-approved contracts, tests and
// explicit SC-M01 authorization (CODE-BASELINE remains NONE).
package ipcbframe

import (
	"bytes"
	_ "embed"
	"encoding/binary"
	"encoding/json"
	"errors"
	"fmt"
	"io"
	"regexp"
	"strings"
	"unicode/utf8"
)

const (
	ProtocolVersion = "WCP360-IPC-B/1"
	RequestMaxBytes = 65_536 // canonical IPC-B request payload maximum
	ResponseMaxBytes = 262_144 // canonical IPC-B response payload maximum
	// Research-only limits: depth and tokens are defensive hypotheses requiring
	// Security/API owner review, NOT published normative IPC-B limits.
	researchMaxDepth = 16
	researchMaxTokens = 4096
)

//go:embed operations.json
var operationsJSON []byte

type operationEntry struct {
	ValidatedInputSchema string `json:"validated_input_schema"`
	ActionID string `json:"action_id"`
	ContractID     string `json:"contract_id"`
	IPCOperationID string `json:"ipc_operation_id"`
	TestID         string `json:"test_id"`
}

type researchCatalog struct {
	Status            string           `json:"status"`
	SourceContract    string           `json:"source_contract_version"`
	SourceGitBlobSHA  string           `json:"source_git_blob_sha"`
	Operations        []operationEntry `json:"operations"`
}

type Reference struct {
	ContractID string
	ActionID string
	OperationID string
	TestID string
	ValidatedInputSchema string
}

// Parsed is deliberately a data-only research result.
// No code path accepts a Parsed value as authorization or executable command.
type Parsed struct {
	RequestID     string
	CorrelationID string
	Operation     Reference
	ActionID      string
	ResourceType  string
	ResourceID    string
	// Opaque context reference only, never a bearer credential.
	AuthorizationContextRef string
}

var typedID = regexp.MustCompile("^[a-z][a-z0-9_.-]{1,127}$")

var requiredFields = map[string]bool{
	"protocol_version": true,
	"op_version": true,
	"request_id": true,
	"correlation_id": true,
	"action_id": true,
	"ipc_operation_id": true,
	"resource_type": true,
	"resource_id": true,
	"authorization_context_ref": true,
	"deadline_ms": true,
	"payload": true,
}

var optionalFields = map[string]bool{"job_id": true}

// Forbidden caller-supplied keys are a research rejection subset. The real
// canonical per-operation payload schema must be validated separately by its
// final owner-approved handler; this package has NO privileged dispatcher.
var forbiddenKeys = map[string]bool{
	"shell": true,
	"shell.exec": true,
	"exec": true,
	"command": true,
	"command_string": true,
	"executable": true,
	"executable_path": true,
	"argv": true,
	"env": true,
	"environment": true,
	"working_directory": true,
	"sudo": true,
	"systemctl": true,
	"service_name": true,
	"private_key": true,
	"token": true,
	"bearer_token": true,
	"password": true,
	"cookie": true,
	"role": true,
	"permission_id": true,
	"raw_sql": true,
	"signal": true,
	"signal_number": true,
	"absolute_path": true,
	"target_path": true,
	"filesystem_path": true,
}

func loadAllowed() (map[string]Reference, error) {
	var catalog researchCatalog
	if err := json.Unmarshal(operationsJSON, &catalog); err != nil {
		return nil, errors.New("research operation manifest not readable")
	}
	if catalog.Status != "DERIVED_RESEARCH_ONLY_NONCANONICAL" ||
		catalog.SourceContract != ProtocolVersion ||
		len(catalog.Operations) != 47 {
		return nil, errors.New("research operation manifest provenance/count mismatch")
	}
	result := make(map[string]Reference, len(catalog.Operations))
	contractIDs := make(map[string]bool)
	for _, x := range catalog.Operations {
		if !typedID.MatchString(x.IPCOperationID) ||
			!typedID.MatchString(x.ActionID) ||
			!strings.HasPrefix(x.ContractID, "IPC-WCP-") ||
			x.TestID != strings.Replace(x.ContractID, "IPC-WCP-", "TEST-IPC-", 1) {
			return nil, errors.New("research operation catalog identifier mismatch")
		}
		if _, ok := result[x.IPCOperationID]; ok || contractIDs[x.ContractID] {
			return nil, errors.New("research operation catalog duplicate")
		}
		if _, _, err := splitPayloadSchemaFields(x.ValidatedInputSchema); err != nil {
			return nil, fmt.Errorf("research operation input schema invalid: %w", err)
		}
		result[x.IPCOperationID] = Reference{
			ContractID: x.ContractID,
			ActionID: x.ActionID,
			OperationID: x.IPCOperationID,
			TestID: x.TestID,
			ValidatedInputSchema: x.ValidatedInputSchema,
		}
		contractIDs[x.ContractID] = true
	}
	return result, nil
}

// splitPayloadSchemaFields interprets only the flat declared FIELD NAMES from
// the existing IPC-B owner export. It CANNOT establish field types or value
// validity, authorization/tenant scope, path safety or real OS side effects.
// It rejects unsupported nested/wildcard schema grammar instead of guessing.
func splitPayloadSchemaFields(schema string) (map[string]bool, map[string]bool, error) {
	if len(schema) < 3 || schema[0] != '{' || schema[len(schema)-1] != '}' {
		return nil, nil, errors.New("unsupported operation schema envelope")
	}
	required, optional := map[string]bool{}, map[string]bool{}
	parts := strings.Split(schema[1:len(schema)-1], ",")
	if len(parts) == 0 {
		return nil, nil, errors.New("empty operation schema")
	}
	for _, raw := range parts {
		name := strings.TrimSpace(raw)
		optionalField := strings.HasSuffix(name, "?")
		if optionalField {
			name = strings.TrimSuffix(name, "?")
		}
		if !typedID.MatchString(name) || forbiddenKeys[strings.ToLower(name)] {
			return nil, nil, errors.New("invalid operation schema field")
		}
		if required[name] || optional[name] {
			return nil, nil, errors.New("duplicate operation schema field")
		}
		if optionalField { optional[name] = true } else { required[name] = true }
	}
	return required, optional, nil
}

// validatePayloadFieldNames enforces required/allowed KEYS ONLY, not value
// types or bound resource objects; returning success never authorizes dispatch.
func validatePayloadFieldNames(operation Reference, raw map[string]any) error {
	required, optional, err := splitPayloadSchemaFields(operation.ValidatedInputSchema)
	if err != nil { return err }
	for field := range required {
		if _, ok := raw[field]; !ok {
			return errors.New("required operation payload field missing")
		}
	}
	for field := range raw {
		if !required[field] && !optional[field] {
			return errors.New("unknown operation payload field")
		}
	}
	return nil
}

// parseValue rejects duplicate keys recursively, in particular unicode-escaped
// aliases of a field name. encoding/json.Unmarshal alone accepts duplicates.
func parseValue(dec *json.Decoder, depth int, tokens *int) (any, error) {
	if depth > researchMaxDepth || *tokens >= researchMaxTokens {
		return nil, errors.New("research parser nesting/token limit exceeded")
	}
	tok, err := dec.Token()
	if err != nil {
		return nil, errors.New("malformed JSON token")
	}
	*tokens++
	switch v := tok.(type) {
	case json.Delim:
		switch v {
		case '{':
			object := make(map[string]any)
			for dec.More() {
				keyValue, err := dec.Token()
				if err != nil {
					return nil, errors.New("malformed JSON object key")
				}
				key, ok := keyValue.(string)
				if !ok {
					return nil, errors.New("non-string JSON object key")
				}
				*tokens++
				if *tokens >= researchMaxTokens {
					return nil, errors.New("research token limit exceeded")
				}
				if _, exists := object[key]; exists {
					return nil, errors.New("duplicate JSON object key")
				}
				if forbiddenKeys[strings.ToLower(key)] {
					return nil, errors.New("forbidden caller-controlled field")
				}
				value, err := parseValue(dec, depth+1, tokens)
				if err != nil {
					return nil, err
				}
				object[key] = value
			}
			end, err := dec.Token()
			if err != nil || end != json.Delim('}') {
				return nil, errors.New("invalid JSON object termination")
			}
			return object, nil
		case '[':
			array := make([]any, 0)
			for dec.More() {
				child, err := parseValue(dec, depth+1, tokens)
				if err != nil {
					return nil, err
				}
				array = append(array, child)
			}
			end, err := dec.Token()
			if err != nil || end != json.Delim(']') {
				return nil, errors.New("invalid JSON array termination")
			}
			return array, nil
		default:
			return nil, errors.New("unexpected JSON closing delimiter")
		}
	case json.Number, string, bool, nil:
		return v, nil
	default:
		return nil, errors.New("unsupported JSON token")
	}
}

func fieldString(m map[string]any, name string) (string, error) {
	value, ok := m[name].(string)
	if !ok || value == "" || strings.ContainsRune(value, rune(0)) {
		return "", fmt.Errorf("%s: missing/invalid string", name)
	}
	return value, nil
}

// decodeFrameMap validates the shared IPC-B physical envelope in memory only.
// The caller separately validates request or response field sets.
func decodeFrameMap(frame []byte, max uint32, kind string) (map[string]any, error) {
	if len(frame) < 4 {
		return nil, errors.New("missing frame length")
	}
	length := binary.BigEndian.Uint32(frame[:4])
	if length == 0 || length > max {
		return nil, fmt.Errorf("invalid %s frame length", kind)
	}
	if len(frame) != int(length)+4 {
		return nil, fmt.Errorf("truncated or trailing %s frame", kind)
	}
	body := frame[4:]
	if !utf8.Valid(body) {
		return nil, fmt.Errorf("invalid UTF-8 %s", kind)
	}
	decoder := json.NewDecoder(bytes.NewReader(body))
	decoder.UseNumber()
	tokens := 0
	parsed, err := parseValue(decoder, 0, &tokens)
	if err != nil {
		return nil, err
	}
	if _, err := decoder.Token(); err != io.EOF {
		return nil, fmt.Errorf("trailing JSON or invalid %s", kind)
	}
	object, ok := parsed.(map[string]any)
	if !ok {
		return nil, fmt.Errorf("%s must be JSON object", kind)
	}
	return object, nil
}

// DecodeRequestFrame parses exactly one in-memory framed research message.
// This function deliberately does not open/read a network connection. It
// does not establish SO_PEERCRED, authn/authz, idempotency or real deadlines.
func DecodeRequestFrame(frame []byte) (Parsed, error) {
	var empty Parsed
	request, err := decodeFrameMap(frame, RequestMaxBytes, "request")
	if err != nil {
		return empty, err
	}
	for field := range requiredFields {
		if _, present := request[field]; !present {
			return empty, errors.New("required request field missing")
		}
	}
	for field := range request {
		if !requiredFields[field] && !optionalFields[field] {
			return empty, errors.New("unknown request field")
		}
	}
	protocol, err := fieldString(request, "protocol_version")
	if err != nil || protocol != ProtocolVersion {
		return empty, errors.New("unsupported IPC-B protocol version")
	}
	for _, field := range []string{"op_version", "deadline_ms"} {
		if request[field] == nil {
			return empty, errors.New("required opaque version/deadline field missing")
		}
	}
	if _, ok := request["payload"].(map[string]any); !ok {
		return empty, errors.New("IPC-B payload must be object")
	}
	for _, field := range []string{"request_id", "correlation_id",
		"authorization_context_ref", "resource_id"} {
		if _, err := fieldString(request, field); err != nil {
			return empty, err
		}
	}
	if optionalJob, present := request["job_id"]; present {
		if _, ok := optionalJob.(string); !ok || optionalJob == "" {
			return empty, errors.New("optional job_id must be nonempty string")
		}
	}
	for _, field := range []string{"action_id", "resource_type", "ipc_operation_id"} {
		value, err := fieldString(request, field)
		if err != nil || !typedID.MatchString(value) {
			return empty, errors.New("invalid typed IPC-B identifier")
		}
	}
	catalog, err := loadAllowed()
	if err != nil {
		return empty, err
	}
	op := request["ipc_operation_id"].(string)
	registered, ok := catalog[op]
	if !ok {
		return empty, errors.New("unknown IPC-B operation; no fallback")
	}
	// A valid IPC operation alone grants no privilege. Reject even syntactically
	// when its caller-supplied action differs from the canonical IPC-B row.
	if request["action_id"].(string) != registered.ActionID {
		return empty, errors.New("action_id / ipc_operation_id source binding mismatch")
	}
	if err := validatePayloadFieldNames(registered, request["payload"].(map[string]any)); err != nil {
		return empty, err
	}
	// IMPORTANT: Returning this Parsed object does not validate per-operation
	// payload schemas, permission/action binding, peer identity, tenant/resource
	// scope, active authorization context, deadline meaning or retry semantics.
	// It must NEVER trigger system/privileged side effects.
	return Parsed{
		RequestID: request["request_id"].(string),
		CorrelationID: request["correlation_id"].(string),
		Operation: registered,
		ActionID: request["action_id"].(string),
		ResourceType: request["resource_type"].(string),
		ResourceID: request["resource_id"].(string),
		AuthorizationContextRef: request["authorization_context_ref"].(string),
	}, nil
}

/* Response parsing is also INERT. It validates the canonical response
envelope, not the truth of an external effect, audit evidence or outcome. */

var responseRequiredFields = map[string]bool{
	"protocol_version": true,
	"request_id": true,
	"correlation_id": true,
	"ipc_operation_id": true,
	"outcome": true,
	"reconciliation_required": true,
}
var responseOptionalFields = map[string]bool{
	"error_code": true,
	"result": true,
}
var canonicalIPCErrorCodes = map[string]bool{
	"IPC_CALLER_UNTRUSTED": true,
	"IPC_SCHEMA_INVALID": true,
	"IPC_OPERATION_NOT_ALLOWED": true,
	"AUTHORIZATION_DENIED": true,
	"VALIDATION_FAILED": true,
	"RESOURCE_NOT_FOUND": true,
	"RESOURCE_CONFLICT": true,
	"CONCURRENCY_CONFLICT": true,
	"OPERATION_TIMEOUT": true,
	"DEPENDENCY_FAILURE": true,
	"UNKNOWN_OUTCOME": true,
	"INTERNAL_ERROR": true,
}

// ParsedResponse remains untrusted data until a separate caller compares
// request/correlation IDs, authenticated peer, persisted intent, expected
// operation/target, observed effect and durable audit evidence.
type ParsedResponse struct {
	RequestID string
	CorrelationID string
	Operation Reference
	Outcome string
	ErrorCode string
	ReconciliationRequired bool
	HasResult bool
}

// DecodeResponseFrame is an inert in-memory validation of one IPC-B response.
// A parsed SUCCESS string does NOT prove the privileged effect succeeded.
func DecodeResponseFrame(frame []byte) (ParsedResponse, error) {
	var empty ParsedResponse
	response, err := decodeFrameMap(frame, ResponseMaxBytes, "response")
	if err != nil {
		return empty, err
	}
	for field := range responseRequiredFields {
		if _, ok := response[field]; !ok {
			return empty, errors.New("required response field missing")
		}
	}
	for field := range response {
		if !responseRequiredFields[field] && !responseOptionalFields[field] {
			return empty, errors.New("unknown response field")
		}
	}
	version, err := fieldString(response, "protocol_version")
	if err != nil || version != ProtocolVersion {
		return empty, errors.New("unsupported IPC-B protocol version")
	}
	for _, name := range []string{"request_id", "correlation_id", "outcome"} {
		if _, err := fieldString(response, name); err != nil {
			return empty, err
		}
	}
	operation, err := fieldString(response, "ipc_operation_id")
	if err != nil || !typedID.MatchString(operation) {
		return empty, errors.New("invalid response IPC-B operation")
	}
	validity, ok := response["reconciliation_required"].(bool)
	if !ok {
		return empty, errors.New("reconciliation_required must be boolean")
	}
	code := ""
	if raw, present := response["error_code"]; present && raw != nil {
		value, isString := raw.(string)
		if !isString || !canonicalIPCErrorCodes[value] {
			return empty, errors.New("unknown response error_code")
		}
		code = value
	}
	if (response["outcome"] == "UNKNOWN_OUTCOME" || code == "UNKNOWN_OUTCOME") && !validity {
		return empty, errors.New("UNKNOWN_OUTCOME requires reconciliation")
	}
	catalog, err := loadAllowed()
	if err != nil {
		return empty, err
	}
	known, ok := catalog[operation]
	if !ok {
		return empty, errors.New("unknown IPC-B response operation")
	}
	_, hasResult := response["result"]
	return ParsedResponse{
		RequestID: response["request_id"].(string),
		CorrelationID: response["correlation_id"].(string),
		Operation: known,
		Outcome: response["outcome"].(string),
		ErrorCode: code,
		ReconciliationRequired: validity,
		HasResult: hasResult,
	}, nil
}
