package cli

import (
	"bytes"
	"encoding/json"
	"errors"
	"strings"
	"testing"
)

func TestHelpAndVersion(t *testing.T) {
	for _, tc := range []struct {
		name string
		args []string
		want string
	}{
		{"default", nil, "Usage: wcpd"},
		{"help", []string{"--help"}, "Usage: wcpd"},
		{"short help", []string{"-h"}, "Usage: wcpd"},
		{"version", []string{"--version"}, "CODE-BASELINE NONE"},
	} {
		t.Run(tc.name, func(t *testing.T) {
			var out, diagnostic bytes.Buffer
			if code := Run(tc.args, &out, &diagnostic); code != 0 {
				t.Fatalf("exit = %d, want 0", code)
			}
			if !strings.Contains(out.String(), tc.want) || diagnostic.Len() != 0 {
				t.Fatalf("unexpected output: stdout=%q stderr=%q", out.String(), diagnostic.String())
			}
		})
	}
}

func TestCheckReportsInertState(t *testing.T) {
	var out, diagnostic bytes.Buffer
	if code := Run([]string{"--check"}, &out, &diagnostic); code != 0 {
		t.Fatalf("exit = %d", code)
	}
	var result map[string]string
	if err := json.Unmarshal(out.Bytes(), &result); err != nil {
		t.Fatal(err)
	}
	want := map[string]string{
		"component": "wcpd", "mode": "inert-foundation",
		"control_transport": "disabled", "privileged_operations": "disabled",
	}
	if len(result) != len(want) {
		t.Fatalf("unexpected status fields: %v", result)
	}
	for key, value := range want {
		if result[key] != value {
			t.Errorf("%s = %q, want %q", key, result[key], value)
		}
	}
	if diagnostic.Len() != 0 {
		t.Fatalf("unexpected stderr: %q", diagnostic.String())
	}
}

func TestRejectsUnsupportedInputWithoutEcho(t *testing.T) {
	for _, args := range [][]string{
		{""}, {"serve"}, {"shell.exec"}, {"--listen=127.0.0.1:8080"},
		{"--config=/etc/wcp360/secret"}, {"--token=do-not-print-me"},
		{"--check", "--version"}, {"--help", "do-not-print-me"},
	} {
		var out, diagnostic bytes.Buffer
		if code := Run(args, &out, &diagnostic); code != 2 {
			t.Errorf("args %q: exit = %d, want 2", args, code)
		}
		if out.Len() != 0 || diagnostic.String() != "wcpd: unsupported arguments; use --help\n" {
			t.Errorf("unexpected response: stdout=%q stderr=%q", out.String(), diagnostic.String())
		}
	}
}

type brokenWriter struct{}

func (brokenWriter) Write([]byte) (int, error) {
	return 0, errors.New("output unavailable")
}

func TestOutputFailureIsNotSuccess(t *testing.T) {
	for _, args := range [][]string{nil, {"--version"}, {"--check"}, {"invalid"}} {
		if code := Run(args, brokenWriter{}, brokenWriter{}); code != 1 {
			t.Errorf("args %q: exit = %d, want 1", args, code)
		}
	}
}
