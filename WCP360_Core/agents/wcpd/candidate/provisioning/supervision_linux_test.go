//go:build linux

package provisioning

import (
	"testing"
	"time"
)

func TestSupervisorObservesMasterExitWhileDescendantKeepsOutputOpen(t *testing.T) {
	b := &Broker{failures: make(chan error, 1)}
	defer b.Close()
	// Fixed test-only shell fixture: the bounded descendant inherits both outputs.
	// With Go output-copy pipes, Wait incorrectly waits for that descendant.
	if e := b.start("/bin/sh", "-c", "sleep 4 & exit 0"); e != nil {
		t.Fatal(e)
	}
	select {
	case e := <-b.Failures():
		if e == nil {
			t.Fatal("master exit was not reported")
		}
	case <-time.After(2 * time.Second):
		t.Fatal("master exit hidden by descendant-held output streams")
	}
}
