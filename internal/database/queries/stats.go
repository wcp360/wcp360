// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/database/queries/stats.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/queries/stats.go
// Description: Dashboard statistics aggregation queries.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"fmt"
)

type DashboardStats struct {
<<<<<<< HEAD
	TotalTenants    int
	ActiveTenants   int
	SuspendedTenants int
	RecentAudit     []AuditEntry
}

func GetDashboardStats(ctx context.Context, db *sql.DB) (*DashboardStats, error) {
	s := &DashboardStats{}
	err := db.QueryRowContext(ctx,
		`SELECT
		    COUNT(*) FILTER (WHERE deleted_at IS NULL),
		    COUNT(*) FILTER (WHERE status='active'    AND deleted_at IS NULL),
		    COUNT(*) FILTER (WHERE status='suspended' AND deleted_at IS NULL)
		 FROM tenants`).Scan(&s.TotalTenants, &s.ActiveTenants, &s.SuspendedTenants)
	if err != nil {
		return nil, fmt.Errorf("queries.GetDashboardStats: %w", err)
	}
	s.RecentAudit, _ = GetAuditLog(ctx, db, 10)
	return s, nil
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}

func CountTenants(ctx context.Context, db *sql.DB) (int, error) {
	var n int
<<<<<<< HEAD
	return n, db.QueryRowContext(ctx,
		`SELECT COUNT(*) FROM tenants WHERE deleted_at IS NULL`).Scan(&n)
=======
	if err := db.QueryRowContext(ctx,
		`SELECT COUNT(*) FROM tenants WHERE deleted_at IS NULL`).Scan(&n); err != nil {
		return 0, fmt.Errorf("queries.CountTenants: %w", err)
	}
	return n, nil
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}
