//go:build linux

package provisioning

import (
	"context"
	_ "embed"
	"encoding/json"
	"fmt"
	planner "github.com/yyogas/wcp360/candidate/planner"
	"io"
	"net"
	"net/http"
	"os"
	"os/exec"
	"os/user"
	"path/filepath"
	"strconv"
	"strings"
	"syscall"
	"time"
)

//go:embed fixtures/demo.php
var demoApplication string

func rootDir(path string, mode os.FileMode) error {
	s, e := os.Lstat(path)
	if os.IsNotExist(e) {
		if e = os.Mkdir(path, mode); e != nil {
			return e
		}
		return syncDir(filepath.Dir(path))
	}
	if e != nil {
		return e
	}
	if !s.IsDir() || s.Mode()&os.ModeSymlink != 0 {
		return fmt.Errorf("unexpected managed directory")
	}
	stat, ok := s.Sys().(*syscall.Stat_t)
	if !ok || stat.Uid != 0 || s.Mode().Perm()&0022 != 0 {
		return fmt.Errorf("untrusted managed directory")
	}
	return nil
}

// All parents are root-owned and non-writable by callers. Application writes are confined to tmp/var.
func syncDir(path string) error {
	f, e := os.Open(path)
	if e != nil {
		return e
	}
	defer f.Close()
	return f.Sync()
}
func writeNew(path string, data []byte, mode os.FileMode) error {
	f, e := os.OpenFile(path, os.O_WRONLY|os.O_CREATE|os.O_EXCL, mode)
	if e != nil {
		return e
	}
	if _, e = f.Write(data); e == nil {
		e = f.Sync()
	}
	closeErr := f.Close()
	if e != nil {
		return e
	}
	if closeErr != nil {
		return closeErr
	}
	return syncDir(filepath.Dir(path))
}
func fixedCommand(binary string, args ...string) error {
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()
	cmd := exec.CommandContext(ctx, binary, args...)
	cmd.Env = []string{"PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/bin", "LANG=C"}
	cmd.Stdout = io.Discard
	cmd.Stderr = io.Discard
	return cmd.Run()
}
func (b *Broker) Prepare() error {
	if e := Guard(); e != nil {
		return e
	}
	if os.Geteuid() != 0 {
		return fmt.Errorf("broker requires root")
	}
	b.failures = make(chan error, 1)
	for _, path := range []string{StateRoot, StateRoot + "/records", StateRoot + "/nginx-sites", StateRoot + "/fpm-pools"} {
		if e := rootDir(path, 0755); e != nil {
			return e
		}
	}
	if e := os.Chmod(StateRoot+"/records", 0700); e != nil {
		return e
	}
	for _, path := range []string{"/srv/wcp360-candidate", "/srv/wcp360-candidate/vhosts", "/run/wcp360-candidate"} {
		if e := rootDir(path, 0755); e != nil {
			return e
		}
	}
	www, e := user.Lookup("www-data")
	if e != nil {
		return e
	}
	gid, e := strconv.Atoi(www.Gid)
	if e != nil {
		return e
	}
	if e = os.Chown("/run/wcp360-candidate", 0, gid); e != nil {
		return e
	}
	if e = os.Chmod("/run/wcp360-candidate", 0750); e != nil {
		return e
	}
	nginx := "user www-data;\nworker_processes 1;\npid " + StateRoot + "/nginx.pid;\nerror_log " + StateRoot + "/nginx-error.log;\nevents { worker_connections 128; }\nhttp { access_log off; server { listen 8080 default_server; return 444; } include " + StateRoot + "/nginx-sites/*.conf; }\n"
	fpm := "[global]\nerror_log = " + StateRoot + "/fpm-error.log\ndaemonize = no\ninclude = " + StateRoot + "/fpm-pools/*.conf\n"
	for p, c := range map[string]string{StateRoot + "/nginx.conf": nginx, StateRoot + "/fpm.conf": fpm} {
		if _, e = os.Lstat(p); os.IsNotExist(e) {
			if e = writeNew(p, []byte(c), 0644); e != nil {
				return e
			}
		} else if e != nil {
			return e
		} else {
			old, e := trustedRead(p, 0644, 0)
			if e != nil || string(old) != c {
				return fmt.Errorf("managed supervisor configuration changed")
			}
		}
	}
	return nil
}
func (b *Broker) Handle(r Request) Response {
	if Guard() != nil || os.Geteuid() != 0 {
		return Reply(r, "REJECTED", false)
	}
	b.mu.Lock()
	defer b.mu.Unlock()
	if r.Validate() != nil {
		return Reply(r, "REJECTED", false)
	}
	path := StateRoot + "/records/" + r.SiteID + ".json"
	raw, e := trustedRead(path, 0600, 0)
	if e == nil {
		old, decodeErr := decodeRecord(raw)
		if decodeErr != nil || old.Fingerprint != r.Fingerprint() {
			return Reply(r, "REJECTED", false)
		}
		if old.State == "ACTIVE" && b.healthy(r) {
			return Reply(r, "ACTIVE", true)
		}
		return Reply(r, "UNKNOWN_OUTCOME", true)
	}
	if !os.IsNotExist(e) {
		return Reply(r, "UNKNOWN_OUTCOME", false)
	}
	if r.Operation == "inspect_php_fpm" {
		return Reply(r, "REJECTED", false)
	}
	records, e := os.ReadDir(StateRoot + "/records")
	if e != nil || len(records) >= 4 {
		return Reply(r, "REJECTED", false)
	}
	for _, entry := range records {
		data, e := trustedRead(StateRoot+"/records/"+entry.Name(), 0600, 0)
		old, decodeErr := decodeRecord(data)
		if e != nil || decodeErr != nil {
			return Reply(r, "REJECTED", false)
		}
		if old.Request.JobID == r.JobID || old.Request.Hostname == r.Hostname {
			return Reply(r, "REJECTED", false)
		}
	}
	saved := record{r, r.Fingerprint(), "PREPARING"}
	data, _ := json.Marshal(saved)
	if writeNew(path, data, 0600) != nil {
		return Reply(r, "UNKNOWN_OUTCOME", false)
	}
	if e = b.apply(r); e != nil {
		return Reply(r, "UNKNOWN_OUTCOME", false)
	}
	if !b.healthy(r) {
		return Reply(r, "UNKNOWN_OUTCOME", false)
	}
	saved.State = "ACTIVE"
	data, _ = json.Marshal(saved)
	temp := path + ".active"
	if writeNew(temp, data, 0600) != nil || os.Rename(temp, path) != nil || syncDir(StateRoot+"/records") != nil {
		return Reply(r, "UNKNOWN_OUTCOME", false)
	}
	return Reply(r, "ACTIVE", false)
}
func siteSources(r Request) map[string]string {
	probe := "<?php declare(strict_types=1); header('Content-Type: application/json'); preg_match('/^Uid:\\s+([0-9]+)\\s+([0-9]+)/m',(string)file_get_contents('/proc/self/status'),$uid); echo json_encode(['site_id'=>'" + r.SiteID + "','tenant_id'=>'" + r.TenantID + "','euid'=>(int)($uid[2]??0)],JSON_THROW_ON_ERROR);"
	app := strings.NewReplacer("{{SITE_ID}}", r.SiteID, "{{TENANT_ID}}", r.TenantID).Replace(demoApplication)
	return map[string]string{"index.php": app, "__wcp_probe.php": probe}
}

