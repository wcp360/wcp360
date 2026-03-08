// ======================================================================
// WCP 360 | V0.1.0 | internal/api/handlers/helpers_test.go
// ======================================================================

package handlers

import (
	"net/http/httptest"
	"testing"
)

func TestParseLimit(t *testing.T) {
	cases := []struct{ query string; def, max, want int }{
		{"", 50, 500, 50},
		{"?limit=100", 50, 500, 100},
		{"?limit=600", 50, 500, 500},
		{"?limit=0", 50, 500, 50},
		{"?limit=abc", 50, 500, 50},
	}
	for _, tc := range cases {
		r := httptest.NewRequest("GET", "/"+tc.query, nil)
		if got := parseLimit(r, tc.def, tc.max); got != tc.want {
			t.Errorf("parseLimit(%q,%d,%d) = %d, want %d", tc.query, tc.def, tc.max, got, tc.want)
		}
	}
}

func TestNewPagination(t *testing.T) {
	cases := []struct{ page, pp, total, wantTP int }{
		{1, 20, 0, 1}, {1, 20, 20, 1}, {1, 20, 21, 2}, {1, 20, 101, 6}, {1, 0, 100, 1},
	}
	for _, tc := range cases {
		pag := NewPagination(tc.page, tc.pp, tc.total)
		if pag.TotalPages != tc.wantTP {
			t.Errorf("NewPagination(%d,%d,%d).TotalPages = %d, want %d",
				tc.page, tc.pp, tc.total, pag.TotalPages, tc.wantTP)
		}
	}
}

func TestParseFilterParams_Whitelist(t *testing.T) {
	r := httptest.NewRequest("GET", "/?status=invalid&plan=enterprise", nil)
	f := parseFilterParams(r)
	if f.Status != "" { t.Errorf("invalid status should be empty, got %q", f.Status) }
	if f.Plan != "" { t.Errorf("invalid plan should be empty, got %q", f.Plan) }
}
