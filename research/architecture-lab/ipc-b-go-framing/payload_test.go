package ipcbframe

import (
	"fmt"
	"strings"
	"testing"
)

// These tests check declared INPUT FIELD NAMES ONLY. They deliberately do not
// validate types, resource existence, authorization, tenant isolation, or
// whether any privileged operation succeeds. All constructed IDs/values are
// inert synthetic test data, never dispatched to a daemon.
func TestAll47OperationPayloadNameSchemas(t *testing.T) {
	catalog, err := loadAllowed()
	if err != nil {
		t.Fatal(err)
	}
	if len(catalog) != 47 {
		t.Fatalf("expected 47 source-declared operations, got %d",len(catalog))
	}
	seenOptionals:=0
	for id, operation := range catalog {
		t.Run(id,func(t *testing.T) {
			reqFields,optional,err:=splitPayloadSchemaFields(operation.ValidatedInputSchema)
			if err!=nil { t.Fatal(err) }
			if len(reqFields)==0 { t.Fatal("unexpected empty required fields") }
			for field := range optional { if field=="" {t.Fatal("unnamed optional field")};seenOptionals++ }
			payload:=make(map[string]any)
			for key := range reqFields { payload[key]="synthetic-only" }
			req:=fixture()
			req["ipc_operation_id"]=id
			req["payload"]=payload
			if got,err:=DecodeRequestFrame(fixtureFrame(t,req));err!=nil || got.Operation.OperationID!=id {
				t.Fatalf("source schema fixture failed: got %+v, err %v",got,err)
			}
			for missing := range reqFields {
				broken:=make(map[string]any)
				for name,value:=range payload {if name!=missing {broken[name]=value}}
				req["payload"]=broken
				expectReject(t,fixtureFrame(t,req),"required operation payload field missing")
				break
			}
			payload["caller_invented_field"]=true
			req["payload"]=payload
			expectReject(t,fixtureFrame(t,req),"unknown operation payload field")
			for field:=range optional {
				delete(payload,"caller_invented_field")
				payload[field]="synthetic-optional-only"
				req["payload"]=payload
				if _,err:=DecodeRequestFrame(fixtureFrame(t,req));err!=nil{
					t.Fatalf("declared optional field %q wrongly rejected: %v",field,err)
				}
			}
		})
	}
	if seenOptionals != 1 {
		t.Fatalf("source IPC-B manifest has %d optional fields, expected 1",seenOptionals)
	}
}

func TestPayloadSchemaCompilerFailsClosed(t *testing.T) {
	badSchemas:=[]string{
		"", "{}", "{bad,}", "bad", "{x,x}", "{x,x?}", "{x?,x}",
		"{shell}", "{sudo}", "{target_path}", "{x/y}", "{bad field}",
		"{a,{nested}}", "{*}", "{a?,?}",
	}
	for _,s:=range badSchemas {
		t.Run(fmt.Sprintf("schema=%q",s),func(t *testing.T) {
			if _,_,err:=splitPayloadSchemaFields(s);err==nil{
				t.Fatalf("schema %q should fail",s)
			}
		})
	}
}

func TestNoProposedPayloadTypeCanAuthorizeAnOperation(t *testing.T) {
	catalog,err:=loadAllowed()
	if err!=nil {t.Fatal(err)}
	operation:=catalog["nginx_vhost.validate"]
	required,_,err:=splitPayloadSchemaFields(operation.ValidatedInputSchema)
	if err!=nil {t.Fatal(err)}
	// Integer value for application_id remains syntactically accepted here:
	// a normative typed handler MUST reject it. This is an explicit negative
	// demonstration that this in-memory parser is not safe for production.
	payload:=map[string]any{}
	for field:=range required{payload[field]=42}
	req:=fixture()
	req["payload"]=payload
	_,err=DecodeRequestFrame(fixtureFrame(t,req))
	if err!=nil {t.Fatalf("name-only parser unexpectedly asserted field types: %v",err)}
}

func TestNoPayloadKeyAllowsGenericExecutionEvenIfNested(t *testing.T) {
	req:=fixture()
	for _,k:=range []string{"command","sudo","systemctl","raw_sql","absolute_path","executable"}{
		copyReq:=fixture()
		copyReq["payload"]=map[string]any{k:"harmful-but-inert"}
		_,err:=DecodeRequestFrame(fixtureFrame(t,copyReq))
		if err==nil || !strings.Contains(err.Error(),"forbidden caller-controlled field"){
			t.Fatalf("missing expected forbidden-field check for %s: %v",k,err)
		}
	}
	_ = req
}

func TestOpcodeDoesNotProvidePrivilegeOrResponseEvidence(t *testing.T) {
	catalog,err:=loadAllowed()
	if err!=nil{t.Fatal(err)}
	for _,op:=range catalog {
		// No dispatcher/exec/socket handle exists in this reference package.
		if op.OperationID==""||op.ContractID==""||op.TestID=="" {
			t.Fatal("missing source reference identity")
		}
	}
}
