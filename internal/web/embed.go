// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/web/embed.go
// Description: Embeds all HTML templates into the binary at compile time.
// ======================================================================

package web

import "embed"

//go:embed templates/*.html
var TemplatesFS embed.FS
