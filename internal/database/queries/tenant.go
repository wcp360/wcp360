// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/queries/tenant.go
// Description: Full CRUD + paginated list for the tenants table.
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"time"

	"github.com/wcp360/wcp360/internal/models"
)

func ListTenants(ctx context.Context, db *sql.DB) ([]models.Tenant, error) {
	const q = `SELECT id, username, email, plan, status,
		disk_quota_mb, bandwidth_mb, max_sites, home_dir, created_at, updated_at
		FROM tenants WHERE deleted_at IS NULL ORDER BY created_at DESC`
	rows, err := db.QueryContext(ctx, q)
	if err != nil {
		return nil, fmt.Errorf("queries.ListTenants: %w", err)
	}
	defer rows.Close()
	var list []models.Tenant
	for rows.Next() {
		t, err := scanTenant(rows)
		if err != nil {
			return nil, err
		}
		list = append(list, *t)
	}
	return list, rows.Err()
}

func ListTenantsPaginated(ctx context.Context, db *sql.DB, page, perPage int) ([]models.Tenant, error) {
	if page < 1 { page = 1 }
	if perPage < 1 || perPage > 100 { perPage = 20 }
	offset := (page - 1) * perPage
	const q = `SELECT id, username, email, plan, status,
		disk_quota_mb, bandwidth_mb, max_sites, home_dir, created_at, updated_at
		FROM tenants WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT ? OFFSET ?`
	rows, err := db.QueryContext(ctx, q, perPage, offset)
	if err != nil {
		return nil, fmt.Errorf("queries.ListTenantsPaginated: %w", err)
	}
	defer rows.Close()
	var list []models.Tenant
	for rows.Next() {
		t, err := scanTenant(rows)
		if err != nil {
			return nil, err
		}
		list = append(list, *t)
	}
	return list, rows.Err()
}

func GetTenantByID(ctx context.Context, db *sql.DB, id int64) (*models.Tenant, error) {
	const q = `SELECT id, username, email, plan, status,
		disk_quota_mb, bandwidth_mb, max_sites, home_dir, created_at, updated_at
		FROM tenants WHERE id = ? AND deleted_at IS NULL LIMIT 1`
	row := db.QueryRowContext(ctx, q, id)
	t, err := scanTenantRow(row)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			return nil, ErrNotFound
		}
		return nil, fmt.Errorf("queries.GetTenantByID: %w", err)
	}
	return t, nil
}

func TenantUsernameExists(ctx context.Context, db *sql.DB, username string) (bool, error) {
	var n int
	err := db.QueryRowContext(ctx,
		`SELECT COUNT(*) FROM tenants WHERE username = ? COLLATE NOCASE AND deleted_at IS NULL`,
		username).Scan(&n)
	if err != nil {
		return false, fmt.Errorf("queries.TenantUsernameExists: %w", err)
	}
	return n > 0, nil
}

func CreateTenant(ctx context.Context, db *sql.DB, req *models.CreateTenantRequest, dataDir string) (int64, error) {
	homeDir := dataDir + "/" + req.Username
	res, err := db.ExecContext(ctx,
		`INSERT INTO tenants (username, email, plan, status, disk_quota_mb, bandwidth_mb, max_sites, home_dir)
		 VALUES (?, ?, ?, 'active', ?, ?, ?, ?)`,
		req.Username, req.Email, req.Plan, req.DiskQuotaMB, req.BandwidthMB, req.MaxSites, homeDir)
	if err != nil {
		return 0, fmt.Errorf("queries.CreateTenant: %w", err)
	}
	id, _ := res.LastInsertId()
	return id, nil
}

func UpdateTenant(ctx context.Context, db *sql.DB, id int64, req *models.UpdateTenantRequest) error {
	const q = `UPDATE tenants SET
		email         = COALESCE(NULLIF(?, ''), email),
		plan          = COALESCE(NULLIF(?, ''), plan),
		status        = COALESCE(NULLIF(?, ''), status),
		disk_quota_mb = CASE WHEN ? > 0 THEN ? ELSE disk_quota_mb END,
		bandwidth_mb  = CASE WHEN ? > 0 THEN ? ELSE bandwidth_mb  END,
		max_sites     = CASE WHEN ? > 0 THEN ? ELSE max_sites     END,
		updated_at    = datetime('now','utc')
		WHERE id = ? AND deleted_at IS NULL`
	res, err := db.ExecContext(ctx, q,
		req.Email, req.Plan, req.Status,
		req.DiskQuotaMB, req.DiskQuotaMB,
		req.BandwidthMB, req.BandwidthMB,
		req.MaxSites, req.MaxSites, id)
	if err != nil {
		return fmt.Errorf("queries.UpdateTenant: %w", err)
	}
	n, _ := res.RowsAffected()
	if n == 0 {
		return ErrNotFound
	}
	return nil
}

func SoftDeleteTenant(ctx context.Context, db *sql.DB, id int64) error {
	res, err := db.ExecContext(ctx,
		`UPDATE tenants SET deleted_at = datetime('now','utc'), status = 'deleted', updated_at = datetime('now','utc')
		 WHERE id = ? AND deleted_at IS NULL`, id)
	if err != nil {
		return fmt.Errorf("queries.SoftDeleteTenant: %w", err)
	}
	n, _ := res.RowsAffected()
	if n == 0 {
		return ErrNotFound
	}
	return nil
}

type rowScanner interface{ Scan(dest ...any) error }

func scanTenant(s rowScanner) (*models.Tenant, error) {
	var t models.Tenant
	var ca, ua string
	if err := s.Scan(&t.ID, &t.Username, &t.Email, &t.Plan, &t.Status,
		&t.DiskQuotaMB, &t.BandwidthMB, &t.MaxSites, &t.HomeDir, &ca, &ua); err != nil {
		return nil, err
	}
	parseTime(ca, &t.CreatedAt)
	parseTime(ua, &t.UpdatedAt)
	return &t, nil
}

func scanTenantRow(row *sql.Row) (*models.Tenant, error) {
	var t models.Tenant
	var ca, ua string
	if err := row.Scan(&t.ID, &t.Username, &t.Email, &t.Plan, &t.Status,
		&t.DiskQuotaMB, &t.BandwidthMB, &t.MaxSites, &t.HomeDir, &ca, &ua); err != nil {
		return nil, err
	}
	parseTime(ca, &t.CreatedAt)
	parseTime(ua, &t.UpdatedAt)
	return &t, nil
}

func parseTime(s string, dst *time.Time) {
	if t, err := time.Parse(time.DateTime, s); err == nil {
		*dst = t
	}
}
