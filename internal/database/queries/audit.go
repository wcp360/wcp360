// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/database/queries/audit.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/queries/audit.go
// Description: Audit log — append-only inserts, never UPDATE/DELETE (INV-8).
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"fmt"
	"log/slog"
<<<<<<< HEAD
	"time"
=======
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
)

const (
	ActionAdminLogin    = "admin.login"
	ActionAdminLogout   = "admin.logout"
	ActionTenantCreate  = "tenant.create"
	ActionTenantUpdate  = "tenant.update"
	ActionTenantDelete  = "tenant.delete"
	ActionTenantSuspend = "tenant.suspend"
)

<<<<<<< HEAD
type AuditEntry struct {
	ID        int64     `json:"id"`
	Actor     string    `json:"actor"`
	Action    string    `json:"action"`
	Target    string    `json:"target"`
	Detail    string    `json:"detail"`
	IPAddress string    `json:"ip_address"`
	CreatedAt time.Time `json:"created_at"`
}

// LogAction inserts an audit entry fire-and-forget (errors are only logged).
func LogAction(ctx context.Context, db *sql.DB, actor, action, target, detail, ip string) {
	_, err := db.ExecContext(ctx,
		`INSERT INTO audit_log(actor,action,target,detail,ip_address) VALUES(?,?,?,?,?)`,
		actor, action, target, detail, ip)
	if err != nil {
		slog.Warn("audit: log action failed", "action", action, "err", err)
=======
// LogAction inserts into audit_log. Fire-and-forget — never blocks caller.
func LogAction(ctx context.Context, db *sql.DB, actor, action, target, detail, ip string) {
	if _, err := db.ExecContext(ctx,
		`INSERT INTO audit_log (actor, action, target, detail, ip_address) VALUES (?, ?, ?, ?, ?)`,
		actor, action, target, detail, ip); err != nil {
		slog.Error("audit_log: insert failed", "actor", actor, "action", action, "err", err)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	}
}

func GetAuditLog(ctx context.Context, db *sql.DB, limit int) ([]AuditEntry, error) {
<<<<<<< HEAD
	if limit <= 0 || limit > 500 {
=======
	if limit <= 0 || limit > 1000 {
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
		limit = 50
	}
	rows, err := db.QueryContext(ctx,
		`SELECT id, actor, action, target, detail, ip_address, created_at
		 FROM audit_log ORDER BY created_at DESC LIMIT ?`, limit)
	if err != nil {
		return nil, fmt.Errorf("queries.GetAuditLog: %w", err)
	}
	defer rows.Close()
	var entries []AuditEntry
	for rows.Next() {
		var e AuditEntry
<<<<<<< HEAD
		if err := rows.Scan(&e.ID, &e.Actor, &e.Action, &e.Target,
			&e.Detail, &e.IPAddress, &e.CreatedAt); err != nil {
=======
		if err := rows.Scan(&e.ID, &e.Actor, &e.Action, &e.Target, &e.Detail, &e.IPAddress, &e.CreatedAt); err != nil {
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
			return nil, fmt.Errorf("queries.GetAuditLog scan: %w", err)
		}
		entries = append(entries, e)
	}
	return entries, rows.Err()
}

<<<<<<< HEAD
func GetAuditLogByTarget(ctx context.Context, db *sql.DB, target string, limit int) ([]AuditEntry, error) {
	if limit <= 0 || limit > 500 {
		limit = 50
	}
	rows, err := db.QueryContext(ctx,
		`SELECT id, actor, action, target, detail, ip_address, created_at
		 FROM audit_log WHERE target = ? ORDER BY created_at DESC LIMIT ?`, target, limit)
	if err != nil {
		return nil, fmt.Errorf("queries.GetAuditLogByTarget: %w", err)
	}
	defer rows.Close()
	var entries []AuditEntry
	for rows.Next() {
		var e AuditEntry
		if err := rows.Scan(&e.ID, &e.Actor, &e.Action, &e.Target,
			&e.Detail, &e.IPAddress, &e.CreatedAt); err != nil {
			return nil, err
		}
		entries = append(entries, e)
	}
	return entries, rows.Err()
}

type AuditPage struct {
	Entries []AuditEntry
	Total   int
}

func GetAuditLogPaginated(ctx context.Context, db *sql.DB, page, perPage int) (*AuditPage, error) {
	if page < 1 { page = 1 }
	if perPage < 1 || perPage > 200 { perPage = 50 }
	offset := (page - 1) * perPage
	rows, err := db.QueryContext(ctx,
		`SELECT id, actor, action, target, detail, ip_address, created_at,
		        COUNT(*) OVER() AS total_count
		 FROM audit_log ORDER BY created_at DESC LIMIT ? OFFSET ?`, perPage, offset)
	if err != nil {
		return nil, fmt.Errorf("queries.GetAuditLogPaginated: %w", err)
	}
	defer rows.Close()
	var ap AuditPage
	for rows.Next() {
		var e AuditEntry
		if err := rows.Scan(&e.ID, &e.Actor, &e.Action, &e.Target,
			&e.Detail, &e.IPAddress, &e.CreatedAt, &ap.Total); err != nil {
			return nil, err
		}
		ap.Entries = append(ap.Entries, e)
	}
	return &ap, rows.Err()
=======
type AuditEntry struct {
	ID        int64  `json:"id"`
	Actor     string `json:"actor"`
	Action    string `json:"action"`
	Target    string `json:"target"`
	Detail    string `json:"detail,omitempty"`
	IPAddress string `json:"ip_address,omitempty"`
	CreatedAt string `json:"created_at"`
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}
