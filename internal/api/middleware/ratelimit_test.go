// ======================================================================
// WCP 360 | V0.1.0 | internal/api/middleware/ratelimit_test.go
// ======================================================================

package middleware

import (
	"context"
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"strconv"
	"testing"
	"time"
)

func okHandler(w http.ResponseWriter, r *http.Request) { w.WriteHeader(http.StatusOK) }
func newRL(max int) *RateLimiter {
	return NewRateLimiter(context.Background(), max, time.Minute, time.Hour)
}

func TestRateLimiter_AllowsUnderLimit(t *testing.T) {
	rl := newRL(5); h := rl.Limit(http.HandlerFunc(okHandler))
	for i := 0; i < 5; i++ {
		req := httptest.NewRequest("POST", "/", nil); req.RemoteAddr = "10.0.0.1:1"
		rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
		if rr.Code != 200 { t.Errorf("req %d: expected 200, got %d", i+1, rr.Code) }
	}
}

func TestRateLimiter_BlocksOverLimit(t *testing.T) {
	rl := newRL(3); h := rl.Limit(http.HandlerFunc(okHandler))
	for i := 0; i < 3; i++ {
		req := httptest.NewRequest("POST", "/", nil); req.RemoteAddr = "10.0.0.2:1"
		rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	}
	req := httptest.NewRequest("POST", "/", nil); req.RemoteAddr = "10.0.0.2:1"
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 429 { t.Errorf("expected 429, got %d", rr.Code) }
	ra := rr.Header().Get("Retry-After")
	if n, err := strconv.Atoi(ra); err != nil || n <= 0 {
		t.Errorf("Retry-After %q must be positive int", ra)
	}
}

func TestRateLimiter_RetryAfterLargeWindow(t *testing.T) {
	rl := NewRateLimiter(context.Background(), 1, 2*time.Minute, time.Hour)
	h := rl.Limit(http.HandlerFunc(okHandler))
	for i := 0; i < 2; i++ {
		req := httptest.NewRequest("POST", "/", nil); req.RemoteAddr = "10.0.0.9:0"
		rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
		if i == 1 {
			n, _ := strconv.Atoi(rr.Header().Get("Retry-After"))
			if n <= 9 { t.Errorf("Retry-After %d too small for 2min window", n) }
		}
	}
}

func TestRateLimiter_DifferentIPs(t *testing.T) {
	rl := newRL(2); h := rl.Limit(http.HandlerFunc(okHandler))
	for i := 0; i < 2; i++ {
		req := httptest.NewRequest("POST", "/", nil); req.RemoteAddr = "1.1.1.1:0"
		rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	}
	req := httptest.NewRequest("POST", "/", nil); req.RemoteAddr = "2.2.2.2:0"
	rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
	if rr.Code != 200 { t.Errorf("IP-B should not be rate-limited: %d", rr.Code) }
}

func TestRateLimiter_XRealIP(t *testing.T) {
	rl := newRL(1); h := rl.Limit(http.HandlerFunc(okHandler))
	for i := 0; i < 2; i++ {
		req := httptest.NewRequest("POST", "/", nil)
		req.RemoteAddr = "127.0.0.1:0"; req.Header.Set("X-Real-IP", "3.3.3.3")
		rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
		if i == 1 && rr.Code != 429 { t.Errorf("2nd req: expected 429, got %d", rr.Code) }
	}
}

func TestRateLimiter_JSONBody(t *testing.T) {
	rl := newRL(1); h := rl.Limit(http.HandlerFunc(okHandler))
	for i := 0; i < 2; i++ {
		req := httptest.NewRequest("POST", "/", nil); req.RemoteAddr = "7.7.7.7:0"
		rr := httptest.NewRecorder(); h.ServeHTTP(rr, req)
		if i == 1 {
			var body map[string]any
			if err := json.NewDecoder(rr.Body).Decode(&body); err != nil { t.Fatal(err) }
			if _, ok := body["error"]; !ok { t.Error("expected 'error' field") }
		}
	}
}

func TestRateLimiter_CtxCancel(t *testing.T) {
	ctx, cancel := context.WithCancel(context.Background())
	rl := NewRateLimiter(ctx, 100, time.Minute, 10*time.Millisecond)
	h := rl.Limit(http.HandlerFunc(okHandler))
	req := httptest.NewRequest("GET", "/", nil); req.RemoteAddr = "5.5.5.5:0"
	h.ServeHTTP(httptest.NewRecorder(), req)
	cancel()
	time.Sleep(50 * time.Millisecond)
}

func TestExtractIP(t *testing.T) {
	cases := []struct{ remote, real, fwd, want string }{
		{"1.2.3.4:5678", "", "", "1.2.3.4"},
		{"1.2.3.4:5678", "5.6.7.8", "", "5.6.7.8"},
		{"1.2.3.4:5678", "", "9.10.11.12, 13.14.15.16", "9.10.11.12"},
		{"[::1]:8080", "", "", "::1"},
	}
	for _, tc := range cases {
		r := httptest.NewRequest("GET", "/", nil)
		r.RemoteAddr = tc.remote
		if tc.real != "" { r.Header.Set("X-Real-IP", tc.real) }
		if tc.fwd != "" { r.Header.Set("X-Forwarded-For", tc.fwd) }
		if got := extractIP(r); got != tc.want {
			t.Errorf("extractIP(%q,%q,%q) = %q, want %q", tc.remote, tc.real, tc.fwd, got, tc.want)
		}
	}
}
