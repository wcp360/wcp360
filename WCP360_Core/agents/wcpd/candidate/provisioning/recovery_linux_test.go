//go:build linux

package provisioning

import (
	"os"
	"path/filepath"
	"testing"
)

func TestRecoveryRefusesSymlinksHardlinksAndWritableManagedCode(t *testing.T) {
	if os.Geteuid() != 0 {
		t.Skip("root metadata regression runs inside the disposable Go build container")
	}
	dir := t.TempDir()
	path := filepath.Join(dir, "managed.conf")
	if e := os.WriteFile(path, []byte("expected"), 0644); e != nil {
		t.Fatal(e)
	}
	if e := expectFile(path, "expected", 0644, 0); e != nil {
		t.Fatal(e)
	}
	if e := expectFile(path, "changed", 0644, 0); e == nil {
		t.Fatal("changed content accepted")
	}
	link := filepath.Join(dir, "link")
	if e := os.Symlink(path, link); e != nil {
		t.Fatal(e)
	}
	if _, e := trustedRead(link, 0644, 0); e == nil {
		t.Fatal("symlink accepted")
	}
	hard := filepath.Join(dir, "hard")
	if e := os.Link(path, hard); e != nil {
		t.Fatal(e)
	}
	if _, e := trustedRead(path, 0644, 0); e == nil {
		t.Fatal("multiply-linked file accepted")
	}
	os.Remove(hard)
	os.Chmod(path, 0664)
	if _, e := trustedRead(path, 0644, 0); e == nil {
		t.Fatal("writable managed configuration accepted")
	}
	os.Chmod(path, 0644)
	os.Chown(path, 31003, 0)
	if _, e := trustedRead(path, 0644, 0); e == nil {
		t.Fatal("tenant-owned managed code accepted")
	}
}

func TestOrphanConfigurationCannotBeLoadedWithoutJournal(t *testing.T) {
	dir := t.TempDir()
	if e := verifyConfigInventory(dir, nil); e != nil {
		t.Fatal(e)
	}
	path := filepath.Join(dir, "orphan.conf")
	if e := os.WriteFile(path, []byte("unrecorded"), 0644); e != nil {
		t.Fatal(e)
	}
	if verifyConfigInventory(dir, nil) == nil {
		t.Fatal("empty journal accepted an orphan configuration")
	}
	if verifyConfigInventory(dir, map[string]bool{"different.conf": true}) == nil {
		t.Fatal("same-count mismatched configuration accepted")
	}
	if e := verifyConfigInventory(dir, map[string]bool{"orphan.conf": true}); e != nil {
		t.Fatal(e)
	}
	os.Remove(path)
	if verifyConfigInventory(dir, map[string]bool{"orphan.conf": true}) == nil {
		t.Fatal("missing configuration accepted")
	}
}
