package provisioning

import "testing"

func TestVMEvidenceBindsBootDistributionAndRootIntent(t *testing.T) {
	boot := "13e80812-7be4-4c72-90be-602091be2740\n"
	marker := "wcp360.candidate.vm/1\n" + boot
	release := "ID=debian\nVERSION_ID=\"13\"\n"
	if e := validateVMEvidence(marker, boot, VMProduct+"\n", release, 0, 0644); e != nil {
		t.Fatal(e)
	}
	cases := []struct {
		name, marker, boot, product, release string
		uid, mode                            uint32
	}{
		{"prior boot", marker, "13e80812-7be4-4c72-90be-602091be2741\n", VMProduct, release, 0, 0644},
		{"ordinary VM", marker, boot, "Standard PC", release, 0, 0644},
		{"wrong distribution", marker, boot, VMProduct, "ID=ubuntu\nVERSION_ID=\"13\"\n", 0, 0644},
		{"wrong version", marker, boot, VMProduct, "ID=debian\nVERSION_ID=\"12\"\n", 0, 0644},
		{"tenant owned marker", marker, boot, VMProduct, release, 31003, 0644},
		{"group writable marker", marker, boot, VMProduct, release, 0, 0664},
		{"world writable marker", marker, boot, VMProduct, release, 0, 0646},
		{"empty boot", "wcp360.candidate.vm/1\n", "", VMProduct, release, 0, 0644},
		{"trailing input", marker + "extra", boot, VMProduct, release, 0, 0644},
	}
	for _, c := range cases {
		t.Run(c.name, func(t *testing.T) {
			if validateVMEvidence(c.marker, c.boot, c.product, c.release, c.uid, c.mode) == nil {
				t.Fatal("unsafe VM accepted")
			}
		})
	}
}
