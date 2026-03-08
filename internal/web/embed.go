// ======================================================================
// WCP 360 | V0.1.0 | internal/web/embed.go
// ======================================================================

package web

import "io/fs"

//go:generate echo "templates embedded via go:embed in renderer.go"

// TemplatesFS is set by renderer.go via go:embed
var TemplatesFS fs.FS
