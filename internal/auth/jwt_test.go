// ======================================================================
// WCP 360 | V0.1.0 | internal/auth/jwt_test.go
// ======================================================================

package auth

import (
	"strings"
	"testing"
	"time"
)

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
}
