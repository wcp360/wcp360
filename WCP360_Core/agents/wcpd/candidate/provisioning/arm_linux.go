//go:build linux

package provisioning

import (
	"fmt"
	"os"
)

// ArmVM only reissues this boot's /run marker after a separate root-only,
// machine-bound opt-in. It never enables an ordinary host or arbitrary VM.
func ArmVM() error {
	if os.Geteuid() != 0 || os.Getenv("WCP_ISOLATED_TEST_VM") != "1" || os.Getenv("WCP_ISOLATED_TEST_CONTAINER") != "" {
		return fmt.Errorf("disposable VM arm boundary rejected")
	}
	if _, e := os.Lstat("/.dockerenv"); !os.IsNotExist(e) {
		return fmt.Errorf("container refused")
	}
	if e := expectDir("/etc/wcp360-candidate", 0, 0, 0700); e != nil {
		return e
	}
	policy, e := trustedRead(RecoveryPolicy, 0600, 0)
	if e != nil {
		return e
	}
	machine, e := os.ReadFile("/etc/machine-id")
	if e != nil {
		return e
	}
	if e = validateBootPolicy(string(policy), string(machine)); e != nil {
		return e
	}
	boot, e := os.ReadFile("/proc/sys/kernel/random/boot_id")
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
	marker := "wcp360.candidate.vm/1\n" + string(boot)
	if e = validateVMEvidence(marker, string(boot), string(product), string(release), 0, 0644); e != nil {
		return e
	}
	if e = rootDir("/run", 0755); e != nil {
		return e
	}
	if _, e = os.Lstat(VMMarker); os.IsNotExist(e) {
		return writeNew(VMMarker, []byte(marker), 0644)
	} else if e != nil {
		return e
	}
	return expectFile(VMMarker, marker, 0644, 0)
}
