// ======================================================================
// WCP 360 | V0.1.0 | internal/database/queries/tenant.go
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
	"path/filepath"

	"github.com/wcp360/wcp360/internal/models"
)

type TenantFilter struct {
	Search string
	Status string
	Plan   string
}

func scanTenant(rows *sql.Rows) (*models.Tenant, error) {
	t := &models.Tenant{}
	return t, rows.Scan(&t.ID, &t.Username, &t.Email, &t.Plan, &t.Status,
		&t.DiskQuotaMB, &t.BandwidthMB, &t.MaxSites, &t.HomeDir, &t.CreatedAt, &t.UpdatedAt)
}

func scanTenantRow(row *sql.Row) (*models.Tenant, error) {
	t := &models.Tenant{}
	return t, row.Scan(&t.ID, &t.Username, &t.Email, &t.Plan, &t.Status,
		&t.DiskQuotaMB, &t.BandwidthMB, &t.MaxSites, &t.HomeDir, &t.CreatedAt, &t.UpdatedAt)
}

const tenantCols = `id, username, email, plan, status,
    disk_quota_mb, bandwidth_mb, max_sites, home_dir, created_at, updated_at`

func CreateTenant(ctx context.Context, db *sql.DB, req *models.CreateTenantRequest, dataDir string) (int64, error) {
	homeDir := filepath.Join(dataDir, req.Username)
	res, err := db.ExecContext(ctx,
		`INSERT INTO tenants(username,email,plan,status,disk_quota_mb,bandwidth_mb,max_sites,home_dir)
		 VALUES(?,?,?,?,?,?,?,?)`,
		req.Username, req.Email, req.Plan, "active",
		req.DiskQuotaMB, req.BandwidthMB, req.MaxSites, homeDir)
	if err != nil {
		return 0, fmt.Errorf("queries.CreateTenant: %w", err)
	}
	id, _ := res.LastInsertId()
	return id, nil
}

func GetTenantByID(ctx context.Context, db *sql.DB, id int64) (*models.Tenant, error) {
	row := db.QueryRowContext(ctx,
		`SELECT `+tenantCols+` FROM tenants WHERE id = ? AND deleted_at IS NULL LIMIT 1`, id)
	t, err := scanTenantRow(row)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) { return nil, ErrNotFound }
		return nil, fmt.Errorf("queries.GetTenantByID: %w", err)
	}
	return t, nil
}

func GetTenantByUsername(ctx context.Context, db *sql.DB, username string) (*models.Tenant, error) {
	row := db.QueryRowContext(ctx,
		`SELECT `+tenantCols+` FROM tenants WHERE username = ? COLLATE NOCASE AND deleted_at IS NULL LIMIT 1`, username)
	t, err := scanTenantRow(row)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) { return nil, ErrNotFound }
		return nil, fmt.Errorf("queries.GetTenantByUsername: %w", err)
	}
	return t, nil
}

func TenantUsernameExists(ctx context.Context, db *sql.DB, username string) (bool, error) {
	var n int
	err := db.QueryRowContext(ctx,
		`SELECT COUNT(*) FROM tenants WHERE username = ? COLLATE NOCASE AND deleted_at IS NULL`, username).Scan(&n)
	return n > 0, err
}

func ListTenantsPaginatedFiltered(ctx context.Context, db *sql.DB, page, perPage int, f TenantFilter) ([]models.Tenant, error) {
	if page < 1 { page = 1 }
	if perPage < 1 || perPage > 100 { perPage = 20 }
	offset := (page - 1) * perPage
	query := `SELECT ` + tenantCols + ` FROM tenants WHERE deleted_at IS NULL`
	args := []any{}
	if f.Search != "" {
		query += ` AND (username LIKE ? OR email LIKE ?)`
		like := "%" + f.Search + "%"
		args = append(args, like, like)
	}
	if f.Status != "" {
		query += ` AND status = ?`
		args = append(args, f.Status)
	}
	if f.Plan != "" {
		query += ` AND plan = ?`
		args = append(args, f.Plan)
	}
	query += ` ORDER BY created_at DESC LIMIT ? OFFSET ?`
	args = append(args, perPage, offset)
	rows, err := db.QueryContext(ctx, query, args...)
	if err != nil {
		return nil, fmt.Errorf("queries.ListTenantsPaginatedFiltered: %w", err)
	}
	defer rows.Close()
	var list []models.Tenant
	for rows.Next() {
		t, err := scanTenant(rows)
		if err != nil { return nil, err }
		list = append(list, *t)
	}
	return list, rows.Err()
}

