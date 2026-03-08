// ======================================================================
// WCP 360 | V0.1.0 | internal/services/email_test.go
// ======================================================================

package services

import (
	"testing"
	"github.com/wcp360/wcp360/internal/config"
)

func TestNoopMailer(t *testing.T) {
	m := &NoopMailer{}
	if m.Enabled() { t.Error("NoopMailer.Enabled() must be false") }
	if err := m.SendInvite("t@t.com", "alice", "https://x"); err != nil {
		t.Errorf("NoopMailer.SendInvite() error: %v", err)
	}
}

func TestNewMailer_NoSMTP(t *testing.T) {
	m := NewMailer(&config.Config{SMTPHost: ""})
	if m.Enabled() { t.Error("expected noop when no SMTP") }
}

func TestNewMailer_WithSMTP(t *testing.T) {
	m := NewMailer(&config.Config{SMTPHost: "smtp.example.com", SMTPPort: 587, SMTPFrom: "x@x.com"})
	if !m.Enabled() { t.Error("expected SMTP mailer") }
}

func TestSendTenantInvite_Noop(t *testing.T) {
	if err := SendTenantInvite(&NoopMailer{}, "wcp360.com", "a@a.com", "alice"); err != nil {
		t.Error(err)
	}
}

func TestSMTPMailer_UnreachableErrors(t *testing.T) {
	m := &SMTPMailer{host: "127.0.0.1", port: 1, from: "x@x.com"}
	if err := m.SendInvite("t@t.com", "alice", "https://x"); err == nil {
		t.Error("expected error for unreachable server")
	}
}
