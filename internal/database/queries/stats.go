// ======================================================================
// WCP 360 | V0.1.0 | internal/database/queries/stats.go
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"fmt"
)

type DashboardStats struct {
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
}

func CountTenants(ctx context.Context, db *sql.DB) (int, error) {
	var n int
	return n, db.QueryRowContext(ctx,
		`SELECT COUNT(*) FROM tenants WHERE deleted_at IS NULL`).Scan(&n)
}
