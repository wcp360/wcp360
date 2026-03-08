// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.1.0
// File: internal/database/queries/errors.go
// ======================================================================

package queries

import "errors"

// ErrNotFound is returned when a queried resource does not exist.
var ErrNotFound = errors.New("not found")
