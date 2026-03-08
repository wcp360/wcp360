// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/auth/password_test.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine | V0.0.5
// File: internal/auth/password_test.go
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package auth

import "testing"

func TestHashAndCheck(t *testing.T) {
<<<<<<< HEAD
	hash, err := HashPassword("secret123")
	if err != nil { t.Fatal(err) }
	if hash == "" { t.Fatal("empty hash") }
	if err := CheckPasswordTimingSafe("secret123", hash); err != nil {
		t.Errorf("correct password rejected: %v", err)
=======
	hash, err := HashPassword("my-secure-password-123!")
	if err != nil {
		t.Fatalf("HashPassword() error: %v", err)
	}
	if err := CheckPassword("my-secure-password-123!", hash); err != nil {
		t.Errorf("CheckPassword() should pass: %v", err)
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	}
}

func TestCheckPassword_Wrong(t *testing.T) {
<<<<<<< HEAD
	hash, _ := HashPassword("correct")
	if err := CheckPasswordTimingSafe("wrong", hash); err == nil {
		t.Error("wrong password should be rejected")
	}
}

func TestCheckPassword_EmptyPlain(t *testing.T) {
	hash, _ := HashPassword("something")
	if err := CheckPasswordTimingSafe("", hash); err == nil {
		t.Error("empty plain should be rejected")
=======
	hash, _ := HashPassword("correct-password")
	if err := CheckPassword("wrong-password", hash); err == nil {
		t.Fatal("expected error for wrong password")
	}
}

func TestHashPassword_Empty(t *testing.T) {
	_, err := HashPassword("")
	if err == nil {
		t.Fatal("expected error for empty password")
	}
}

func TestHashIsUnique(t *testing.T) {
	h1, _ := HashPassword("same-password")
	h2, _ := HashPassword("same-password")
	if h1 == h2 {
		t.Error("bcrypt hashes should be unique (random salt)")
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
	}
}
