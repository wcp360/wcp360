// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/database/queries/session.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/database/queries/session.go
// Description: JWT session / token blocklist queries.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
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
<<<<<<< HEAD
		`INSERT INTO sessions(jti, username, role, expires_at) VALUES(?,?,?,?)`,
		jti, username, role, expiresAt.UTC().Format("2006-01-02T15:04:05Z"))
=======
		`INSERT INTO sessions (jti, username, role, expires_at) VALUES (?, ?, ?, ?)`,
		jti, username, role, expiresAt.UTC().Format(time.DateTime))
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	if err != nil {
		return fmt.Errorf("queries.RegisterSession: %w", err)
	}
	return nil
}

<<<<<<< HEAD
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
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}
