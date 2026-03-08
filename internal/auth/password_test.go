// ======================================================================
// WCP 360 | V0.1.0 | internal/auth/password_test.go
// ======================================================================

package auth

import "testing"

func TestHashAndCheck(t *testing.T) {
	hash, err := HashPassword("secret123")
	if err != nil { t.Fatal(err) }
	if hash == "" { t.Fatal("empty hash") }
	if err := CheckPasswordTimingSafe("secret123", hash); err != nil {
		t.Errorf("correct password rejected: %v", err)
	}
}

func TestCheckPassword_Wrong(t *testing.T) {
	hash, _ := HashPassword("correct")
	if err := CheckPasswordTimingSafe("wrong", hash); err == nil {
		t.Error("wrong password should be rejected")
	}
}

func TestCheckPassword_EmptyPlain(t *testing.T) {
	hash, _ := HashPassword("something")
	if err := CheckPasswordTimingSafe("", hash); err == nil {
		t.Error("empty plain should be rejected")
	}
}
