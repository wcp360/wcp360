// ======================================================================
// WCP 360 | V0.1.0 | internal/services/provisioner.go
// ======================================================================

package services

import (
	"fmt"
	"log/slog"
	"os"
	"path/filepath"
	"strings"
)

type TenantFS struct {
	HomeDir    string
	PublicHTML string
	LogsDir    string
	TmpDir     string
}

func NewTenantFS(dataDir, username string) TenantFS {
	home := filepath.Join(dataDir, username)
	return TenantFS{
		HomeDir:    home,
		PublicHTML: filepath.Join(home, "public_html"),
		LogsDir:    filepath.Join(home, "logs"),
		TmpDir:     filepath.Join(home, "tmp"),
	}
}

func ProvisionTenant(dataDir, username string) (*TenantFS, error) {
	if err := validateUsername(username); err != nil {
		return nil, fmt.Errorf("provisioner: %w", err)
	}
	fs := NewTenantFS(dataDir, username)
	dirs := []struct {
		path string
		mode os.FileMode
	}{
		{fs.HomeDir, 0750},
		{fs.PublicHTML, 0755},
		{fs.LogsDir, 0750},
		{fs.TmpDir, 0700},
	}
	for _, d := range dirs {
		if err := os.MkdirAll(d.path, d.mode); err != nil {
			return nil, fmt.Errorf("provisioner: create %s: %w", d.path, err)
		}
		if err := os.Chmod(d.path, d.mode); err != nil {
			slog.Warn("provisioner: chmod failed", "path", d.path, "err", err)
		}
	}
	keepFile := filepath.Join(fs.HomeDir, ".keep")
	if _, err := os.Stat(keepFile); os.IsNotExist(err) {
		if err := os.WriteFile(keepFile, []byte("wcp360\n"), 0640); err != nil {
			slog.Warn("provisioner: write .keep failed", "err", err)
		}
	}
	indexFile := filepath.Join(fs.PublicHTML, "index.html")
	if _, err := os.Stat(indexFile); os.IsNotExist(err) {
		content := fmt.Sprintf(defaultIndexHTML, username)
		if err := os.WriteFile(indexFile, []byte(content), 0644); err != nil {
			slog.Warn("provisioner: write index.html failed", "err", err)
		}
	}
	slog.Info("provisioner: directories created", "username", username, "home", fs.HomeDir)
	return &fs, nil
}

func DeprovisionTenant(dataDir, username string) error {
	if err := validateUsername(username); err != nil {
		return fmt.Errorf("provisioner: %w", err)
	}
	fs := NewTenantFS(dataDir, username)
	if !strings.HasPrefix(fs.HomeDir, filepath.Clean(dataDir)+"/") {
		return fmt.Errorf("provisioner: home dir %q is outside dataDir %q", fs.HomeDir, dataDir)
	}
	if err := os.RemoveAll(fs.HomeDir); err != nil {
		return fmt.Errorf("provisioner: remove %s: %w", fs.HomeDir, err)
	}
	slog.Info("provisioner: home directory removed", "username", username)
	return nil
}

func validateUsername(username string) error {
	if username == "" { return fmt.Errorf("username must not be empty") }
	if strings.Contains(username, "/") || strings.Contains(username, "..") || strings.Contains(username, "\x00") {
		return fmt.Errorf("username %q contains invalid characters", username)
	}
	return nil
}

const defaultIndexHTML = `<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Welcome</title>
<style>body{font-family:system-ui,sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;background:#050A14;color:#B8CCEB}.box{text-align:center;padding:40px}h1{color:#4EFFC5;font-size:2rem;margin-bottom:8px}p{color:#4F6488;font-size:.9rem}</style>
</head>
<body>
  <div class="box">
    <h1>⬡ WCP360</h1>
    <p>Site for <strong>%s</strong> is ready.</p>
    <p>Upload your files to <code>public_html/</code> to get started.</p>
  </div>
</body>
</html>`