func (b *Broker) apply(r Request) error {
	name := UnixUser(r.SiteID)
	if _, e := user.Lookup(name); e == nil {
		return fmt.Errorf("Unix identity already exists")
	}
	if e := fixedCommand("/usr/sbin/useradd", "--system", "--user-group", "--no-create-home", "--shell", "/usr/sbin/nologin", name); e != nil {
		return e
	}
	u, e := user.Lookup(name)
	if e != nil {
		return e
	}
	uid, e := strconv.Atoi(u.Uid)
	if e != nil {
		return e
	}
	gid, e := strconv.Atoi(u.Gid)
	if e != nil {
		return e
	}
	base := "/srv/wcp360-candidate/vhosts/" + r.SiteID
	// Refuse reuse, including pre-existing links, before writing any application file.
	if e = os.Mkdir(base, 0750); e != nil {
		return e
	}
	if e = os.Chown(base, 0, gid); e != nil {
		return e
	}
	for _, sub := range []string{"public", "tmp", "tmp/sessions", "var"} {
		path := base + "/" + sub
		if e = os.Mkdir(path, 0750); e != nil {
			return e
		}
		if sub == "public" {
			e = os.Chown(path, 0, gid)
		} else {
			e = os.Chown(path, uid, gid)
			if e == nil {
				e = os.Chmod(path, 0700)
			}
		}
		if e != nil {
			return e
		}
	}
	for file, content := range siteSources(r) {
		if e = writeNew(base+"/public/"+file, []byte(content), 0640); e != nil {
			return e
		}
		if e = os.Chown(base+"/public/"+file, 0, gid); e != nil {
			return e
		}
	}
	if e = fixedCommand("/usr/sbin/usermod", "-aG", name, "www-data"); e != nil {
		return e
	}
	plan, e := planner.Render(planner.Request{SiteID: r.SiteID, Hostname: r.Hostname})
	if e != nil {
		return e
	}
	if e = writeNew(StateRoot+"/nginx-sites/"+r.SiteID+".conf", []byte(plan.Nginx), 0644); e != nil {
		return e
	}
	if e = writeNew(StateRoot+"/fpm-pools/"+r.SiteID+".conf", []byte(plan.PHPFPM), 0644); e != nil {
		return e
	}
	return b.ensureServers()
}

