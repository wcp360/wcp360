// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/auth/password.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// Website: https://www.wcp360.com
// File: internal/auth/password.go
// Description: Bcrypt password hashing (cost 12) and timing-safe verification.
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package auth

import (
<<<<<<< HEAD
	"crypto/subtle"
=======
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	"fmt"

	"golang.org/x/crypto/bcrypt"
)

<<<<<<< HEAD
const BcryptCost = 12

// HashPassword hashes a plaintext password with bcrypt cost=12.
func HashPassword(plain string) (string, error) {
	hash, err := bcrypt.GenerateFromPassword([]byte(plain), BcryptCost)
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	if err != nil {
		return "", fmt.Errorf("auth: hash password: %w", err)
	}
	return string(hash), nil
}

<<<<<<< HEAD
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
=======
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
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
