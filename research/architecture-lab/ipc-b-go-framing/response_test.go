package ipcbframe

import (
	"encoding/json"
	"strings"
	"testing"
)

func responseFixture() map[string]any {
	return map[string]any{
		"protocol_version": ProtocolVersion,
		"request_id": "11111111-1111-4111-8111-111111111111",
		"correlation_id": "22222222-2222-4222-8222-222222222222",
		"ipc_operation_id": "nginx_vhost.validate",
		"outcome": "SUCCEEDED",
		"reconciliation_required": false,
		"result": map[string]any{"valid": true},
	}
}

func responseFrame(t *testing.T, message map[string]any) []byte {
	t.Helper()
	b, err := json.Marshal(message)
	if err != nil {
		t.Fatal(err)
	}
	return framed(b)
}

func expectResponseReject(t *testing.T, payload []byte, expected string) {
	t.Helper()
	_, err := DecodeResponseFrame(payload)
	if err == nil || !strings.Contains(err.Error(), expected) {
		t.Fatalf("expected response rejection containing %q, got %v", expected, err)
	}
}

func TestResponseInertDataOnly(t *testing.T) {
	got, err := DecodeResponseFrame(responseFrame(t, responseFixture()))
	if err != nil {
		t.Fatal(err)
	}
	if got.Operation.ContractID != "IPC-WCP-1009" ||
		got.Operation.OperationID != "nginx_vhost.validate" ||
		got.Outcome != "SUCCEEDED" || got.ReconciliationRequired ||
		!got.HasResult {
		t.Fatalf("unexpected parsed response data: %+v", got)
	}
	// The string SUCCEEDED is unverified. This does not authenticate the
	// sender or confirm the effect/DB audit result.
}

func TestResponseErrorRegistryCodes(t *testing.T) {
	for code := range canonicalIPCErrorCodes {
		t.Run(code, func(t *testing.T) {
			response := responseFixture()
			response["outcome"] = "FAILED"
			response["error_code"] = code
			if code == "UNKNOWN_OUTCOME" {
				response["outcome"] = "UNKNOWN_OUTCOME"
				response["reconciliation_required"] = true
			}
			result, err := DecodeResponseFrame(responseFrame(t, response))
			if err != nil || result.ErrorCode != code {
				t.Fatalf("error registry %s not accepted: %v", code, err)
			}
		})
	}
}

func TestUnknownOutcomeRequiresReconciliation(t *testing.T) {
	a := responseFixture()
	a["outcome"] = "UNKNOWN_OUTCOME"
	expectResponseReject(t, responseFrame(t,a), "requires reconciliation")
	a = responseFixture()
	a["error_code"] = "UNKNOWN_OUTCOME"
	expectResponseReject(t, responseFrame(t,a), "requires reconciliation")
	a["reconciliation_required"] = true
	if _, err := DecodeResponseFrame(responseFrame(t,a)); err != nil {
		t.Fatalf("syntactic UNKNOWN_OUTCOME response wrongly rejected: %v",err)
	}
}

func TestResponseBadRequiredFields(t *testing.T) {
	for _, field := range []string{
		"protocol_version", "request_id", "correlation_id",
		"ipc_operation_id", "outcome", "reconciliation_required",
	} {
		t.Run(field, func(t *testing.T) {
			response := responseFixture()
			delete(response,field)
			expectResponseReject(t,responseFrame(t,response),"required response field missing")
		})
	}
}

func TestResponseWrongTypesRejected(t *testing.T) {
	tests := []struct{field string; value any; expect string}{
		{"protocol_version", 1, "unsupported IPC-B protocol"},
		{"request_id", nil, "missing/invalid string"},
		{"correlation_id", true, "missing/invalid string"},
		{"ipc_operation_id", "invalid/op", "invalid response IPC-B operation"},
		{"outcome", 1, "missing/invalid string"},
		{"reconciliation_required", "yes", "must be boolean"},
		{"error_code", "MADE_UP", "unknown response error_code"},
		{"error_code", 1, "unknown response error_code"},
	}
	for _, tt := range tests {
		t.Run(tt.field,func(t *testing.T){
			msg:=responseFixture()
			msg[tt.field]=tt.value
			expectResponseReject(t,responseFrame(t,msg),tt.expect)
		})
	}
}

func TestResponseUnknownOperationAndUnknownFields(t *testing.T) {
	r := responseFixture()
	r["ipc_operation_id"] = "nonexistent.operation"
	expectResponseReject(t,responseFrame(t,r),"unknown IPC-B response operation")
	r=responseFixture()
	r["permission_id"]="servers.reboot"
	expectResponseReject(t,responseFrame(t,r),"forbidden caller-controlled field")
	r=responseFixture()
	r["unknown_field"]="x"
	expectResponseReject(t,responseFrame(t,r),"unknown response field")
}

func TestResponseFramesEnforce262144BytePayloadLimit(t *testing.T) {
	expectResponseReject(t, []byte{0, 4, 0, 1}, "invalid response frame length")
	expectResponseReject(t, framed([]byte{}), "invalid response frame length")
	raw:=responseFrame(t,responseFixture())
	expectResponseReject(t,raw[:len(raw)-1],"truncated or trailing response frame")
	expectResponseReject(t,append(append([]byte{},raw...),byte(1)),"truncated or trailing response frame")
	expectResponseReject(t,framed([]byte{0xff}),"invalid UTF-8 response")
}

func TestResponseDuplicateKeysAndSensitiveResultRejected(t *testing.T) {
	for _, raw := range [][]byte{
		[]byte("{\"outcome\":1,\"outcome\":2}"),
		[]byte("{\"result\":{\"a\":1,\"a\":2}}"),
		[]byte("{\"result\":{\"token\":\"bearer\"}}"),
	} {
		if _, err:=DecodeResponseFrame(framed(raw));err==nil {
			t.Fatal("malformed or sensitive response data accepted")
		}
	}
}

func TestRequestAndResponseSchemasCannotBeMixed(t *testing.T) {
	expectResponseReject(t,fixtureFrame(t,fixture()),"required response field missing")
	expectReject(t,responseFrame(t,responseFixture()),"required request field missing")
}

func TestResponseNullErrorCodeAllowedButUnknownCodeRejected(t *testing.T) {
	msg:=responseFixture()
	msg["error_code"]=nil
	got,err:=DecodeResponseFrame(responseFrame(t,msg))
	if err!=nil || got.ErrorCode!=""{
		t.Fatalf("optional null error code handling unexpected: %v",err)
	}
}

func FuzzDecodeResponseFrameNeverPanics(f *testing.F) {
	msg,_:=json.Marshal(responseFixture())
	f.Add(framed(msg))
	f.Add([]byte{})
	f.Add(framed([]byte("{\"outcome\":1,\"outcome\":2}")))
	f.Fuzz(func(t *testing.T,input []byte){
		_,_=DecodeResponseFrame(input)
	})
}
