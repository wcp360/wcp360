// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine | V0.0.5
// File: internal/auth/password_test.go
// ======================================================================

package auth

import "testing"

func TestHashAndCheck(t *testing.T) {
	hash, err := HashPassword("my-secure-password-123!")
	if err != nil {
		t.Fatalf("HashPassword() error: %v", err)
	}
	if err := CheckPassword("my-secure-password-123!", hash); err != nil {
		t.Errorf("CheckPassword() should pass: %v", err)
	}
}

func TestCheckPassword_Wrong(t *testing.T) {
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
	}
}
