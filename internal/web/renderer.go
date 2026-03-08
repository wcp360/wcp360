// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/web/renderer.go
// Description: HTML template renderer with prewarm cache.
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/web/renderer.go
// Description: HTML template renderer — parses embedded templates and
//              executes them. Supports full-page and login-only renders.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package web

import (
<<<<<<< HEAD
	"embed"
=======
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	"html/template"
	"io/fs"
	"log/slog"
	"net/http"
<<<<<<< HEAD
	"strings"
	"sync"
)

//go:embed templates/*.html
var templatesFS embed.FS

// TemplatesFS is exported for use in embed.go
func init() { TemplatesFS = templatesFS }

type Renderer struct {
	fs    fs.FS
	cache sync.Map
}

func NewRenderer() *Renderer {
	sub, err := fs.Sub(templatesFS, "templates")
	if err != nil { panic("web.NewRenderer: " + err.Error()) }
	r := &Renderer{fs: sub}
	r.prewarm()
	return r
}

func (r *Renderer) prewarm() {
	pages := []string{"dashboard", "tenants", "tenant_detail", "audit"}
	for _, page := range pages {
		tmpl, err := template.New("").Funcs(funcMap).ParseFS(r.fs, "base.html", page+".html")
		if err != nil { panic("web.Renderer prewarm " + page + ": " + err.Error()) }
		r.cache.Store(page, tmpl)
		slog.Debug("renderer: cached", "page", page)
	}
	loginTmpl, err := template.New("").ParseFS(r.fs, "login.html")
	if err != nil { panic("web.Renderer prewarm login: " + err.Error()) }
	r.cache.Store("login", loginTmpl)
=======
)

type Renderer struct{ fs fs.FS }

func NewRenderer() *Renderer {
	sub, err := fs.Sub(TemplatesFS, "templates")
	if err != nil { panic("web.NewRenderer: sub-fs error: " + err.Error()) }
	return &Renderer{fs: sub}
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}

var funcMap = template.FuncMap{
	"prev": func(n int) int { return n - 1 },
	"next": func(n int) int { return n + 1 },
<<<<<<< HEAD
	"buildQS": func(pairs ...string) string {
		if len(pairs)%2 != 0 { return "" }
		var parts []string
		for i := 0; i < len(pairs); i += 2 {
			if pairs[i+1] != "" { parts = append(parts, pairs[i]+"="+pairs[i+1]) }
		}
		return strings.Join(parts, "&")
	},
}

func (r *Renderer) Render(w http.ResponseWriter, status int, page string, data any) {
	val, ok := r.cache.Load(page)
	if !ok {
		tmpl, err := template.New("").Funcs(funcMap).ParseFS(r.fs, "base.html", page+".html")
		if err != nil { slog.Error("renderer: fallback parse", "page", page, "err", err); http.Error(w, "Template error", 500); return }
		val = tmpl
	}
	tmpl := val.(*template.Template)
=======
}

func (r *Renderer) Render(w http.ResponseWriter, status int, page string, data any) {
	tmpl, err := template.New("").Funcs(funcMap).ParseFS(r.fs, "base.html", page+".html")
	if err != nil {
		slog.Error("renderer: parse", "page", page, "err", err)
		http.Error(w, "Template error", http.StatusInternalServerError)
		return
	}
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	w.WriteHeader(status)
	if err := tmpl.ExecuteTemplate(w, "base", data); err != nil {
		slog.Error("renderer: execute", "page", page, "err", err)
	}
}

func (r *Renderer) RenderLogin(w http.ResponseWriter, status int, data any) {
<<<<<<< HEAD
	val, ok := r.cache.Load("login")
	if !ok { http.Error(w, "Template error", 500); return }
	tmpl := val.(*template.Template)
=======
	tmpl, err := template.New("").ParseFS(r.fs, "login.html")
	if err != nil {
		slog.Error("renderer: parse login", "err", err)
		http.Error(w, "Template error", http.StatusInternalServerError)
		return
	}
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	w.Header().Set("Content-Type", "text/html; charset=utf-8")
	w.WriteHeader(status)
	if err := tmpl.ExecuteTemplate(w, "page", data); err != nil {
		slog.Error("renderer: execute login", "err", err)
	}
}
