package provisioning

import (
	"fmt"
	"regexp"
	"strings"
)

const VMProduct = "WCP360 disposable candidate VM"
const VMMarker = "/run/wcp360-candidate-vm"

var bootIDPattern = regexp.MustCompile(`^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\n$`)

// This is an accident-prevention gate for an explicitly created disposable VM,
// not attestation against an administrator who already controls root/hypervisor.
func validateVMEvidence(marker, bootID, product, release string, uid uint32, mode uint32) error {
	if uid != 0 || mode&0022 != 0 || !bootIDPattern.MatchString(bootID) || marker != "wcp360.candidate.vm/1\n"+bootID || strings.TrimSpace(product) != VMProduct || !strings.Contains("\n"+release, "\nID=debian\n") || !strings.Contains("\n"+release, "\nVERSION_ID=\"13\"\n") {
		return fmt.Errorf("disposable Debian 13 VM evidence rejected")
	}
	return nil
}
