// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/web/embed.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/web/embed.go
// Description: Embeds all HTML templates into the binary at compile time.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package web

<<<<<<< HEAD
import "io/fs"

//go:generate echo "templates embedded via go:embed in renderer.go"

// TemplatesFS is set by renderer.go via go:embed
var TemplatesFS fs.FS
=======
import "embed"

//go:embed templates/*.html
var TemplatesFS embed.FS
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
