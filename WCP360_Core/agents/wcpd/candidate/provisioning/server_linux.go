//go:build linux

package provisioning

import (
	"context"
	"encoding/json"
	"fmt"
	"io"
	"net"
	"os"
	"sync"
	"sync/atomic"
	"syscall"
	"time"
)

func Guard() error {
	container, vm := os.Getenv("WCP_ISOLATED_TEST_CONTAINER"), os.Getenv("WCP_ISOLATED_TEST_VM")
	if container == "1" && vm == "" {
		info, e := os.Lstat("/.dockerenv")
		if e == nil && info.Mode().IsRegular() {
			return nil
		}
		return fmt.Errorf("container marker required")
	}
	if vm != "1" || container != "" {
		return fmt.Errorf("exactly one disposable execution profile required")
	}
	if _, e := os.Lstat("/.dockerenv"); !os.IsNotExist(e) {
		return fmt.Errorf("VM profile refuses containers")
	}
	info, e := os.Lstat(VMMarker)
	if e != nil || !info.Mode().IsRegular() || info.Size() > 128 {
		return fmt.Errorf("VM marker required")
	}
	stat, ok := info.Sys().(*syscall.Stat_t)
	if !ok {
		return fmt.Errorf("VM marker ownership unavailable")
	}
	marker, e := os.ReadFile(VMMarker)
	if e != nil {
		return e
	}
	bootID, e := os.ReadFile("/proc/sys/kernel/random/boot_id")
	if e != nil {
		return e
	}
	product, e := os.ReadFile("/sys/class/dmi/id/product_name")
	if e != nil {
		return e
	}
	release, e := os.ReadFile("/etc/os-release")
	if e != nil {
		return e
	}
	return validateVMEvidence(string(marker), string(bootID), string(product), string(release), stat.Uid, uint32(info.Mode().Perm()))
}

func fpmBinary() string {
	if os.Getenv("WCP_ISOLATED_TEST_VM") == "1" {
		return "/usr/sbin/php-fpm8.4"
	}
	return "/usr/local/sbin/php-fpm"
}

func peer(conn *net.UnixConn) (uint32, error) {
	raw, e := conn.SyscallConn()
	if e != nil {
		return 0, e
	}
	var uid uint32
	var inner error
	e = raw.Control(func(fd uintptr) {
		var cred *syscall.Ucred
		cred, inner = syscall.GetsockoptUcred(int(fd), syscall.SOL_SOCKET, syscall.SO_PEERCRED)
		if inner == nil {
			uid = cred.Uid
		}
	})
	if e != nil {
		return 0, e
	}
	return uid, inner
}
func Serve(ctx context.Context, path string, peerUID uint32, owner, group int, handler func(Request) Response) error {
	if e := Guard(); e != nil {
		return e
	}
	if s, e := os.Lstat(path); e == nil {
		if s.Mode()&os.ModeSocket == 0 {
			return fmt.Errorf("unexpected socket path")
		}
		if e = os.Remove(path); e != nil {
			return e
		}
	} else if !os.IsNotExist(e) {
		return e
	}
	listener, e := net.ListenUnix("unix", &net.UnixAddr{Name: path, Net: "unix"})
	if e != nil {
		return e
	}
	defer listener.Close()
	if e = os.Chmod(path, 0660); e != nil {
		return e
	}
	if e = os.Chown(path, owner, group); e != nil {
		return e
	}
	if e = notifyReady(); e != nil {
		return e
	}
	go func() { <-ctx.Done(); listener.Close() }()
	// Serialize requests and cap work. No unbounded goroutine per attacker connection.
	for {
		conn, e := listener.AcceptUnix()
		if e != nil {
			if ctx.Err() != nil {
				return nil
			}
			return e
		}
		conn.SetDeadline(time.Now().Add(20 * time.Second))
		uid, e := peer(conn)
		if e != nil || uid != peerUID {
			json.NewEncoder(conn).Encode(Reply(Request{}, "REJECTED", false))
			conn.Close()
			continue
		}
		// One bounded frame per connection. The client half-closes its write side.
		response := dispatchFrame(conn, handler)
		json.NewEncoder(conn).Encode(response)
		conn.Close()
	}
}
func Forward(r Request) Response {
	conn, e := net.DialTimeout("unix", BrokerSocket, 3*time.Second)
	if e != nil {
		return Reply(r, "UNKNOWN_OUTCOME", false)
	}
	defer conn.Close()
	conn.SetDeadline(time.Now().Add(18 * time.Second))
	u := conn.(*net.UnixConn)
	uid, e := peer(u)
	if e != nil || uid != 0 {
		return Reply(r, "UNKNOWN_OUTCOME", false)
	}
	if json.NewEncoder(conn).Encode(r) != nil {
		return Reply(r, "UNKNOWN_OUTCOME", false)
	}
	u.CloseWrite()
	var reply Response
	d := json.NewDecoder(io.LimitReader(conn, 4097))
	d.DisallowUnknownFields()
	if d.Decode(&reply) != nil || d.Decode(new(any)) != io.EOF || reply.Schema != Schema || reply.JobID != r.JobID || reply.SiteID != r.SiteID || (reply.Status != "ACTIVE" && reply.Status != "REJECTED" && reply.Status != "UNKNOWN_OUTCOME") {
		return Reply(r, "UNKNOWN_OUTCOME", false)
	}
	return reply
}

type Broker struct {
	failures     chan error
	stopping     atomic.Bool
	mu           sync.Mutex
	children     []*os.Process
	nginxStarted bool
	fpmStarted   bool
}

func (b *Broker) Close() {
	b.stopping.Store(true)
	for _, p := range b.children {
		p.Signal(syscall.SIGQUIT)
	}
}

func PrepareSocketDirs(agentUID, workerUID int) error {
	if e := rootDir("/run/wcp360-provision-candidate", 0755); e != nil {
		return e
	}
	path := "/run/wcp360-provision-candidate/public"
	if e := os.Mkdir(path, 0750); e == nil {
		if e = os.Chown(path, agentUID, workerUID); e != nil {
			return e
		}
		return syncDir("/run/wcp360-provision-candidate")
	} else if !os.IsExist(e) {
		return e
	}
	s, e := os.Lstat(path)
	if e != nil {
		return e
	}
	st, ok := s.Sys().(*syscall.Stat_t)
	if !s.IsDir() || s.Mode()&os.ModeSymlink != 0 || !ok || int(st.Uid) != agentUID || int(st.Gid) != workerUID || s.Mode().Perm() != 0750 {
		return fmt.Errorf("unexpected agent socket directory")
	}
	return nil
}

// systemd readiness is emitted only after recovery and socket ownership succeed.
func notifyReady() error {
	path := os.Getenv("NOTIFY_SOCKET")
	if path == "" {
		return nil
	}
	conn, e := net.DialUnix("unixgram", nil, &net.UnixAddr{Name: path, Net: "unixgram"})
	if e != nil {
		return e
	}
	defer conn.Close()
	conn.SetWriteDeadline(time.Now().Add(time.Second))
	_, e = conn.Write([]byte("READY=1"))
	return e
}

func (b *Broker) Failures() <-chan error { return b.failures }
