// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/auth/jwt.go
// Description: JWT token generation and validation (HS256, 24h TTL, JTI).
// ======================================================================

package auth

import (
	"crypto/rand"
	"encoding/hex"
	"errors"
	"fmt"
	"time"

	"github.com/golang-jwt/jwt/v5"
)

const TokenDuration = 24 * time.Hour

type Role string

const (
	RoleRoot     Role = "root"
	RoleAdmin    Role = "admin"
	RoleReseller Role = "reseller"
	RoleTenant   Role = "tenant"
)

type Claims struct {
	Username string `json:"username"`
	Role     Role   `json:"role"`
	jwt.RegisteredClaims
}

// GenerateToken creates a signed JWT with a unique JTI.
// Returns token string, JTI (for session registration), expiry time.
func GenerateToken(username string, role Role, secret string) (tokenStr string, jti string, expiresAt time.Time, err error) {
	if secret == "" {
		return "", "", time.Time{}, fmt.Errorf("auth: jwt_secret is not configured")
	}
	if username == "" {
		return "", "", time.Time{}, fmt.Errorf("auth: username must not be empty")
	}

	jti, err = generateJTI()
	if err != nil {
		return "", "", time.Time{}, fmt.Errorf("auth: generate JTI: %w", err)
	}

	now := time.Now()
	expiresAt = now.Add(TokenDuration)

	claims := Claims{
		Username: username,
		Role:     role,
		RegisteredClaims: jwt.RegisteredClaims{
			ID:        jti,
			Subject:   username,
			Issuer:    "wcp360",
			IssuedAt:  jwt.NewNumericDate(now),
			ExpiresAt: jwt.NewNumericDate(expiresAt),
		},
	}

	token := jwt.NewWithClaims(jwt.SigningMethodHS256, claims)
	tokenStr, err = token.SignedString([]byte(secret))
	if err != nil {
		return "", "", time.Time{}, fmt.Errorf("auth: sign token: %w", err)
	}
	return tokenStr, jti, expiresAt, nil
}

// ValidateToken parses and validates a JWT. Returns Claims on success.
func ValidateToken(tokenStr, secret string) (*Claims, error) {
	if secret == "" {
		return nil, fmt.Errorf("auth: jwt_secret is not configured")
	}
	token, err := jwt.ParseWithClaims(tokenStr, &Claims{},
		func(t *jwt.Token) (any, error) {
			if _, ok := t.Method.(*jwt.SigningMethodHMAC); !ok {
				return nil, fmt.Errorf("auth: unexpected algorithm: %v", t.Header["alg"])
			}
			return []byte(secret), nil
		},
	)
	if err != nil {
		if errors.Is(err, jwt.ErrTokenExpired) {
			return nil, fmt.Errorf("auth: token has expired")
		}
		return nil, fmt.Errorf("auth: invalid token: %w", err)
	}
	claims, ok := token.Claims.(*Claims)
	if !ok || !token.Valid {
		return nil, fmt.Errorf("auth: invalid token claims")
	}
	return claims, nil
}

func generateJTI() (string, error) {
	b := make([]byte, 16)
	if _, err := rand.Read(b); err != nil {
		return "", err
	}
	return hex.EncodeToString(b), nil
}
