// ======================================================================
// WCP 360 – Modern Web Control Panel (Go + Caddy + FrankenPHP)
// ======================================================================
// Creator: HADJ RAMDANE Yacine
// Contact: yacine@wcp360.com
// Version: V0.0.5
// File: internal/auth/jwt_test.go
// ======================================================================

package auth

import (
	"strings"
	"testing"
	"time"
)

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
}
