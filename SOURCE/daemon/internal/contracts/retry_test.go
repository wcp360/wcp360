package contracts

import "testing"

func TestReadRetriesAreBoundedAndDoNotRedispatchMutations(t *testing.T) {
	c := mustCatalog(t)
	for _, a := range c.APIs() {
		for _, count := range []int{-1, 0, 1, 2, 3} {
			got := c.CanRetryRead(a.ID, ReadFailure{Retries: count, TransportFailed: true})
			want := a.Retry == "RR" && count >= 0 && count < 2
			if got != want {
				t.Fatalf("%s retry=%s count=%d got=%t", a.ID, a.Retry, count, got)
			}
		}
	}
	if c.CanRetryRead("API-WCP-9999", ReadFailure{TransportFailed: true}) {
		t.Fatal("unknown API retried")
	}
}

func TestReadRetryFailureClassification(t *testing.T) {
	c := mustCatalog(t)
	for _, status := range []int{200, 400, 401, 403, 404, 408, 409, 429, 500, 502, 503, 504} {
		got := c.CanRetryRead("API-WCP-0101", ReadFailure{HTTPStatus: status})
		if got != (status == 429 || status == 503) {
			t.Fatalf("unexpected retry for HTTP %d", status)
		}
	}
	for code := range c.errors {
		got := c.CanRetryRead("API-WCP-0101", ReadFailure{HTTPStatus: 503, ErrorCode: code})
		if got != (code == "RATE_LIMITED" || code == "SERVICE_UNAVAILABLE") {
			t.Fatalf("unexpected retry for error %s", code)
		}
	}
	for _, f := range []ReadFailure{
		{},
		{HTTPStatus: 503, ErrorCode: "UNRECOGNIZED"},
		{HTTPStatus: 429, ResponseExposed: true},
		{TransportFailed: true, ResponseExposed: true},
		{TransportFailed: true, ErrorCode: "UNKNOWN_OUTCOME"},
	} {
		if c.CanRetryRead("API-WCP-0101", f) {
			t.Fatalf("unsafe retry: %+v", f)
		}
	}
}
