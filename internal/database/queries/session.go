// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/queries/session.go
// Description: JWT session / token blocklist queries.
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
		`INSERT INTO sessions (jti, username, role, expires_at) VALUES (?, ?, ?, ?)`,
		jti, username, role, expiresAt.UTC().Format(time.DateTime))
	if err != nil {
		return fmt.Errorf("queries.RegisterSession: %w", err)
	}
	return nil
}

func InvalidateSession(ctx context.Context, db *sql.DB, jti string) error {
	_, err := db.ExecContext(ctx, `UPDATE sessions SET invalidated = 1 WHERE jti = ?`, jti)
	if err != nil {
		return fmt.Errorf("queries.InvalidateSession: %w", err)
	}
	return nil
}

func IsTokenInvalidated(ctx context.Context, db *sql.DB, jti string) (bool, error) {
	var invalidated int
	err := db.QueryRowContext(ctx,
		`SELECT invalidated FROM sessions WHERE jti = ? LIMIT 1`, jti).Scan(&invalidated)
	if err == sql.ErrNoRows {
		return false, nil
	}
	if err != nil {
		return false, fmt.Errorf("queries.IsTokenInvalidated: %w", err)
	}
	return invalidated == 1, nil
}

func PruneExpiredSessions(ctx context.Context, db *sql.DB) (int64, error) {
	res, err := db.ExecContext(ctx,
		`DELETE FROM sessions WHERE expires_at < datetime('now','utc')`)
	if err != nil {
		return 0, fmt.Errorf("queries.PruneExpiredSessions: %w", err)
	}
	n, _ := res.RowsAffected()
	return n, nil
}
