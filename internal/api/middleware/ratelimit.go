// ======================================================================
// WCP 360 | V0.1.0 | internal/api/middleware/ratelimit.go
// Description: IP sliding-window rate limiter, stdlib only.
// ======================================================================

package middleware

import (
	"context"
	"encoding/json"
	"net/http"
	"strconv"
	"strings"
	"sync"
	"time"
)

type entry struct {
	mu        sync.Mutex
	count     int
	windowEnd time.Time
}

type RateLimiter struct {
	maxRequests     int
	windowSize      time.Duration
	cleanupInterval time.Duration
	entries         sync.Map
}

func NewRateLimiter(ctx context.Context, maxRequests int, windowSize, cleanupInterval time.Duration) *RateLimiter {
	rl := &RateLimiter{maxRequests: maxRequests, windowSize: windowSize, cleanupInterval: cleanupInterval}
	go rl.cleanup(ctx)
	return rl
}

func (rl *RateLimiter) Limit(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		ip := extractIP(r)
		val, _ := rl.entries.LoadOrStore(ip, &entry{})
		e := val.(*entry)
		e.mu.Lock()
		now := time.Now()
		if now.After(e.windowEnd) {
			e.count = 0
			e.windowEnd = now.Add(rl.windowSize)
		}
		e.count++
		count := e.count
		retryAfter := int(e.windowEnd.Sub(now).Seconds()) + 1
		e.mu.Unlock()
		remaining := rl.maxRequests - count
		if remaining < 0 { remaining = 0 }
		w.Header().Set("X-RateLimit-Limit", strconv.Itoa(rl.maxRequests))
		w.Header().Set("X-RateLimit-Remaining", strconv.Itoa(remaining))
		if count > rl.maxRequests {
			w.Header().Set("Retry-After", strconv.Itoa(retryAfter))
			w.Header().Set("Content-Type", "application/json")
			w.WriteHeader(http.StatusTooManyRequests)
			json.NewEncoder(w).Encode(map[string]any{
				"error": "too many requests — please wait before retrying",
				"retry_after": retryAfter,
			})
			return
		}
		next.ServeHTTP(w, r)
	})
}

func (rl *RateLimiter) cleanup(ctx context.Context) {
	ticker := time.NewTicker(rl.cleanupInterval)
	defer ticker.Stop()
	for {
		select {
		case <-ctx.Done(): return
		case <-ticker.C:
			now := time.Now()
			rl.entries.Range(func(key, val any) bool {
				e := val.(*entry)
				e.mu.Lock(); stale := now.After(e.windowEnd); e.mu.Unlock()
				if stale { rl.entries.Delete(key) }
				return true
			})
		}
	}
}

func extractIP(r *http.Request) string {
	if ip := r.Header.Get("X-Real-IP"); ip != "" { return strings.TrimSpace(ip) }
	if fwd := r.Header.Get("X-Forwarded-For"); fwd != "" {
		return strings.TrimSpace(strings.SplitN(fwd, ",", 2)[0])
	}
	addr := r.RemoteAddr
	if i := strings.LastIndex(addr, ":"); i > 0 { return addr[:i] }
	return addr
}
