// ======================================================================
// WCP 360 | V0.1.0 | internal/services/email.go
// ======================================================================

package services

import (
	"bytes"
	"crypto/tls"
	"fmt"
	"log/slog"
	"net"
	"net/smtp"
	"strings"
	"time"

	"github.com/wcp360/wcp360/internal/config"
)

type Mailer interface {
	SendInvite(to, username, loginURL string) error
	Enabled() bool
}

type SMTPMailer struct {
	host, username, password, from string
	port     int
	startTLS bool
}

func NewSMTPMailer(cfg *config.Config) *SMTPMailer {
	return &SMTPMailer{
		host: cfg.SMTPHost, port: cfg.SMTPPort,
		username: cfg.SMTPUsername, password: cfg.SMTPPassword,
		from: cfg.SMTPFrom, startTLS: cfg.SMTPStartTLS,
	}
}

func (m *SMTPMailer) Enabled() bool { return m.host != "" }

func (m *SMTPMailer) SendInvite(to, username, loginURL string) error {
	if !m.Enabled() { return fmt.Errorf("email: SMTP not configured") }
	subject := fmt.Sprintf("Welcome to WCP360 — your account for %s is ready", username)
	body := fmt.Sprintf(`<!DOCTYPE html><html><body style="font-family:sans-serif;background:#050A14;color:#B8CCEB;padding:40px"><h1 style="color:#4EFFC5">⬡ WCP360</h1><h2>Welcome, %s!</h2><p>Your hosting account is ready.</p><p><a href="%s" style="background:#4EFFC5;color:#050A14;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600">Access your account →</a></p></body></html>`, username, loginURL)
	var buf bytes.Buffer
	fmt.Fprintf(&buf, "From: WCP360 <%s>\r\nTo: %s\r\nSubject: %s\r\nDate: %s\r\nMIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n%s",
		m.from, to, subject, time.Now().UTC().Format("Mon, 02 Jan 2006 15:04:05 -0700"), body)
	addr := fmt.Sprintf("%s:%d", m.host, m.port)
	host, _, _ := net.SplitHostPort(addr)
	c, err := smtp.Dial(addr)
	if err != nil { return fmt.Errorf("smtp.Dial: %w", err) }
	defer c.Quit()
	if m.startTLS {
		if err := c.StartTLS(&tls.Config{ServerName: host, MinVersion: tls.VersionTLS12}); err != nil {
			return fmt.Errorf("STARTTLS: %w", err)
		}
	}
	if m.username != "" {
		auth := smtp.PlainAuth("", m.username, m.password, host)
		if err := c.Auth(auth); err != nil { return fmt.Errorf("smtp auth: %w", err) }
	}
	if err := c.Mail(m.from); err != nil { return err }
	if err := c.Rcpt(to); err != nil { return err }
	w, err := c.Data()
	if err != nil { return err }
	defer w.Close()
	_, err = w.Write(buf.Bytes())
	if err == nil { slog.Info("email: invite sent", "to", to, "username", username) }
	return err
}

type NoopMailer struct{}
func (n *NoopMailer) Enabled() bool { return false }
func (n *NoopMailer) SendInvite(to, username, loginURL string) error {
	slog.Info("email: [NOOP] invite", "to", to, "username", username)
	return nil
}

func NewMailer(cfg *config.Config) Mailer {
	if cfg.EmailEnabled() {
		slog.Info("email: SMTP mailer configured", "host", cfg.SMTPHost)
		return NewSMTPMailer(cfg)
	}
	slog.Info("email: using noop mailer")
	return &NoopMailer{}
}

func SendTenantInvite(mailer Mailer, domain, to, username string) error {
	if !mailer.Enabled() {
		slog.Info("email: invite skipped — mailer not enabled", "to", to, "username", username)
		return nil
	}
	loginURL := fmt.Sprintf("https://%s/admin/login", strings.TrimRight(domain, "/"))
	return mailer.SendInvite(to, username, loginURL)
}
