// ======================================================================
// WCP 360 | V0.1.0 | internal/cache/redis.go
// Description: Stdlib-only RESP Redis client with graceful degradation.
// ======================================================================

package cache

import (
	"bufio"
	"context"
	"fmt"
	"log/slog"
	"net"
	"strconv"
	"strings"
	"sync"
	"time"
)

type Client struct {
	addr, password string
	db             int
	mu             sync.Mutex
	conn           net.Conn
	rdr            *bufio.Reader
}

func New(addr, password string, db int) *Client {
	c := &Client{addr: addr, password: password, db: db}
	if err := c.connect(); err != nil {
		slog.Warn("cache: Redis unavailable — running without cache", "addr", addr, "err", err)
	} else {
		slog.Info("cache: Redis connected", "addr", addr)
	}
	return c
}

func (c *Client) connect() error {
	conn, err := net.DialTimeout("tcp", c.addr, 3*time.Second)
	if err != nil { return fmt.Errorf("cache: dial %s: %w", c.addr, err) }
	rdr := bufio.NewReader(conn)
	if c.password != "" {
		if err := sendCmd(conn, rdr, "AUTH", c.password); err != nil { conn.Close(); return err }
	}
	if c.db != 0 {
		if err := sendCmd(conn, rdr, "SELECT", strconv.Itoa(c.db)); err != nil { conn.Close(); return err }
	}
	c.conn = conn; c.rdr = rdr
	return nil
}

func (c *Client) ensureConn() (net.Conn, *bufio.Reader, error) {
	if c.conn != nil { return c.conn, c.rdr, nil }
	if err := c.connect(); err != nil { return nil, nil, err }
	return c.conn, c.rdr, nil
}

func (c *Client) Ping(ctx context.Context) error {
	if c == nil { return nil }
	c.mu.Lock(); defer c.mu.Unlock()
	conn, rdr, err := c.ensureConn()
	if err != nil { return err }
	return sendCmd(conn, rdr, "PING")
}

func (c *Client) Set(ctx context.Context, key, value string, ttl time.Duration) error {
	if c == nil || c.addr == "" { return nil }
	c.mu.Lock(); defer c.mu.Unlock()
	conn, rdr, err := c.ensureConn()
	if err != nil { slog.Debug("cache.Set: no conn", "key", key); return nil }
	if ttl > 0 {
		return sendCmd(conn, rdr, "SET", key, value, "PX", strconv.FormatInt(ttl.Milliseconds(), 10))
	}
	return sendCmd(conn, rdr, "SET", key, value)
}

func (c *Client) Get(ctx context.Context, key string) (string, bool, error) {
	if c == nil || c.addr == "" { return "", false, nil }
	c.mu.Lock(); defer c.mu.Unlock()
	conn, rdr, err := c.ensureConn()
	if err != nil { return "", false, nil }
	val, err := sendCmdReply(conn, rdr, "GET", key)
	if err != nil {
		if strings.Contains(err.Error(), "nil") { return "", false, nil }
		c.conn.Close(); c.conn = nil
		return "", false, nil
	}
	return val, true, nil
}

func (c *Client) Del(ctx context.Context, key string) error {
	if c == nil || c.addr == "" { return nil }
	c.mu.Lock(); defer c.mu.Unlock()
	conn, rdr, err := c.ensureConn()
	if err != nil { return nil }
	return sendCmd(conn, rdr, "DEL", key)
}

func (c *Client) Close() error {
	if c == nil || c.conn == nil { return nil }
	c.mu.Lock(); defer c.mu.Unlock()
	return c.conn.Close()
}

func sendCmd(conn net.Conn, rdr *bufio.Reader, args ...string) error {
	_, err := sendCmdReply(conn, rdr, args...)
	return err
}

func sendCmdReply(conn net.Conn, rdr *bufio.Reader, args ...string) (string, error) {
	var sb strings.Builder
	fmt.Fprintf(&sb, "*%d\r\n", len(args))
	for _, a := range args { fmt.Fprintf(&sb, "$%d\r\n%s\r\n", len(a), a) }
	conn.SetWriteDeadline(time.Now().Add(2 * time.Second))
	if _, err := conn.Write([]byte(sb.String())); err != nil { return "", fmt.Errorf("cache: write: %w", err) }
	conn.SetReadDeadline(time.Now().Add(2 * time.Second))
	line, err := rdr.ReadString('\n')
	if err != nil { return "", fmt.Errorf("cache: read: %w", err) }
	line = strings.TrimRight(line, "\r\n")
	switch line[0] {
	case '+', ':': return line[1:], nil
	case '-': return "", fmt.Errorf("cache: redis error: %s", line[1:])
	case '$':
		if line == "$-1" { return "", fmt.Errorf("nil") }
		var n int; fmt.Sscanf(line[1:], "%d", &n)
		buf := make([]byte, n+2)
		if _, err := rdr.Read(buf); err != nil { return "", err }
		return string(buf[:n]), nil
	}
	return "", fmt.Errorf("cache: unexpected reply: %q", line)
}
