// ======================================================================
// WCP 360 | V0.1.0 | internal/cache/redis_test.go
// ======================================================================

package cache

import (
	"context"
	"testing"
	"time"
)

func TestClient_NilSafe(t *testing.T) {
	var c *Client
	if err := c.Ping(context.Background()); err != nil { t.Error(err) }
	if err := c.Set(context.Background(), "k", "v", time.Minute); err != nil { t.Error(err) }
	v, ok, err := c.Get(context.Background(), "k")
	if err != nil || ok || v != "" { t.Errorf("Get nil: (%q,%v,%v)", v, ok, err) }
	if err := c.Del(context.Background(), "k"); err != nil { t.Error(err) }
	if err := c.Close(); err != nil { t.Error(err) }
}

func TestClient_EmptyAddr_Degrades(t *testing.T) {
	c := &Client{addr: ""}
	if err := c.Set(context.Background(), "k", "v", 0); err != nil { t.Error(err) }
	v, ok, err := c.Get(context.Background(), "k")
	if err != nil || ok || v != "" { t.Error("expected miss") }
}

func TestClient_UnreachableAddr_Degrades(t *testing.T) {
	c := New("127.0.0.1:1", "", 0)
	ctx := context.Background()
	if err := c.Set(ctx, "k", "v", time.Second); err != nil { t.Error(err) }
	v, ok, _ := c.Get(ctx, "k")
	if ok { t.Errorf("expected miss, got %q", v) }
}
