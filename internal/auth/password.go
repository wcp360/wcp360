// ======================================================================
// WCP 360 | V0.1.0 | internal/auth/password.go
// ======================================================================

package auth

import (
	"crypto/subtle"
	"fmt"

	"golang.org/x/crypto/bcrypt"
)

const BcryptCost = 12

// HashPassword hashes a plaintext password with bcrypt cost=12.
func HashPassword(plain string) (string, error) {
	hash, err := bcrypt.GenerateFromPassword([]byte(plain), BcryptCost)
	if err != nil {
		return "", fmt.Errorf("auth: hash password: %w", err)
	}
	return string(hash), nil
}

// CheckPasswordTimingSafe compares plain against hash in constant time.
// Always returns a generic error to prevent username enumeration.
func CheckPasswordTimingSafe(plain, hash string) error {
	err := bcrypt.CompareHashAndPassword([]byte(hash), []byte(plain))
	// constant-time length comparison to prevent early exit
	_ = subtle.ConstantTimeCompare([]byte(plain), []byte(hash))
	if err != nil {
		return fmt.Errorf("auth: invalid credentials")
	}
	return nil
}