func CountTenantsFiltered(ctx context.Context, db *sql.DB, f TenantFilter) (int, error) {
	query := `SELECT COUNT(*) FROM tenants WHERE deleted_at IS NULL`
	args := []any{}
	if f.Search != "" {
		query += ` AND (username LIKE ? OR email LIKE ?)`
		like := "%" + f.Search + "%"
		args = append(args, like, like)
	}
	if f.Status != "" { query += ` AND status = ?`; args = append(args, f.Status) }
	if f.Plan != "" { query += ` AND plan = ?`; args = append(args, f.Plan) }
	var n int
	return n, db.QueryRowContext(ctx, query, args...).Scan(&n)
}

func UpdateTenant(ctx context.Context, db *sql.DB, id int64, req *models.UpdateTenantRequest) error {
	_, err := db.ExecContext(ctx,
		`UPDATE tenants SET
		    email        = CASE WHEN ? != '' THEN ? ELSE email END,
		    plan         = CASE WHEN ? != '' THEN ? ELSE plan END,
		    status       = CASE WHEN ? != '' THEN ? ELSE status END,
		    disk_quota_mb= CASE WHEN ? > 0   THEN ? ELSE disk_quota_mb END,
		    bandwidth_mb = CASE WHEN ? > 0   THEN ? ELSE bandwidth_mb END,
		    max_sites    = CASE WHEN ? > 0   THEN ? ELSE max_sites END,
		    updated_at   = datetime('now','utc')
		 WHERE id = ? AND deleted_at IS NULL`,
		req.Email, req.Email,
		req.Plan, req.Plan,
		req.Status, req.Status,
		req.DiskQuotaMB, req.DiskQuotaMB,
		req.BandwidthMB, req.BandwidthMB,
		req.MaxSites, req.MaxSites,
		id)
	return err
}

func SoftDeleteTenant(ctx context.Context, db *sql.DB, id int64) error {
	res, err := db.ExecContext(ctx,
		`UPDATE tenants SET deleted_at=datetime('now','utc'), status='deleted', updated_at=datetime('now','utc')
		 WHERE id = ? AND deleted_at IS NULL`, id)
	if err != nil { return fmt.Errorf("queries.SoftDeleteTenant: %w", err) }
	n, _ := res.RowsAffected()
	if n == 0 { return ErrNotFound }
	return nil
}

func ToggleTenantStatus(ctx context.Context, db *sql.DB, id int64) (string, error) {
	tx, err := db.BeginTx(ctx, nil)
	if err != nil { return "", fmt.Errorf("queries.ToggleTenantStatus: begin: %w", err) }
	defer func() {
		if err != nil { tx.Rollback() }
	}()
	var current string
	err = tx.QueryRowContext(ctx,
		`SELECT status FROM tenants WHERE id = ? AND deleted_at IS NULL LIMIT 1`, id).Scan(&current)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) { return "", ErrNotFound }
		return "", fmt.Errorf("queries.ToggleTenantStatus: read: %w", err)
	}
	newStatus := "suspended"
	if current == "suspended" { newStatus = "active" }
	_, err = tx.ExecContext(ctx,
		`UPDATE tenants SET status=?, updated_at=datetime('now','utc') WHERE id=? AND deleted_at IS NULL`,
		newStatus, id)
	if err != nil { return "", fmt.Errorf("queries.ToggleTenantStatus: update: %w", err) }
	if err = tx.Commit(); err != nil { return "", fmt.Errorf("queries.ToggleTenantStatus: commit: %w", err) }
	return newStatus, nil
}
