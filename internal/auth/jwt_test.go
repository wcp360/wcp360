// ======================================================================
<<<<<<< HEAD
// WCP 360 | V0.1.0 | internal/auth/jwt_test.go
=======
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// File: internal/auth/jwt_test.go
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
// ======================================================================

package auth

import (
	"strings"
	"testing"
	"time"
)

<<<<<<< HEAD
const testSecret = "this-is-a-test-secret-32-characters!!"

func TestGenerateAndParseToken(t *testing.T) {
	tok, jti, exp, err := GenerateToken("alice", RoleRoot, testSecret)
	if err != nil { t.Fatal(err) }
	if tok == "" { t.Error("empty token") }
	if jti == "" { t.Error("empty jti") }
	if exp.Before(time.Now()) { t.Error("expiry in the past") }

	claims, err := ParseToken(tok, testSecret)
	if err != nil { t.Fatal(err) }
	if claims.Username != "alice" { t.Errorf("username = %q", claims.Username) }
	if claims.Role != RoleRoot { t.Errorf("role = %q", claims.Role) }
	if claims.ID != jti { t.Errorf("jti mismatch") }
}

func TestParseToken_WrongSecret(t *testing.T) {
	tok, _, _, _ := GenerateToken("bob", RoleRoot, testSecret)
	_, err := ParseToken(tok, "wrong-secret-wrong-secret-wrong!!")
	if err == nil { t.Error("expected error with wrong secret") }
}

func TestParseToken_Malformed(t *testing.T) {
	_, err := ParseToken("not.a.token", testSecret)
	if err == nil { t.Error("expected error for malformed token") }
}

func TestGenerateToken_UniqueJTIs(t *testing.T) {
	_, jti1, _, _ := GenerateToken("u", RoleRoot, testSecret)
	_, jti2, _, _ := GenerateToken("u", RoleRoot, testSecret)
	if jti1 == jti2 { t.Error("JTIs must be unique") }
}

func TestGenerateToken_ShortSecret(t *testing.T) {
	// jwt library accepts any length secret — we just ensure it doesn't panic
	tok, _, _, err := GenerateToken("u", RoleRoot, "short")
	if err != nil { t.Fatal(err) }
	if !strings.Contains(tok, ".") { t.Error("not a JWT") }
=======
const testSecret = "test-secret-must-be-at-least-32-chars-long-00"

func TestGenerateAndValidate(t *testing.T) {
	token, _, expiresAt, err := GenerateToken("admin", RoleRoot, testSecret)
	if err != nil {
		t.Fatalf("GenerateToken() unexpected error: %v", err)
	}
	if token == "" {
		t.Fatal("GenerateToken() returned empty token")
	}
	if expiresAt.Before(time.Now()) {
		t.Error("GenerateToken() returned an already-expired time")
	}
	claims, err := ValidateToken(token, testSecret)
	if err != nil {
		t.Fatalf("ValidateToken() unexpected error: %v", err)
	}
	if claims.Username != "admin" {
		t.Errorf("expected username admin, got %q", claims.Username)
	}
	if claims.Role != RoleRoot {
		t.Errorf("expected role root, got %q", claims.Role)
	}
}

func TestValidate_WrongSecret(t *testing.T) {
	token, _, _, _ := GenerateToken("admin", RoleRoot, testSecret)
	_, err := ValidateToken(token, "completely-different-secret-which-is-wrong-xx")
	if err == nil {
		t.Fatal("expected error for wrong secret")
	}
}

func TestValidate_TamperedPayload(t *testing.T) {
	token, _, _, _ := GenerateToken("admin", RoleRoot, testSecret)
	parts := strings.Split(token, ".")
	parts[1] = parts[1] + "tampered"
	_, err := ValidateToken(strings.Join(parts, "."), testSecret)
	if err == nil {
		t.Fatal("expected error for tampered token")
	}
}

func TestGenerate_EmptyUsername(t *testing.T) {
	_, _, _, err := GenerateToken("", RoleRoot, testSecret)
	if err == nil {
		t.Fatal("expected error for empty username")
	}
>>>>>>> 73460c3d7e41f737a10e5a15c51d744bfadf5dee
}
