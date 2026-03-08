// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/auth/password.go
// Description: Bcrypt password hashing (cost 12) and timing-safe verification.
// ======================================================================

package auth

import (
	"fmt"

	"golang.org/x/crypto/bcrypt"
)

const bcryptCost = 12

var dummyHash []byte

func init() {
	h, err := bcrypt.GenerateFromPassword([]byte("__wcp360_dummy_password__"), bcryptCost)
	if err != nil {
		panic(fmt.Sprintf("auth: failed to initialize dummy hash: %v", err))
	}
	dummyHash = h
}

func HashPassword(plain string) (string, error) {
	if plain == "" {
		return "", fmt.Errorf("auth: password must not be empty")
	}
	hash, err := bcrypt.GenerateFromPassword([]byte(plain), bcryptCost)
	if err != nil {
		return "", fmt.Errorf("auth: hash password: %w", err)
	}
	return string(hash), nil
}

func CheckPassword(plain, hash string) error {
	if err := bcrypt.CompareHashAndPassword([]byte(hash), []byte(plain)); err != nil {
		return fmt.Errorf("auth: invalid password")
	}
	return nil
}

// CheckPasswordTimingSafe runs bcrypt against a dummy hash when the
// real hash is not available, preventing timing-based username enumeration.
func CheckPasswordTimingSafe(plain string) error {
	_ = bcrypt.CompareHashAndPassword(dummyHash, []byte(plain))
	return fmt.Errorf("auth: invalid credentials")
}
