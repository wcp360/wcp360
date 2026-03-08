// ======================================================================
// WCP 360 | V0.1.0 | internal/database/queries/admin.go
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"errors"
	"fmt"

	"github.com/wcp360/wcp360/internal/models"
)

var ErrNotFound = errors.New("not found")

func GetAdminByUsername(ctx context.Context, db *sql.DB, username string) (*models.Admin, error) {
	row := db.QueryRowContext(ctx,
		`SELECT id, username, email, password_hash, role, created_at, updated_at
		 FROM admins WHERE username = ? COLLATE NOCASE LIMIT 1`, username)
	a := &models.Admin{}
	err := row.Scan(&a.ID, &a.Username, &a.Email, &a.PasswordHash,
		&a.Role, &a.CreatedAt, &a.UpdatedAt)
	if err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			return nil, ErrNotFound
		}
		return nil, fmt.Errorf("queries.GetAdminByUsername: %w", err)
	}
	return a, nil
}
