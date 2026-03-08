// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/database/queries/admin.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/queries/admin.go
// Description: SQL queries for the admins table.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"errors"
	"fmt"
<<<<<<< HEAD
=======
	"time"
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee

	"github.com/wcp360/wcp360/internal/models"
)

<<<<<<< HEAD
var ErrNotFound = errors.New("not found")

func GetAdminByUsername(ctx context.Context, db *sql.DB, username string) (*models.Admin, error) {
	row := db.QueryRowContext(ctx,
		`SELECT id, username, email, password_hash, role, created_at, updated_at
		 FROM admins WHERE username = ? COLLATE NOCASE LIMIT 1`, username)
	a := &models.Admin{}
	err := row.Scan(&a.ID, &a.Username, &a.Email, &a.PasswordHash,
		&a.Role, &a.CreatedAt, &a.UpdatedAt)
=======
var ErrNotFound = errors.New("record not found")

func GetAdminByUsername(ctx context.Context, db *sql.DB, username string) (*models.Admin, error) {
	const q = `
		SELECT id, username, password_hash, email, role, is_active,
		       last_login_at, created_at, updated_at
		FROM admins
		WHERE username = ? COLLATE NOCASE AND is_active = 1
		LIMIT 1`

	row := db.QueryRowContext(ctx, q, username)
	var a models.Admin
	var lastLogin sql.NullString
	var createdAt, updatedAt string

	err := row.Scan(&a.ID, &a.Username, &a.PasswordHash, &a.Email,
		&a.Role, &a.IsActive, &lastLogin, &createdAt, &updatedAt)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			return nil, ErrNotFound
		}
		return nil, fmt.Errorf("queries.GetAdminByUsername: %w", err)
	}
<<<<<<< HEAD
	return a, nil
=======
	if lastLogin.Valid {
		t, _ := time.Parse(time.DateTime, lastLogin.String)
		a.LastLoginAt = &t
	}
	if t, err := time.Parse(time.DateTime, createdAt); err == nil {
		a.CreatedAt = t
	}
	if t, err := time.Parse(time.DateTime, updatedAt); err == nil {
		a.UpdatedAt = t
	}
	return &a, nil
}

func CreateAdmin(ctx context.Context, db *sql.DB, username, passwordHash, email, role string) (int64, error) {
	res, err := db.ExecContext(ctx,
		`INSERT INTO admins (username, password_hash, email, role) VALUES (?, ?, ?, ?)`,
		username, passwordHash, email, role)
	if err != nil {
		return 0, fmt.Errorf("queries.CreateAdmin: %w", err)
	}
	id, _ := res.LastInsertId()
	return id, nil
}

func UpdateLastLogin(ctx context.Context, db *sql.DB, id int64) error {
	_, err := db.ExecContext(ctx,
		`UPDATE admins SET last_login_at = datetime('now','utc'), updated_at = datetime('now','utc') WHERE id = ?`, id)
	if err != nil {
		return fmt.Errorf("queries.UpdateLastLogin: %w", err)
	}
	return nil
}

func AdminExists(ctx context.Context, db *sql.DB) (bool, error) {
	var count int
	if err := db.QueryRowContext(ctx, "SELECT COUNT(*) FROM admins").Scan(&count); err != nil {
		return false, fmt.Errorf("queries.AdminExists: %w", err)
	}
	return count > 0, nil
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}
