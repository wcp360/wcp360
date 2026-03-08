// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/queries/stats.go
// Description: Dashboard statistics aggregation queries.
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"fmt"
)

type DashboardStats struct {
	TotalTenants     int
	ActiveTenants    int
	SuspendedTenants int
	RecentAudit      []AuditEntry
}

func GetDashboardStats(ctx context.Context, db *sql.DB) (*DashboardStats, error) {
	stats := &DashboardStats{}
	const countQ = `
		SELECT
			COUNT(*),
			SUM(CASE WHEN status = 'active'    THEN 1 ELSE 0 END),
			SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END)
		FROM tenants WHERE deleted_at IS NULL`

	row := db.QueryRowContext(ctx, countQ)
	if err := row.Scan(&stats.TotalTenants, &stats.ActiveTenants, &stats.SuspendedTenants); err != nil {
		return nil, fmt.Errorf("queries.GetDashboardStats: %w", err)
	}
	entries, err := GetAuditLog(ctx, db, 10)
	if err != nil {
		return stats, nil
	}
	stats.RecentAudit = entries
	return stats, nil
}

func CountTenants(ctx context.Context, db *sql.DB) (int, error) {
	var n int
	if err := db.QueryRowContext(ctx,
		`SELECT COUNT(*) FROM tenants WHERE deleted_at IS NULL`).Scan(&n); err != nil {
		return 0, fmt.Errorf("queries.CountTenants: %w", err)
	}
	return n, nil
}
