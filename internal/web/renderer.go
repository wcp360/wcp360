// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/web/renderer.go
// Description: HTML template renderer — parses embedded templates and
//              executes them. Supports full-page and login-only renders.
// ======================================================================

package web

import (
	"html/template"
	"io/fs"
	"log/slog"
	"net/http"
)

type Renderer struct{ fs fs.FS }

func NewRenderer() *Renderer {
	sub, err := fs.Sub(TemplatesFS, "templates")
	if err != nil { panic("web.NewRenderer: sub-fs error: " + err.Error()) }
	return &Renderer{fs: sub}
}

var funcMap = template.FuncMap{
	"prev": func(n int) int { return n - 1 },
	"next": func(n int) int { return n + 1 },
}

func (r *Renderer) Render(w http.ResponseWriter, status int, page string, data any) {
	tmpl, err := template.New("").Funcs(funcMap).ParseFS(r.fs, "base.html", page+".html")
	if err != nil {
		slog.Error("renderer: parse", "page", page, "err", err)
		http.Error(w, "Template error", http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	w.WriteHeader(status)
	if err := tmpl.ExecuteTemplate(w, "base", data); err != nil {
		slog.Error("renderer: execute", "page", page, "err", err)
	}
}

func (r *Renderer) RenderLogin(w http.ResponseWriter, status int, data any) {
	tmpl, err := template.New("").ParseFS(r.fs, "login.html")
	if err != nil {
		slog.Error("renderer: parse login", "err", err)
		http.Error(w, "Template error", http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	w.WriteHeader(status)
	if err := tmpl.ExecuteTemplate(w, "page", data); err != nil {
		slog.Error("renderer: execute login", "err", err)
	}
}
