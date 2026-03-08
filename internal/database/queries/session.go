// ======================================================================
// WCP 360 | V0.1.0 | internal/database/queries/session.go
// ======================================================================

package queries

import (
	"context"
	"database/sql"
	"fmt"
	"time"
)

func RegisterSession(ctx context.Context, db *sql.DB, jti, username, role string, expiresAt time.Time) error {
	_, err := db.ExecContext(ctx,
		`INSERT INTO sessions(jti, username, role, expires_at) VALUES(?,?,?,?)`,
		jti, username, role, expiresAt.UTC().Format("2006-01-02T15:04:05Z"))
	if err != nil {
		return fmt.Errorf("queries.RegisterSession: %w", err)
	}
	return nil
}

func IsTokenInvalidated(ctx context.Context, db *sql.DB, jti string) (bool, error) {
	var revoked int
	err := db.QueryRowContext(ctx,
		`SELECT revoked FROM sessions
		 WHERE jti = ? AND expires_at > datetime('now','utc') LIMIT 1`, jti).Scan(&revoked)
	if err != nil {
		if err.Error() == "sql: no rows in result set" {
			return true, nil // not found = invalidated (or expired)
		}
		return true, fmt.Errorf("queries.IsTokenInvalidated: %w", err)
	}
	return revoked == 1, nil
}

func InvalidateSession(ctx context.Context, db *sql.DB, jti string) error {
	_, err := db.ExecContext(ctx,
		`UPDATE sessions SET revoked = 1 WHERE jti = ?`, jti)
	return err
}
