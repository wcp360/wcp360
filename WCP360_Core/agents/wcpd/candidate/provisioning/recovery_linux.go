//go:build linux

package provisioning

import (
	"fmt"
	planner "github.com/yyogas/wcp360/candidate/planner"
	"io"
	"os"
	"os/user"
	"path/filepath"
	"strconv"
	"syscall"
)

func trustedRead(path string, mode os.FileMode, gid int) ([]byte, error) {
	f, e := os.OpenFile(path, os.O_RDONLY|syscall.O_NOFOLLOW, 0)
	if e != nil {
		return nil, e
	}
	defer f.Close()
	info, e := f.Stat()
	if e != nil {
		return nil, e
	}
	st, ok := info.Sys().(*syscall.Stat_t)
	if !info.Mode().IsRegular() || !ok || st.Uid != 0 || int(st.Gid) != gid || st.Nlink != 1 || info.Mode().Perm() != mode || info.Size() > 16384 {
		return nil, fmt.Errorf("untrusted managed file: %s", path)
	}
	raw, e := io.ReadAll(io.LimitReader(f, 16385))
	if e != nil || len(raw) > 16384 {
		return nil, fmt.Errorf("managed file read rejected")
	}
	return raw, nil
}
func expectFile(path, content string, mode os.FileMode, gid int) error {
	raw, e := trustedRead(path, mode, gid)
	if e != nil {
		return e
	}
	if string(raw) != content {
		return fmt.Errorf("managed content changed: %s", path)
	}
	return nil
}
func expectDir(path string, uid, gid int, mode os.FileMode) error {
	info, e := os.Lstat(path)
	if e != nil {
		return e
	}
	st, ok := info.Sys().(*syscall.Stat_t)
	if !info.IsDir() || info.Mode()&os.ModeSymlink != 0 || !ok || int(st.Uid) != uid || int(st.Gid) != gid || info.Mode().Perm() != mode {
		return fmt.Errorf("managed directory changed: %s", path)
	}
	return nil
}

// A kernel-held, non-inherited lock fences concurrent brokers. No recorded PID
// is trusted as an instruction to signal another process.
func AcquireBrokerLock() (*os.File, error) {
	if Guard() != nil || os.Geteuid() != 0 {
		return nil, fmt.Errorf("broker lock boundary rejected")
	}
	if e := rootDir("/run/wcp360-provision-candidate", 0755); e != nil {
		return nil, e
	}
	f, e := os.OpenFile("/run/wcp360-provision-candidate/broker.lock", os.O_RDWR|os.O_CREATE|syscall.O_NOFOLLOW, 0600)
	if e != nil {
		return nil, e
	}
	info, e := f.Stat()
	if e != nil {
		f.Close()
		return nil, e
	}
	st, ok := info.Sys().(*syscall.Stat_t)
	if !ok || !info.Mode().IsRegular() || st.Uid != 0 || st.Nlink != 1 || info.Mode().Perm() != 0600 {
		f.Close()
		return nil, fmt.Errorf("broker lock file rejected")
	}
	if e = syscall.Flock(int(f.Fd()), syscall.LOCK_EX|syscall.LOCK_NB); e != nil {
		f.Close()
		return nil, fmt.Errorf("another broker holds the lock")
	}
	return f, nil
}

func (b *Broker) Recover() error {
	if Guard() != nil || os.Geteuid() != 0 {
		return fmt.Errorf("recovery boundary rejected")
	}
	entries, e := os.ReadDir(StateRoot + "/records")
	if e != nil {
		return e
	}
	if len(entries) == 0 {
		return nil
	}
	if os.Getenv("WCP_ISOLATED_TEST_VM") != "1" || os.Getenv("WCP_ALLOW_CANDIDATE_RECOVERY") != "1" || len(entries) > 4 {
		return fmt.Errorf("explicit disposable VM recovery required")
	}
	records := make([]record, 0, len(entries))
	names, jobs := map[string]bool{}, map[string]bool{}
	for _, entry := range entries {
		raw, e := trustedRead(StateRoot+"/records/"+entry.Name(), 0600, 0)
		if e != nil {
			return e
		}
		r, e := decodeRecord(raw)
		if e != nil {
			return e
		}
		if entry.Name() != r.Request.SiteID+".json" || r.State != "ACTIVE" || names[r.Request.Hostname] || jobs[r.Request.JobID] {
			return fmt.Errorf("incomplete/conflicting journal requires inspection")
		}
		names[r.Request.Hostname], jobs[r.Request.JobID] = true, true
		if e = verifySite(r.Request); e != nil {
			return e
		}
		records = append(records, r)
	}
	// Wildcard includes are safe only when every loaded configuration is accounted for.
	for _, sub := range []string{"nginx-sites", "fpm-pools"} {
		files, e := os.ReadDir(StateRoot + "/" + sub)
		if e != nil || len(files) != len(records) {
			return fmt.Errorf("unexpected service configuration inventory")
		}
		expected := map[string]bool{}
		for _, r := range records {
			expected[r.Request.SiteID+".conf"] = true
		}
		for _, f := range files {
			if !expected[f.Name()] {
				return fmt.Errorf("unrecorded service configuration")
			}
		}
	}
	if e = b.ensureServers(); e != nil {
		return e
	}
	for _, r := range records {
		if !b.healthy(r.Request) {
			return fmt.Errorf("recovered site identity check failed")
		}
	}
	return nil
}
func verifySite(r Request) error {
	account, e := user.Lookup(UnixUser(r.SiteID))
	if e != nil {
		return e
	}
	uid, e := strconv.Atoi(account.Uid)
	if e != nil || uid == 0 {
		return fmt.Errorf("invalid site UID")
	}
	gid, e := strconv.Atoi(account.Gid)
	if e != nil || gid == 0 {
		return fmt.Errorf("invalid site GID")
	}
	groups, e := account.GroupIds()
	if e != nil || len(groups) != 1 || groups[0] != account.Gid {
		return fmt.Errorf("site group membership changed")
	}
	nginx, e := user.Lookup("www-data")
	if e != nil {
		return e
	}
	nginxGroups, e := nginx.GroupIds()
	if e != nil {
		return e
	}
	member := false
	for _, g := range nginxGroups {
		if g == account.Gid {
			member = true
		}
	}
	if !member {
		return fmt.Errorf("nginx site-group membership missing")
	}
	base := "/srv/wcp360-candidate/vhosts/" + r.SiteID
	for _, sub := range []string{"", "/public"} {
		if e = expectDir(base+sub, 0, gid, 0750); e != nil {
			return e
		}
	}
	for _, sub := range []string{"/tmp", "/tmp/sessions", "/var"} {
		if e = expectDir(base+sub, uid, gid, 0700); e != nil {
			return e
		}
	}
	for name, content := range siteSources(r) {
		if e = expectFile(base+"/public/"+name, content, 0640, gid); e != nil {
			return e
		}
	}
	plan, e := planner.Render(planner.Request{SiteID: r.SiteID, Hostname: r.Hostname})
	if e != nil {
		return e
	}
	for path, content := range map[string]string{"nginx-sites/": plan.Nginx, "fpm-pools/": plan.PHPFPM} {
		if e = expectFile(filepath.Join(StateRoot, path, r.SiteID+".conf"), content, 0644, 0); e != nil {
			return e
		}
	}
	return nil
}