func (b *Broker) ensureServers() error {
	if e := fixedCommand("/usr/sbin/nginx", "-t", "-c", StateRoot+"/nginx.conf"); e != nil {
		return e
	}
	if e := fixedCommand(fpmBinary(), "-t", "-y", StateRoot+"/fpm.conf"); e != nil {
		return e
	}
	if !b.fpmStarted {
		if e := b.start(fpmBinary(), "-F", "-y", StateRoot+"/fpm.conf"); e != nil {
			return e
		}
		b.fpmStarted = true
	} else {
		if e := b.children[0].Signal(syscall.SIGUSR2); e != nil {
			return e
		}
	}
	if !b.nginxStarted {
		if e := b.start("/usr/sbin/nginx", "-c", StateRoot+"/nginx.conf", "-g", "daemon off;"); e != nil {
			return e
		}
		b.nginxStarted = true
	} else {
		if e := b.children[1].Signal(syscall.SIGHUP); e != nil {
			return e
		}
	}
	return nil
}
func (b *Broker) start(binary string, args ...string) error {
	cmd := exec.Command(binary, args...)
	cmd.Env = []string{"PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/bin", "LANG=C"}
	cmd.Stdout = io.Discard
	cmd.Stderr = io.Discard
	if e := cmd.Start(); e != nil {
		return e
	}
	b.children = append(b.children, cmd.Process)
	go func() {
		e := cmd.Wait()
		if !b.stopping.Load() {
			select {
			case b.failures <- fmt.Errorf("managed child exited (%s): %v", filepath.Base(binary), e):
			default:
			}
		}
	}()
	return nil
}
func (b *Broker) healthy(r Request) bool {
	u, e := user.Lookup(UnixUser(r.SiteID))
	if e != nil {
		return false
	}
	expectedUID, e := strconv.Atoi(u.Uid)
	if e != nil || expectedUID == 0 {
		return false
	}
	client := http.Client{Timeout: time.Second, CheckRedirect: func(*http.Request, []*http.Request) error { return http.ErrUseLastResponse }, Transport: &http.Transport{Proxy: nil, DialContext: (&net.Dialer{Timeout: time.Second}).DialContext, DisableKeepAlives: true}}
	defer client.CloseIdleConnections()
	for i := 0; i < 30; i++ {
		req, _ := http.NewRequest("GET", "http://127.0.0.1:8080/__wcp_probe.php", nil)
		req.Host = r.Hostname
		response, e := client.Do(req)
		if e == nil {
			raw, readErr := io.ReadAll(io.LimitReader(response.Body, 1025))
			response.Body.Close()
			var body struct {
				SiteID   string `json:"site_id"`
				TenantID string `json:"tenant_id"`
				EUID     int    `json:"euid"`
			}
			if response.StatusCode == 200 && readErr == nil && len(raw) <= 1024 && json.Unmarshal(raw, &body) == nil && body.SiteID == r.SiteID && body.TenantID == r.TenantID && body.EUID == expectedUID {
				return true
			}
		}
		time.Sleep(100 * time.Millisecond)
	}
	return false
}
