// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/queries/audit.go
// Description: Audit log — append-only inserts, never UPDATE/DELETE (INV-8).
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"fmt"
	"log/slog"
)

const (
	ActionAdminLogin    = "admin.login"
	ActionAdminLogout   = "admin.logout"
	ActionTenantCreate  = "tenant.create"
	ActionTenantUpdate  = "tenant.update"
	ActionTenantDelete  = "tenant.delete"
	ActionTenantSuspend = "tenant.suspend"
)

// LogAction inserts into audit_log. Fire-and-forget — never blocks caller.
func LogAction(ctx context.Context, db *sql.DB, actor, action, target, detail, ip string) {
	if _, err := db.ExecContext(ctx,
		`INSERT INTO audit_log (actor, action, target, detail, ip_address) VALUES (?, ?, ?, ?, ?)`,
		actor, action, target, detail, ip); err != nil {
		slog.Error("audit_log: insert failed", "actor", actor, "action", action, "err", err)
	}
}

func GetAuditLog(ctx context.Context, db *sql.DB, limit int) ([]AuditEntry, error) {
	if limit <= 0 || limit > 1000 {
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
		if err := rows.Scan(&e.ID, &e.Actor, &e.Action, &e.Target, &e.Detail, &e.IPAddress, &e.CreatedAt); err != nil {
			return nil, fmt.Errorf("queries.GetAuditLog scan: %w", err)
		}
		entries = append(entries, e)
	}
	return entries, rows.Err()
}

type AuditEntry struct {
	ID        int64  `json:"id"`
	Actor     string `json:"actor"`
	Action    string `json:"action"`
	Target    string `json:"target"`
	Detail    string `json:"detail,omitempty"`
	IPAddress string `json:"ip_address,omitempty"`
	CreatedAt string `json:"created_at"`
}
